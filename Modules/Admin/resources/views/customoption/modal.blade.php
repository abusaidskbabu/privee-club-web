<!-- Modal -->

<div class="modal fade" id="CreateModalOpen" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">

        <div class="modal-content">
            <form id="CreateForm" action="{{ route('admin.custom-options.store') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="modal-header" style="display: block;">
                    <h4 class="modal-title">Add New</h4>
                </div>

                <div class="modal-body">
                    <div class="server_side_error"></div>

                    <div class="form-group">
                        <label>Parent</label>
                        <select name="parent_id" id="cutom_parent" class="form-control">
                            <option value="0">--Select Parent--</option>
                            @foreach ($selectoptions as $option)
                                <option value="{{ $option->id }}">{{ $option->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group name_block">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name">
                    </div>

                    <div class="form-group">
                        <label>Value</label>
                        <input type="text" class="form-control" name="value">
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
