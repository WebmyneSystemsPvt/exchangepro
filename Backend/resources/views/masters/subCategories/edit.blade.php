@extends('layouts.app')
@section('page_title', 'Edit Sub Category')
@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <div class="col-md-6">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <form action="{{ route('subCategories.update', $subCategory->id) }}" method="POST" class="material-form" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="categories_id" class="">Category</label>
                                <select name="categories_id" id="categories_id" class="form-control">
                                    <option value="">Select</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ $category->id === $subCategory->categories_id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <i class="bar"></i>
                            </div>
                            <div class="form-group">
                                <input type="text" required="required" name="name" id="name" value="{{ $subCategory->name }}" />
                                <label for="name" class="control-label">Name</label>
                                <i class="bar"></i>
                            </div>
                            <div class="form-group">
                                <label>Photo</label>
                                <input type="file" name="photo" id="photo" onchange="previewImage(event)">
                                <img id="imagePreview" src="{{ $subCategory->photo }}" alt="Current Photo" height="100px" width="100px">
                            </div>
                            <div class="button-container">
                                <button type="submit" class="btn btn-primary" id="saveBtn">Submit</button>
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
    </script>
@endsection
