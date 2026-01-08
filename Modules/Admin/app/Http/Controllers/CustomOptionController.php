<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Hash;
use Helper;
use App\Models\CustomOption;
use Yajra\DataTables\DataTables;
use App\Models\LeadingAndGovernor;

use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;

class CustomOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customoptions = CustomOption::whereNull('deleted_at')->orderBy('id', 'desc')->get();
        $selectoptions = CustomOption::whereNull('deleted_at')->whereNull('parent_id')->orderBy('id', 'desc')->get();
        return view('admin::customoption.index', compact('customoptions', 'selectoptions'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = $this->Validation($request);
        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        if ($request->parent_id < 1 && !$request->name) {
            return response()->json([
                'type' => 'error',
                'status' => 0,
                'message' => 'Please select parent or type name.',
            ], 200);
        }


        // $data = $request->except(['video', 'image']);
        $data = $request->all();
        $data['slug'] = $this->uniqueSlug($request->value);
        if ($request->parent_id > 0) {
            $option = CustomOption::find($request->parent_id);
            $data['name'] = $option?->name;
        } else {
            $data['parent_id'] = Null;
        }
        $data['status'] = 1;
        $customoption = CustomOption::create($data);
        return response()->json([
            'type' => 'success',
            'return' => $customoption,
            'status' => 1,
            'message' => 'CustomOption added Successfully !',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // return view('backend.customoption.edit');
        echo 'ddd';
        exit;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customoption = CustomOption::find($id);
        $selectoptions = CustomOption::whereNull('deleted_at')->whereNull('parent_id')->orderBy('id', 'desc')->get();
        return view('admin::customoption.edit', compact('customoption', 'selectoptions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = $this->Validation($request);
        if ($validator->fails()) {
            return response()->json([
                'type' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        $customoption = CustomOption::findOrFail($id);
        $data['value'] = $request->value;
        $data['status'] = $request->status;
        $customoption->update($data);
        return response()->json([
            'type' => 'success',
            'status' => 1,
            'message' => 'CustomOption updated successfully!',
        ], 200);
    }


    public function destroy(string $id)
    {
        $customoption = CustomOption::findOrFail($id);
        if ($customoption) {
            $customoption->deleted_at = now();
            $customoption->save();

            return response()->json([
                'type' => 'success',
                'status' => 1,
                'message' => 'CustomOption deleted successfully!',
            ], 200);
        } else {
            return response()->json([
                'type' => 'error',
                'status' => 0,
                'message' => 'CustomOption not found',
            ], 200);
        }
    }
    protected function Validation($request)
    {
        return Validator::make($request->except(['_token', '_method']), [
            //'name'          => 'required|string|max:255',
            'value'          => 'required|string|max:255',
        ]);
        return $validator;
    }
    private function uniqueSlug($slug)
    {
        $originalSlug = $slug;
        $count = 1;
        while (CustomOption::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        return $slug;
    }
}
