<form id="EditForm" method="POST" enctype="multipart/form-data"
    action="{{ route('admin.custom-options.update', $customoption->id) }}">
    @csrf
    @method('PUT')
    <div class="modal-header" style="display: block;">
        <h4 class="modal-title">Edit</h4>
    </div>

    <div class="modal-body">
        <div class="server_side_error"></div>
        <div class="row">
            <div class="col-md-12">
                {{-- <div class="form-group">
                    <label>Parent</label>
                    <select name="parent_id" id="cutom_parent" class="form-control">
                        <option value="0">--Select Parent--</option>
                        @foreach ($selectoptions as $option)
                            <option @if ($option->id == $customoption->id) selected @endif value="{{ $option->id }}">
                                {{ $option->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group name_block">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ $customoption->name }}"
                        placeholder="Enter title" required>
                </div> --}}
                <div class="form-group">
                    <label>Value</label>
                    <input type="text" class="form-control" name="value" value="{{ $customoption->value }}"
                        placeholder="Enter title" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ $customoption->status == 1 ? 'selected' : '' }}>
                            Active</option>
                        <option value="0" {{ $customoption->status == 0 ? 'selected' : '' }}>
                            Inactive</option>
                    </select>
                </div>
            </div>
        </div>
    </div>


    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="EditFormSubmitBtn" class="btn btn-sm btn-primary">Update</button>
    </div>
</form>
