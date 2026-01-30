<?php // Code within app\Helpers\Helper.php

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;
use App\Models\Translation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class Helper
{

    public static function insertLanguage(
        string $modelClass,
        int $modelId,
        string $language,
        string $field,
        string $value
    ) {
        Translation::updateOrCreate(
            [
                'translatable_type' => $modelClass,
                'translatable_id'   => $modelId,
                'language_code'     => $language,
                'field'             => $field,
            ],
            [
                'value' => $value,
            ]
        );
    }


    public static function sendEmail($email, $subject, $data, $template = 'default')
    {
        Mail::send('mails.' . $template, ['data' => $data], function ($message) use ($email, $subject) {
            $message->from(env('MAIL_FROM_ADDRESS'), $subject);
            $message->to($email)->subject($subject);
        });
    }
}
