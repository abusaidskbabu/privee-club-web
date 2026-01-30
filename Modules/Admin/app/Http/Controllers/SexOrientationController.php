<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Helper;
use Yajra\DataTables\DataTables;
use App\Models\SexOrientation;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class SexOrientationController extends Controller
{
    public function index()
    {
        $sexorientations = SexOrientation::whereNull('deleted_at')->orderBy('id', 'desc')->get();
        return view('admin::sexorientation.index', compact('sexorientations'));
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
        $sexorientation = SexOrientation::create($data);
        Helper::insertLanguage(SexOrientation::class, $sexorientation->id, Session::get('admin_language') ?? 'en', 'sex_orientation', $sexorientation->sex_orientation);

        return response()->json([
            'type' => 'success',
            'return' => $sexorientation,
            'status' => 1,
            'message' => 'sexorientation added Successfully !',
        ], 200);
    }
    public function show(string $id)
    {
        // return view('backend.sexorientation.edit');
        echo 'ddd';
        exit;
    }
    public function edit(string $id)
    {
        $sexorientation = SexOrientation::find($id);
        return view('admin::sexorientation.edit', compact('sexorientation'));
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
        $sexorientation = SexOrientation::findOrFail($id);
        $data['sex_orientation'] = $request->sex_orientation;
        $sexorientation->update($data);

        Helper::insertLanguage(SexOrientation::class, $sexorientation->id, Session::get('admin_language') ?? 'en', 'sex_orientation', $request->sex_orientation);
        return response()->json([
            'type' => 'success',
            'status' => 1,
            'message' => 'sexorientation updated successfully!',
        ], 200);
    }


    public function destroy(string $id)
    {
        $sexorientation = SexOrientation::findOrFail($id);
        if ($sexorientation) {
            $sexorientation->deleted_at = now();
            $sexorientation->save();

            return response()->json([
                'type' => 'success',
                'status' => 1,
                'message' => 'sexorientation deleted successfully!',
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'status' => 0,
                'message' => 'sexorientation not found',
            ], 200);
        }
    }
    protected function Validation($request)
    {
        return Validator::make($request->except(['_token', '_method']), [
            'sex_orientation'    => 'required|string|max:255',
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
