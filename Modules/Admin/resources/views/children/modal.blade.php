<!-- Modal -->
<div class="modal fade" id="CreateModalOpen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="CreateForm" action="{{ route('admin.children.store') }}" method="POST"
                enctype="multipart/form-data">

                @csrf
                <div class="modal-header">
                    <b class="modal-title fs-5" id="exampleModalLabel">Add New</b>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <div class="server_side_error"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Enter title">
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal-footer">
                    <button type="button" id="CreateModalSubmitBtn" class="btn btn-sm btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- edit modal  --}}
