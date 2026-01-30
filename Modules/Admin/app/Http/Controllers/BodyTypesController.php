<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Helper;
use Yajra\DataTables\DataTables;
use App\Models\BodyType;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Translation;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class BodyTypesController extends Controller
{
    public function index()
    {
        // $bodytypes = BodyType::whereNull('deleted_at')->orderBy('id', 'desc')->get();
        $bodytypes = Translation::where('translatable_type', BodyType::class)->where('field', 'body_type')->get();
        return view('admin::bodytype.index', compact('bodytypes'));
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
        $bodytype = BodyType::create($data);
        Helper::insertLanguage(BodyType::class, $bodytype->id, Session::get('admin_language') ?? 'en', 'body_type', $bodytype->body_type);

        return response()->json([
            'type' => 'success',
            'return' => $bodytype,
            'status' => 1,
            'message' => 'bodytype added Successfully !',
        ], 200);
    }
    public function show(string $id)
    {
        // return view('backend.bodytype.edit');
        echo 'ddd';
        exit;
    }
    public function edit(string $id)
    {
        $bodytype = BodyType::find($id);
        return view('admin::bodytype.edit', compact('bodytype'));
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
        $bodytype = BodyType::findOrFail($id);
        $data['body_type'] = $request->body_type;
        $bodytype->update($data);

        Helper::insertLanguage(BodyType::class, $bodytype->id, Session::get('admin_language') ?? 'en', 'body_type', $request->body_type);
        return response()->json([
            'type' => 'success',
            'status' => 1,
            'message' => 'bodytype updated successfully!',
        ], 200);
    }


    public function destroy(string $id)
    {
        $bodytype = BodyType::findOrFail($id);
        if ($bodytype) {
            $bodytype->deleted_at = now();
            $bodytype->save();

            Translation::where('translatable_type', BodyType::class)->where('field', 'body_type')->where('translatable_id', $bodytype->id)->delete();

            return response()->json([
                'type' => 'success',
                'status' => 1,
                'message' => 'bodytype deleted successfully!',
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'status' => 0,
                'message' => 'bodytype not found',
            ], 200);
        }
    }
    protected function Validation($request)
    {
        return Validator::make($request->except(['_token', '_method']), [
            'body_type'    => 'required|string|max:255',
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
