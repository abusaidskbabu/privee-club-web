<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Helper;
use Yajra\DataTables\DataTables;
use App\Models\Zodiac;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class ZodiacSignController extends Controller
{
    public function index()
    {
        $zodiacsigns = Zodiac::whereNull('deleted_at')->orderBy('id', 'desc')->get();
        return view('admin::zodiacsign.index', compact('zodiacsigns'));
    }


    public function create()
    {
        //
    }
    public function store(Request $request)
    {
        $validator = $this->Validation($request);
        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $request->all();
        $data = $request->except(['_token']);
        $zodiacsign = Zodiac::create($data);
        Helper::insertLanguage(Zodiac::class, $zodiacsign->id, Session::get('admin_language') ?? 'en', 'Zodiac_Signs', $zodiacsign->Zodiac_Signs);

        return response()->json([
            'type' => 'success',
            'return' => $zodiacsign,
            'status' => 1,
            'message' => 'zodiacsign added Successfully !',
        ], 200);
    }
    public function show(string $id)
    {
        // return view('backend.zodiacsign.edit');
        echo 'ddd';
        exit;
    }
    public function edit(string $id)
    {
        $zodiacsign = Zodiac::find($id);
        return view('admin::zodiacsign.edit', compact('zodiacsign'));
    }
    public function update(Request $request, string $id)
    {
        $validator = $this->Validation($request);

        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $zodiacsign = Zodiac::findOrFail($id);
        $data['Zodiac_Signs'] = $request->Zodiac_Signs;
        $zodiacsign->update($data);

        Helper::insertLanguage(Zodiac::class, $zodiacsign->id, Session::get('admin_language') ?? 'en', 'Zodiac_Signs', $request->Zodiac_Signs);
        return response()->json([
            'type' => 'success',
            'status' => 1,
            'message' => 'zodiacsign updated successfully!',
        ], 200);
    }


    public function destroy(string $id)
    {
        $zodiacsign = Zodiac::findOrFail($id);
        if ($zodiacsign) {
            $zodiacsign->deleted_at = now();
            $zodiacsign->save();

            return response()->json([
                'type' => 'success',
                'status' => 1,
                'message' => 'zodiacsign deleted successfully!',
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'status' => 0,
                'message' => 'zodiacsign not found',
            ], 200);
        }
    }
    protected function Validation($request)
    {
        return Validator::make($request->except(['_token', '_method']), [
            'Zodiac_Signs'    => 'required|string|max:255',
        ]);
        return $validator;
    }
    protected function fileUpload($request, $file_name, $folder)
    {
        if (!$request->hasFile($file_name)) {
            return null;
        }

        $file = $request->file($file_name);
        $extension = strtolower($file->getClientOriginalExtension());

        // UNIQUE filename (super safe)
        $filename = uniqid() . '_' . time() . '.' . $extension;

        // Make folder if not exists
        if (!file_exists(public_path($folder))) {
            mkdir(public_path($folder), 0777, true);
        }

        $full_path = public_path($folder . $filename);

        // Check if image
        $image_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        // Check if video
        $video_extensions = ['mp4', 'mov', 'avi', 'webm', 'mkv'];

        if (in_array($extension, $image_extensions)) {
            // image processing
            Image::read($file)
                // ->resize(800, 800)
                ->save($full_path);
        } elseif (in_array($extension, $video_extensions)) {
            // store video normally
            $file->move(public_path($folder), $filename);
        } else {
            return null; // unsupported file
        }

        return $folder . $filename;  // return path
    }
}
