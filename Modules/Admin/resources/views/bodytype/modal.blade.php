<!-- Modal -->

<div class="modal fade CreateModalOpen" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">

        <div class="modal-content">
            <form id="CreateForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="modal-header" style="display: block;">
                    <h4 class="modal-title">Add New</h4>
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
                                        <option value="da"
                                            {{ Session::get('admin_language') == 'da' ? 'selected' : '' }}>Dansk
                                        </option>
                                        <option value="en"
                                            {{ Session::get('admin_language') == 'en' ? 'selected' : '' }}>English
                                        </option>
                                    </select>
                                </form>
                            </li>
                        </ul>
                    </div>
                    <div class="form-group name_block">
                        <label>Body Type</label>
                        <input type="text" class="form-control" name="body_type" placeholder="Enter Body Type..">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="CreateModalSubmitBtn">Submit</button>
                </div>

            </form>
        </div>

    </div>
</div>
{{-- edit modal  --}}
<script>
    $(document).on('change', '#cutom_parent', function(e) {
        e.preventDefault();
        var parent_id = $(this).val();
        if (parent_id > 0) {
            $('.name_block').hide();
        } else {
            $('.name_block').show();
        }
    });
</script>



<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">


        </div>
    </div>
</div>
