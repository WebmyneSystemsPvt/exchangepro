@extends('layouts.app')

@section('page_title', 'Create items')

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
                        <h4 class="card-title">Create item</h4>
                        <form id="createItemForm" class="material-form" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" required="required" name="item_name" id="name"/>
                                        <label for="name" class="control-label">Name</label><i class="bar"></i>
                                    </div>

                                    <div class="form-group">
                                        <textarea required="required" name="item_description"></textarea>
                                        <label for="textarea" class="control-label">Description</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" required="required" name="item_weight" id="item_weight" class="numberonly"/>
                                        <label for="item_weight" class="control-label ">Item Weight (GRM/KG)</label><i class="bar"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Item Image</label>
                                        <input type="file" name="item_photo" id="photo" onchange="previewImage(event)">
                                        <img id="imagePreview" src="#" alt="Image Preview" style="display: none;" height="100px" width="100px;">
                                    </div>


                                    <div class="form-group">
                                        <label for="item_status">Item Status</label><i class="bar"></i>
                                        <select name="item_status" id="item_status" class="form-control">
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
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
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
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
                    document.getElementById('sub_category_id').innerHTML = options;
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        $('#saveBtn').click(function (e) {
            e.preventDefault();
            $(this).html('Sending..');
            $.ajax({
                url: "{{ route('items.store') }}",
                type: "POST",
                data: new FormData($('#createItemForm')[0]),
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.status === true) {
                        Swal.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success",
                        }).then(function() {
                            $('#createItemForm').trigger("reset");
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
