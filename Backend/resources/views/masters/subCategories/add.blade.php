@extends('layouts.app')
@section('page_title', 'Create Sub Category')
@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6">
                        <form  method="POST" id="createItemForm" class="material-form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="category_id">Category</label>
                                <select name="categories_id" id="category_id" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($category as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" required="required" name="name" id="name"/>
                                <label for="name" class="control-label">Sub Category Name</label>
                            </div>
                            <div class="form-group">
                                <label>Photo</label>
                                <input type="file" name="photo" id="photo" onchange="previewImage(event)">
                                <img id="imagePreview" src="#" alt="Image Preview" style="display: none;" height="100px" width="100px;">
                            </div>
                            <div class="button-container">
                                <button type="button" class="btn btn-primary" id="saveBtn">Submit</button>
                                <a href="{{ route('subCategories.index') }}" class="btn btn-danger" id="backBtn">Back</a>
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
        $('#saveBtn').click(function (e) {
            e.preventDefault();
            showLoading();
            $(this).html('Sending..');
            $.ajax({
                url: "{{ route('subCategories.store') }}",
                type: "POST",
                data: new FormData($('#createItemForm')[0]),
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.status === true) {
                        hideLoading();
                        Swal.fire({
                            title: "Success!",
                            text: data.message,
                            icon: "success",
                        }).then(function() {
                            $('#createItemForm').trigger("reset");
                            window.location.href = "{{ route('subCategories.index') }}";
                        });
                    } else {
                        hideLoading();
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
                    hideLoading();
                    console.log('Error:', data);
                },
                complete: function() {
                    hideLoading();
                    $('#saveBtn').html('Submit');
                }
            });
        });
    </script>
@endsection
