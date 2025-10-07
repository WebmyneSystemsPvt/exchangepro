@extends('layouts.app')

@section('page_title', 'Edit Testimonial')

@section('content')
    <div class="container">
        <div class="row">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit Testimonial</h4>
                        <form class="forms-sample material-form" id="testimonialForm" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="editId" value="{{ $testimonial->id }}">

                            <div class="form-group">
                                <input type="text" name="user_name" class="form-control" value="{{ $testimonial->user_name }}" required />
                                <label class="control-label" for="user_name">User Name</label><i class="bar"></i>
                            </div>

                            <div class="form-group">
                                <input type="text" name="user_position" class="form-control" value="{{ $testimonial->user_position }}" required />
                                <label class="control-label" for="user_position">User Position</label><i class="bar"></i>
                            </div>

                            <div class="form-group">
                                <input type="text" name="user_company" class="form-control" value="{{ $testimonial->user_company }}" />
                                <label class="control-label" for="user_company">User Company</label><i class="bar"></i>
                            </div>

                            <div class="form-group">
                                <textarea name="testimonial" class="form-control" rows="3" required>{{ $testimonial->testimonial }}</textarea>
                                <label class="control-label" for="testimonial">Testimonial</label><i class="bar"></i>
                            </div>

                            <div class="form-group">
                                <input type="file" name="photo" class="form-control-file" id="photoInput" />
                                <label class="control-label" for="photo">Photo</label>
                                @if ($testimonial->photo)
                                    <img src="{{ $testimonial->photo }}" alt="Photo" class="img-thumbnail mt-2" width="100" id="photoPreview">
                                @endif
                            </div>
                            <button type="button" class="btn btn-primary" id="saveBtn">Submit</button>
                            <a href="{{ route('testimonials.index') }}" class="btn btn-danger">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#photoInput').change(function() {
                var input = this;
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#photoPreview').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            });

            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');

                var formData = new FormData($('#testimonialForm')[0]);

                $.ajax({
                    data: formData,
                    url: "{{ route('testimonials.update', $testimonial->id) }}", // Update route URL
                    type: "POST",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.status === true) {
                            toastr.success(data.message, 'Success');
                            window.location.href = "{{ route('testimonials.index') }}";
                        } else {
                            toastr.warning(data.message, 'Warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed to update testimonial.', 'Error');
                    },
                    complete: function() {
                        $('#saveBtn').html('Submit');
                    }
                });
            });
        });
    </script>
@endsection
