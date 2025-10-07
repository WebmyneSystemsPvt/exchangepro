@extends('layouts.app')

@section('page_title', 'Create Testimonials')

@section('content')
    <div class="container">
        <form action="{{ route('testimonials.store') }}" method="POST" class="forms-sample material-form"
              enctype="multipart/form-data" id="testimonialForm">
            @csrf
            <div id="testimonials-container">
                <div class="card testimonial-entry mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Testimonial 1</h4>
                        <button type="button" class="btn btn-danger btn-sm float-right remove-entry">Remove</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="testimonials[0][user_name]" required="required" />
                                    <label for="input" class="control-label">User Name</label><i class="bar"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="testimonials[0][user_position]" required>
                                    <label for="input" class="control-label">User Position</label><i class="bar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="testimonials[0][user_company]" required>
                                    <label for="user_company" class="control-label">User Company</label><i
                                        class="bar"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <textarea name="testimonials[0][testimonial]" rows="2" required></textarea>
                                    <label for="testimonial" class="control-label">Testimonial</label><i
                                        class="bar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <label for="photo">Photo</label>
                            <input type="file" name="testimonials[0][photo]" class="form-control-file"
                                   onchange="previewImage(this, 0)">
                            <img id="preview-0" src="" alt="Image Preview" class="img-thumbnail mt-2"
                                 style="display:none; max-width: 100px;">
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mb-4">
                <button type="button" id="addMore" class="btn btn-success">Add More</button>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a href="{{ route('testimonials.index') }}" class="btn btn-danger">Back</a>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            var index = 1;

            $('#addMore').click(function () {
                var newEntry = `
            <div class="card testimonial-entry mb-3">
                <div class="card-header">
                    <h4 class="card-title">Testimonial ${index + 1}</h4>
                    <button type="button" class="btn btn-danger btn-sm float-right remove-entry">Remove</button>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="testimonials[${index}][user_name]" required>
                                <label for="input" class="control-label">User Name</label><i class="bar"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="testimonials[${index}][user_position]" required>
                                <label for="input" class="control-label">User Position</label><i class="bar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <input type="text" name="testimonials[${index}][user_company]">
                                <label for="user_company" class="control-label">User Company</label><i
                                    class="bar"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <textarea name="testimonials[${index}][testimonial]" rows="2" required></textarea>
                                <label for="testimonial" class="control-label">Testimonial</label><i
                                    class="bar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <label for="photo">Photo</label>
                        <input type="file" name="testimonials[${index}][photo]" class="form-control-file"
                            onchange="previewImage(this, ${index})">
                        <img id="preview-${index}" src="" alt="Image Preview" class="img-thumbnail mt-2"
                            style="display:none; max-width: 100px;">
                    </div>
                </div>
            </div>
            `;

                $('#testimonials-container').append(newEntry);
                index++;
            });

            $(document).on('click', '.remove-entry', function () {
                $(this).closest('.testimonial-entry').remove();
                index--;
            });

            $('#testimonialForm').submit(function (event) {
                event.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('testimonials.store') }}",
                    method: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        if (response.status === true) {
                            toastr.success(response.message);
                            $('#testimonialForm')[0].reset();
                            $('#testimonials-container').html('');
                            index = 1;
                            window.location.href = "{{ route('testimonials.index') }}";
                        } else {
                            if (Array.isArray(response.message)) {
                                response.message.forEach(function (error) {
                                    toastr.error(error);
                                });
                            } else {
                                toastr.error(response.message);
                            }
                        }
                    },
                    error: function (response) {
                        toastr.error('Error submitting form. Please try again.');
                    }
                });
            });

            function previewImage(input, index) {
                const file = input.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#preview-' + index).attr('src', e.target.result).css('display', 'block'); // Ensure the image is displayed
                    }
                    reader.readAsDataURL(file);
                }
            }

        });
    </script>
@endsection

<style>
    .testimonial-entry .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .testimonial-entry .remove-entry {
        margin-left: auto;
    }

    .testimonial-entry .card-title {
        margin-bottom: 0;
    }

    .form-control-file {
        margin-top: 5px;
    }
</style>
