@extends('layouts.app')

@section('page_title', 'Edit items')

@section('content')
    <div class="container">
        <div class="row">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit item</h4>
                        <form id="editItemForm" enctype="multipart/form-data" class="material-form" method="POST" action="{{ route('items.update', ['item' => $data->id]) }}">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" required="required" name="item_name" id="item_name" value="{{$data->item_name}}" />
                                        <label for="item_name" class="control-label">Name</label><i class="bar"></i>
                                    </div>

                                    <div class="form-group">
                                        <textarea required="required" name="item_description">{{$data->item_description}}</textarea>
                                        <label for="item_description" class="control-label">Description</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" required="required" name="item_weight" id="item_weight" value="{{$data->item_weight}}" class="numberonly"/>
                                        <label for="item_weight" class="control-label">Item Weight (GRM/KG)</label><i class="bar"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Item Image</label>
                                        <input type="file" name="item_photo" id="item_photo" onchange="previewImage(event)">
                                        <img id="imagePreview" src="{{ $data->item_photo }}" alt="Image Preview" style="display: {{ $data->item_photo ? 'block' : 'none' }};" height="100px" width="100px;">
                                    </div>

                                    <div class="form-group">
                                        <label for="item_status">Item Status</label><i class="bar"></i>
                                        <select name="item_status" id="item_status" class="form-control">
                                            <option value="1" {{$data->item_status == '1' ? 'selected' : ''}}>Active</option>
                                            <option value="0" {{$data->item_status == '0' ? 'selected' : ''}}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="button-container">
                                <button type="button" class="btn btn-primary" id="saveBtn">Submit</button>
                                <a href="{{ route('items.index') }}" class="btn btn-danger" id="backBtn">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        function previewImage(event) {
            var input = event.target;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result);
                    $('#imagePreview').show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function getSubCategories(categoryId) {
            $.ajax({
                url: '/get-subcategories/' + categoryId,
                type: 'GET',
                success: function (data) {
                    var options = '<option value="">Select</option>';
                    data.forEach(function (subcategory) {
                        options += '<option value="' + subcategory.id + '">' + subcategory.name + '</option>';
                    });
                    $('#sub_category_id').html(options);
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Sending..');

            var itemId = "{{ $data->id }}";

            var url = "{{ route('items.update', ['item' => ':id']) }}";
            url = url.replace(':id', itemId);

            var formData = new FormData($('#editItemForm')[0]);
            formData.append('_method', 'PUT'); // Specify the HTTP method override

            $.ajax({
                url: url,
                type: 'POST', // Use POST method
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Include CSRF token
                },
                success: function (data) {
                    if (data.status === true) {
                        Swal.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success",
                        }).then(function() {
                            $('#editItemForm').trigger("reset");
                            window.location.href = "{{ route('items.index') }}";
                        });
                    } else {
                        let formattedErrors = "<ul>";
                        $.each(data.message, function(index, error) {
                            formattedErrors += "<li>" + error + "</li>";
                        });
                        formattedErrors += "</ul>";
                        Swal.fire({
                            title: "Warning!",
                            html: formattedErrors,
                            icon: "warning",
                        });
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                },
                complete: function() {
                    $('#saveBtn').html('Submit');
                }
            });
        });

    </script>
@endsection
