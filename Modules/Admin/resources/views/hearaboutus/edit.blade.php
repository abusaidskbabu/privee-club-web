<form id="EditForm" method="POST" enctype="multipart/form-data"
    action="{{ route('admin.hear-about-us.update', $hearaboutus->id) }}">
    @csrf
    @method('PUT')
    <div class="modal-header" style="display: block;">
        <h4 class="modal-title">Edit</h4>
    </div>


    <div class="modal-body">
        <div class="server_side_error"></div>
        <div class="form-group">
            <label>Select Language</label>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <form action="{{ url('/admin/change-language') }}" method="POST">
                        @csrf
                        <select class="form-control form-control-sm language-select">
                            <option value="da" {{ Session::get('admin_language') == 'da' ? 'selected' : '' }}>Dansk
                            </option>
                            <option value="en" {{ Session::get('admin_language') == 'en' ? 'selected' : '' }}>
                                English
                            </option>
                        </select>
                    </form>
                </li>
            </ul>
        </div>
        <div class="form-group name_block">
            <label>Hear About Us</label>
            <input type="text" class="form-control" name="platform" value="{{ $hearaboutus->platform }}"
                placeholder="Enter Hear About Us..">
        </div>
    </div>


    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" id="EditFormSubmitBtn" class="btn btn-sm btn-primary">Update</button>
    </div>
</form>
