$(document).on('click', '#CreateModalSubmitBtn', function (e) {
    e.preventDefault();
    var $btn  = $(this);
    var $form = $('#CreateForm'); 
    $btn.prop('disabled', true);
    var formData = new FormData($form[0]); 
    $.ajax({
        url: $form.attr('action'),
        type: $form.attr('method'),
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            $btn.prop('disabled', false);

            if (response.status === 1) {
                $('#CreateModalOpen').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    timer: 3000,
                    showConfirmButton: false,
                    position: 'center'
                }).then(() => {
                    console.log('reloading..');
                    
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message,
                    timer: 3000,
                    position: 'center'
                });
            }
        },
        error: function (xhr) {
            $btn.prop('disabled', false);
            $('.text-danger').remove();

            if (xhr.status === 422) {
                $.each(xhr.responseJSON.errors, function (key, val) {
                    $('[name="' + key + '"]')
                        .after('<span class="text-danger">' + val[0] + '</span>');
                });
            }
        }
    });
});


$(document).on('click', '.edit_modal_show', function(e) {
    e.preventDefault();
    let url = $(this).attr('data-url');
    $.ajax({
        url: url,
        type: "GET",
        dataType: "html",
        success: function(data) {
            $('#editModal .modal-content').html(data);
            $('#editModal').modal('show');
        }
    })
});
    $(document).on('click', '.CreateModalOpen', function(e) {
        e.preventDefault();
        $('#CreateModalOpen').modal('show');
    });



$(document).on('click', '#EditFormSubmitBtn', function(e){
    e.preventDefault();

    var $btn  = $(this);
    var id  = $(this).data('id');
    var $form = $('#EditForm');

    $btn.prop('disabled', true);

    // Use the actual HTMLFormElement
    var formData = new FormData($form[0]);

    $.ajax({
        url: $form.attr('action'),
        type: 'POST', // Keep POST, Laravel will detect @method('PUT')
        data: formData,
        processData: false,
        contentType: false,
        headers: { 
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
        },
        success: function(response){
            $btn.prop('disabled', false);

            // Hide the modal
            $form.closest('.modal').modal('hide');

            // SweetAlert success message
            Swal.fire({
                icon: 'success',
                title: 'Updated',
                text: response.message,
                timer: 3000,
                showConfirmButton: false,
                position: 'center'
            }).then(() => location.reload());
        },
        error: function(xhr){
            $btn.prop('disabled', false);

            // Remove old errors
            $('.text-danger').remove();

            if(xhr.status === 422){
                // Show validation errors below inputs
                $.each(xhr.responseJSON.errors, function(key, val){
                    $('[name="'+key+'"]').after('<span class="text-danger">'+val[0]+'</span>');
                });

                // Optional: show all errors in SweetAlert
                let errorList = '<ul>';
                $.each(xhr.responseJSON.errors, function(key, val){
                    errorList += '<li>' + val[0] + '</li>';
                });
                errorList += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorList,
                    position: 'center'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong!',
                    position: 'center'
                });
            }
        }
    });
});


$(document).on('click', '.deleteBtn', function(e){
    e.preventDefault();

    const url = $(this).data('url');
    const id  = $(this).data('id');

    Swal.fire({
        title: 'Are you sure to delete?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed){
            $.ajax({
                url: url,
                type: 'POST', // Laravel DELETE requires POST + _method
                data: {
                    _method: 'DELETE', 
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response){
                    if(response.status == 1){
                        Swal.fire({
                            title: 'Deleted!',
                            text: response.message,
                            icon: 'success',
                            timer: 3000,
                            showConfirmButton: false
                        }).then(() => {
                            // remove the deleted row
                            $('.trclass_' + id).fadeOut('slow', function(){
                                $(this).remove();
                            });
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr){
                    Swal.fire('Error', 'Something went wrong!', 'error');
                }
            });
        }
    });
});



setTimeout(() => {
    $('.alert').hide();
}, 3500);


$(document).ready(function() {
    $(".filter-icon").click(function() {
        $(".filter-box").slideToggle(200);
    });

    $(".reset-filter").click(function() {
        $(".filter-box select").val("");
        $(".filter-box input").val("");
    });
});


$('.reset-filter').on('click', function () {
    const wrapper = $('.search-wrapper');
    wrapper.find('select').val('').trigger('change');
    wrapper.find('input').val('');
    let table = $('#dataTable').DataTable();
    table.ajax.reload();
    $(".filter-icon").trigger('click');
});

$('.apply-filter').on('click', function () {
    let table = $('#dataTable').DataTable();
    table.ajax.reload();
    $(".filter-icon").trigger('click');
});

$(document).on('change', '.image-input', function () {
    const input = this;
    const preview = $(this).closest('.col-md-6').find('.preview-image');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
});


