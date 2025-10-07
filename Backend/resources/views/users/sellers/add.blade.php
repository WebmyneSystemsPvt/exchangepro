@extends('layouts.app')

@section('page_title', 'User Create')

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
                        <h4 class="card-title">User Create</h4>
                        <form class="forms-sample material-form" id="userForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="name" required="required" />
                                        <label for="input" class="control-label">Name</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" required="required" />
                                        <label for="input" class="control-label">Email address</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" required="required" />
                                        <label for="input" class="control-label">Password</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password_confirmation" required="required" />
                                        <label for="input" class="control-label">Password Confirmation</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <label for="active" class="font-weight-medium font-weight-500 flag-color">Status</label>
                                        <div class="row text-right">
                                            <div class="col-3">
                                                <label for="active">Active</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" id="active" name="status" value="1" required>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3">
                                                <label for="inactive">Inactive</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" id="inactive" name="status" value="0" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="company_name" required="required" />
                                        <label for="input" class="control-label">Company Name</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="city" required="required" />
                                        <label for="input" class="control-label">City</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="pincode" required="required" minlength="6" maxlength="6"/>
                                        <label for="input" class="control-label">Pincode</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="location" required="required" />
                                        <label for="input" class="control-label">Location</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="phone_number" required="required" maxlength="10"   />
                                        <label for="input" class="control-label">Phone Number</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <textarea type="text" name="address" required="required" ></textarea>
                                        <label for="input" class="control-label">Address</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="availabilityMF" required="required" id="availabilityMF">
                                        <label for="input" class="control-label">Availability Mon - Fri</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="availabilitySS" required="required" id="availabilitySS" >
                                        <label for="input" class="control-label">Availability Sat - Sub</label><i class="bar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="button-container">
                                <button type="button" class="button btn btn-primary" id="saveBtn">Submit</button>
                                <a href="{{route('sellerusersListing')}}" class="button btn btn-danger" id="backBtn">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        $(document).ready(function() {
            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Sending..');
                $.ajax({
                    data: $('#userForm').serialize(),
                    url: "{{ route('selleruserstore') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function (data) {
                        if (data.status === true) {
                            Swal.fire({
                                title: "Success!",
                                text: data.message,
                                icon: "success",
                            }).then(function() {
                                $('#userForm').trigger("reset");
                                window.location.href = "{{ route('sellerusersListing') }}";
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
                    complete: function() {
                        $('#saveBtn').html('Submit');
                    }
                });
            });

        });
    </script>

@endsection
