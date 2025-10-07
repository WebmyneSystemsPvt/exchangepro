@extends('layouts.app')

@section('page_title', 'Group Edit')

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
                        <h4 class="card-title">Group Edit</h4>
                        <form class="forms-sample material-form" name="groupForm" id="groupForm" enctype="multipart/form-data">
                            <input type="hidden" id="group_id" name="group_id" value="{{ $group->id }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="name" required="required" value="{{ $group->name }}"/>
                                        <label for="input" class="control-label">Name</label><i class="bar"></i>
                                    </div>
                                    <label for="input" class="control-label">Group Documents</label>
                                    <div class="form-group">
                                        <input type="file" name="group_document[]" multiple onchange="previewImages(event)"/>
                                    </div>
                                    <div id="imagePreview" class="row mt-3">
                                        @foreach($group->documents as $document)
                                            <div class="col-md-3">
                                                <img src="{{ $document->group_document }}" class="preview-image">
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="form-group">
                                        <label for="active" class="font-weight-medium font-weight-500 flag-color">Status</label>
                                        <div class="row text-right">
                                            <div class="col-3">
                                                <label for="active">Active</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" id="active" name="status" value="1" required {{ $group->status == 1 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3">
                                                <label for="inactive">Inactive</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" id="inactive" name="status" value="0" required {{ $group->status == 0 ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="button-container">
                                <button type="button" class="button btn btn-primary" id="saveBtn">Submit</button>
                                <a href="{{route('groupList')}}" class="button btn btn-danger" id="backBtn">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        function previewImages(event) {
            var previewContainer = document.getElementById('imagePreview');
            previewContainer.innerHTML = ''; // Clear previous previews

            var files = event.target.files;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var reader = new FileReader();

                reader.onload = function(e) {
                    var imgElement = document.createElement('img');
                    imgElement.setAttribute('src', e.target.result);
                    imgElement.setAttribute('class', 'preview-image');
                    previewContainer.appendChild(imgElement);
                }

                reader.readAsDataURL(file);
            }
        }

        $(document).ready(function() {
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Saving..');
                var formData = new FormData($('#groupForm')[0]); // Create FormData object from form

                $.ajax({
                    data: formData,
                    processData: false,  // Important: Don't process the files
                    contentType: false,  // Important: Set content type to false
                    url: "{{ route('group.update', $group->id) }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if (data.status === true) {
                            hideLoading()
                            Swal.fire({
                                title: "Success!",
                                text: data.message,
                                icon: "success",
                            }).then(function() {
                                $('#groupForm').trigger("reset");
                                window.location.href = "{{ route('groupList') }}";
                            });
                        } else {
                            hideLoading()
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
                    beforeSend: function() {

                    },
                    error: function(data) {

                        console.log('Error:', data);
                    },
                    complete: function() {
                        $('#saveBtn').html('Submit');

                    }
                });
            });
        });
    </script>

@endsection

<style>
    .preview-image {
        max-width: 100px; /* Adjust as needed */
        max-height: 100px; /* Adjust as needed */
        margin-right: 10px; /* Spacing between images */
    }
</style>
