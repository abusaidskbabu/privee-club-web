<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateMigrationsFromSql extends Command
{
    protected $signature = 'migrate:from-sql {sql_file}';
    protected $description = 'Generate migration files from SQL dump file';

    protected $migrationFiles = [];

    public function handle()
    {
        $sqlFile = $this->argument('sql_file');

        if (!file_exists($sqlFile)) {
            $this->error("SQL file not found: {$sqlFile}");
            return 1;
        }

        $this->info("Reading SQL file: {$sqlFile}");
        $sqlContent = file_get_contents($sqlFile);

        // Extract all CREATE TABLE statements
        $tables = $this->extractTables($sqlContent);

        $this->info("Found " . count($tables) . " tables");

        // Generate migration files
        foreach ($tables as $tableName => $tableInfo) {
            $this->generateMigration($tableName, $tableInfo);
        }

        // Update migrations table
        $this->updateMigrationsTable();

        $this->info("Migration files generated successfully!");
        return 0;
    }

    protected function extractTables($sqlContent)
    {
        $tables = [];

        // Match CREATE TABLE statements - handle multi-line and nested parentheses
        $pattern = '/CREATE\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?`?(\w+)`?\s*\((.*?)\)\s*ENGINE/is';
        preg_match_all($pattern, $sqlContent, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $tableName = $match[1];
            $tableBody = $match[2];

            // Extract columns
            $columns = $this->parseColumns($tableBody);

            // Extract indexes and keys (from ALTER TABLE statements)
            $indexes = $this->extractIndexes($sqlContent, $tableName);

            // Check for AUTO_INCREMENT in MODIFY statements
            $this->checkAutoIncrement($sqlContent, $tableName, $columns);

            $tables[$tableName] = [
                'columns' => $columns,
                'indexes' => $indexes,
            ];
        }

        return $tables;
    }

    protected function parseColumns($tableBody)
    {
        $columns = [];

        // Remove comments
        $tableBody = preg_replace('/COMMENT\s+[\'"][^\'"]*[\'"]/i', '', $tableBody);

        // Split by comma, but be careful with nested parentheses and quotes
        $lines = [];
        $current = '';
        $depth = 0;
        $inQuotes = false;
        $quoteChar = '';

        for ($i = 0; $i < strlen($tableBody); $i++) {
            $char = $tableBody[$i];

            if (($char === '"' || $char === "'") && ($i === 0 || $tableBody[$i - 1] !== '\\')) {
                if (!$inQuotes) {
                    $inQuotes = true;
                    $quoteChar = $char;
                } elseif ($char === $quoteChar) {
                    $inQuotes = false;
                    $quoteChar = '';
                }
            } elseif (!$inQuotes) {
                if ($char === '(') {
                    $depth++;
                } elseif ($char === ')') {
                    $depth--;
                } elseif ($char === ',' && $depth === 0) {
                    $lines[] = trim($current);
                    $current = '';
                    continue;
                }
            }

            $current .= $char;
        }

        if (!empty(trim($current))) {
            $lines[] = trim($current);
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line) || preg_match('/^(PRIMARY|KEY|INDEX|UNIQUE|FOREIGN|CONSTRAINT)/i', $line)) {
                continue;
            }

            // Extract column definition - handle backticks and regular names
            if (preg_match('/`?([a-zA-Z_][a-zA-Z0-9_]*)`?\s+(.+)/', $line, $colMatch)) {
                $columnName = $colMatch[1];
                $columnDef = trim($colMatch[2]);

                // Remove trailing comma if present
                $columnDef = rtrim($columnDef, ',');

                $columns[$columnName] = $this->parseColumnDefinition($columnDef);
            }
        }

        return $columns;
    }

    protected function parseColumnDefinition($def)
    {
        $column = [
            'type' => 'string',
            'nullable' => false,
            'default' => null,
            'unsigned' => false,
            'auto_increment' => false,
        ];

        // Check for NOT NULL first
        $isNotNull = preg_match('/\bNOT\s+NULL\b/i', $def);

        // Check for unsigned
        if (preg_match('/\bUNSIGNED\b/i', $def)) {
            $column['unsigned'] = true;
        }

        // Check for auto increment
        if (preg_match('/\bAUTO_INCREMENT\b/i', $def)) {
            $column['auto_increment'] = true;
            // Auto increment columns are typically NOT NULL
            $isNotNull = true;
        }

        // Set nullable based on NOT NULL
        if ($isNotNull) {
            $column['nullable'] = false;
        }

        // Check for nullable - but id columns that are NOT NULL should not be nullable
        if (preg_match('/\bDEFAULT\s+NULL\b/i', $def) || (preg_match('/\bNULL\b/i', $def) && !preg_match('/\bNOT\s+NULL\b/i', $def))) {
            $column['nullable'] = true;
        }

        // Extract default value
        if (preg_match("/DEFAULT\s+([^,\s]+(?:\s+ON\s+UPDATE\s+[^,\s]+)?)/i", $def, $defaultMatch)) {
            $default = trim($defaultMatch[1]);
            // Remove ON UPDATE clause if present
            $default = preg_replace('/\s+ON\s+UPDATE\s+.*$/i', '', $default);
            $default = trim($default, "'\"");

            if (
                strtolower($default) !== 'null' &&
                strtolower($default) !== 'current_timestamp' &&
                strtolower($default) !== 'current_timestamp()'
            ) {
                $column['default'] = $default;
            } elseif (strtolower($default) === 'current_timestamp' || strtolower($default) === 'current_timestamp()') {
                $column['useCurrent'] = true;
            }
        }

        // Determine type
        if (preg_match('/\b(bigint|int|tinyint|smallint|mediumint)\b/i', $def, $typeMatch)) {
            $intType = strtolower($typeMatch[1]);
            if ($intType === 'bigint') {
                $column['type'] = 'bigInteger';
            } elseif ($intType === 'tinyint') {
                $column['type'] = 'tinyInteger';
            } elseif ($intType === 'smallint') {
                $column['type'] = 'smallInteger';
            } else {
                $column['type'] = 'integer';
            }
        } elseif (preg_match('/\bvarchar\((\d+)\)\b/i', $def, $varcharMatch)) {
            $length = $varcharMatch[1];
            $column['type'] = 'string';
            $column['length'] = $length;
        } elseif (preg_match('/\bchar\((\d+)\)\b/i', $def, $charMatch)) {
            $column['type'] = 'char';
            $column['length'] = $charMatch[1];
        } elseif (preg_match('/\btext\b/i', $def)) {
            $column['type'] = 'text';
        } elseif (preg_match('/\blongtext\b/i', $def)) {
            $column['type'] = 'longText';
        } elseif (preg_match('/\bmediumtext\b/i', $def)) {
            $column['type'] = 'mediumText';
        } elseif (preg_match('/\btinytext\b/i', $def)) {
            $column['type'] = 'tinyText';
        } elseif (preg_match('/\bdate\b/i', $def)) {
            $column['type'] = 'date';
        } elseif (preg_match('/\bdatetime\b/i', $def)) {
            $column['type'] = 'dateTime';
        } elseif (preg_match('/\btimestamp\b/i', $def)) {
            $column['type'] = 'timestamp';
        } elseif (preg_match('/\btime\b/i', $def)) {
            $column['type'] = 'time';
        } elseif (preg_match('/\bdecimal\((\d+),(\d+)\)\b/i', $def, $decimalMatch)) {
            $column['type'] = 'decimal';
            $column['precision'] = $decimalMatch[1];
            $column['scale'] = $decimalMatch[2];
        } elseif (preg_match('/\bfloat\b/i', $def)) {
            $column['type'] = 'float';
        } elseif (preg_match('/\bdouble\b/i', $def)) {
            $column['type'] = 'double';
        } elseif (preg_match('/\bboolean\b/i', $def) || preg_match('/\bbool\b/i', $def)) {
            $column['type'] = 'boolean';
        } elseif (preg_match('/\bjson\b/i', $def)) {
            $column['type'] = 'json';
        }

        return $column;
    }

    protected function extractIndexes($sqlContent, $tableName)
    {
        $indexes = [];

        // Look for ALTER TABLE statements for this table
        $pattern = "/ALTER TABLE\s+`?{$tableName}`?\s+(.*?);/is";
        preg_match_all($pattern, $sqlContent, $alterMatches);

        foreach ($alterMatches[1] ?? [] as $alterBody) {
            // Primary key - can have multiple columns
            if (preg_match("/ADD\s+PRIMARY\s+KEY\s+\(([^)]+)\)/i", $alterBody, $pkMatch)) {
                $cols = $this->extractColumnNames($pkMatch[1]);
                $indexes[] = ['type' => 'primary', 'columns' => $cols];
            }

            // Unique key
            if (preg_match("/ADD\s+UNIQUE\s+KEY\s+`?(\w+)`?\s+\(([^)]+)\)/i", $alterBody, $ukMatch)) {
                $cols = $this->extractColumnNames($ukMatch[2]);
                $indexes[] = ['type' => 'unique', 'name' => $ukMatch[1], 'columns' => $cols];
            }

            // Regular index
            if (preg_match("/ADD\s+KEY\s+`?(\w+)`?\s+\(([^)]+)\)/i", $alterBody, $idxMatch)) {
                $cols = $this->extractColumnNames($idxMatch[2]);
                $indexes[] = ['type' => 'index', 'name' => $idxMatch[1], 'columns' => $cols];
            }
        }

        return $indexes;
    }

    protected function extractColumnNames($columnList)
    {
        $columns = [];
        preg_match_all('/`?(\w+)`?/', $columnList, $matches);
        return $matches[1] ?? [];
    }

    protected function checkAutoIncrement($sqlContent, $tableName, &$columns)
    {
        // Look for MODIFY statements that set AUTO_INCREMENT
        $pattern = "/ALTER TABLE\s+`?{$tableName}`?\s+MODIFY\s+`?(\w+)`?\s+.*?AUTO_INCREMENT/is";
        if (preg_match($pattern, $sqlContent, $matches)) {
            $columnName = $matches[1];
            if (isset($columns[$columnName])) {
                $columns[$columnName]['auto_increment'] = true;
            }
        }
    }

    protected function generateMigration($tableName, $tableInfo)
    {
        $timestamp = now()->format('Y_m_d_His');
        $className = 'Create' . Str::studly(Str::singular($tableName)) . 'Table';
        $fileName = "{$timestamp}_create_" . Str::snake(Str::plural($tableName)) . "_table.php";
        $filePath = database_path("migrations/{$fileName}");

        $columnsCode = $this->generateColumnsCode($tableInfo['columns'], $tableInfo['indexes']);

        $migrationContent = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
{$columnsCode}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        File::put($filePath, $migrationContent);
        $this->migrationFiles[] = $fileName;
        $this->info("Generated: {$fileName}");
    }

    protected function generateColumnsCode($columns, $indexes)
    {
        $code = [];
        $hasId = false;
        $hasTimestamps = false;

        // Check for timestamps first
        $hasCreatedAt = isset($columns['created_at']);
        $hasUpdatedAt = isset($columns['updated_at']);
        if ($hasCreatedAt && $hasUpdatedAt) {
            $hasTimestamps = true;
        }

        foreach ($columns as $name => $column) {
            // Handle id column - check if it's auto_increment and unsigned bigint
            if ($name === 'id') {
                // Check if it's a standard Laravel id (bigint unsigned auto_increment)
                if (
                    $column['auto_increment'] &&
                    ($column['type'] === 'bigInteger' || $column['type'] === 'integer') &&
                    $column['unsigned']
                ) {
                    $code[] = "            \$table->id();";
                    $hasId = true;
                    continue;
                }
            }

            // Handle timestamps together
            if ($name === 'created_at' && $hasTimestamps) {
                $code[] = "            \$table->timestamps();";
                continue;
            }

            // Skip updated_at if we already added timestamps()
            if ($name === 'updated_at' && $hasTimestamps) {
                continue;
            }

            $line = $this->generateColumnLine($name, $column);
            if ($line) {
                $code[] = $line;
            }
        }

        // Add indexes (skip primary key as it's handled by id() or primary key definition)
        foreach ($indexes as $index) {
            if ($index['type'] === 'primary') {
                // Only add primary key if id() wasn't used
                if (!$hasId) {
                    // Handle composite primary keys
                    if (count($index['columns']) === 1) {
                        $code[] = "            \$table->primary('{$index['columns'][0]}');";
                    } else {
                        $cols = "'" . implode("', '", $index['columns']) . "'";
                        $code[] = "            \$table->primary([{$cols}]);";
                    }
                }
                // If hasId is true, id() already handles the primary key, so skip
            } elseif ($index['type'] === 'unique') {
                if (count($index['columns']) === 1) {
                    $code[] = "            \$table->unique('{$index['columns'][0]}', '{$index['name']}');";
                } else {
                    $cols = "'" . implode("', '", $index['columns']) . "'";
                    $code[] = "            \$table->unique([{$cols}], '{$index['name']}');";
                }
            } elseif ($index['type'] === 'index' && isset($index['name'])) {
                if (count($index['columns']) === 1) {
                    $code[] = "            \$table->index('{$index['columns'][0]}', '{$index['name']}');";
                } else {
                    $cols = "'" . implode("', '", $index['columns']) . "'";
                    $code[] = "            \$table->index([{$cols}], '{$index['name']}');";
                }
            }
        }

        return implode("\n", $code);
    }

    protected function generateColumnLine($name, $column)
    {
        $method = $column['type'];
        $params = [];

        // Add length for string/char
        if (in_array($method, ['string', 'char']) && isset($column['length'])) {
            $params[] = $column['length'];
        }

        // Add precision/scale for decimal
        if ($method === 'decimal' && isset($column['precision'])) {
            $params[] = $column['precision'];
            $params[] = $column['scale'] ?? 0;
        }

        $paramStr = !empty($params) ? ', ' . implode(', ', $params) : '';
        $line = "            \$table->{$method}('{$name}'{$paramStr})";

        // Add modifiers
        if ($column['unsigned']) {
            $line .= "->unsigned()";
        }

        if ($column['nullable']) {
            $line .= "->nullable()";
        }

        if (isset($column['useCurrent']) && $column['useCurrent']) {
            $line .= "->useCurrent()";
        } elseif ($column['default'] !== null) {
            // Handle numeric defaults
            if (is_numeric($column['default'])) {
                $line .= "->default({$column['default']})";
            } else {
                // Escape quotes in string defaults
                $default = str_replace("'", "\\'", $column['default']);
                $line .= "->default('{$default}')";
            }
        }

        if ($column['auto_increment']) {
            $line .= "->autoIncrement()";
        }

        $line .= ";";

        return $line;
    }

    protected function updateMigrationsTable()
    {
        $this->info("Updating migrations table...");

        try {
            foreach ($this->migrationFiles as $file) {
                $migrationName = str_replace('.php', '', $file);

                // Check if migration already exists
                $exists = DB::table('migrations')
                    ->where('migration', $migrationName)
                    ->exists();

                if (!$exists) {
                    DB::table('migrations')->insert([
                        'migration' => $migrationName,
                        'batch' => DB::table('migrations')->max('batch') + 1,
                    ]);
                    $this->info("  Added: {$migrationName}");
                } else {
                    $this->warn("  Already exists: {$migrationName}");
                }
            }
        } catch (\Exception $e) {
            $this->error("Error updating migrations table: " . $e->getMessage());
        }
    }
}
