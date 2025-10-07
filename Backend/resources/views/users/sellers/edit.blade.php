@extends('layouts.app')

@section('page_title', 'User Edit')

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
                        <h4 class="card-title">User Edit</h4>
                        <form class="forms-sample material-form" id="userForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="hidden" id="user_id" name="user_id" value="{{ @$user->id }}">
                                    <div class="form-group">
                                        <input type="text" name="name" id="name" value="{{@$user->name}}" required="required" />
                                        <label for="input" class="control-label">Name</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" required="required" value="{{@$user->email}}"/>
                                        <label for="input" class="control-label">Email address</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password"/>
                                        <label for="input" class="control-label">Password</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password_confirmation" id="password_confirmation"/>
                                        <label for="input" class="control-label">Password Confirmation</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <label for="active" class="font-weight-medium font-weight-500 flag-color">Status</label>
                                        <div class="row text-right">
                                            <div class="col-3">
                                                <label for="active">Active</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" id="active" name="status" value="1" required {{@$user->status === 1 ? 'checked' : ''}}>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-3">
                                                <label for="inactive">Inactive</label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" id="inactive" name="status" value="0" required {{@$user->status === 0 ? 'checked' : ''}}>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="company_name" required="required" value="{{@$user->sellerDetails->company_name}}"/>
                                        <label for="input" class="control-label">Company Name</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="city" required="required" value="{{@$user->sellerDetails->city}}"/>
                                        <label for="input" class="control-label">City</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="pincode" required="required" minlength="6" maxlength="6" value="{{@$user->sellerDetails->pincode}}"/>
                                        <label for="input" class="control-label">Pincode</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="location" required="required" value="{{@$user->sellerDetails->location}}"/>
                                        <label for="input" class="control-label">Location</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="phone_number" required="required" maxlength="10" value="{{@$user->sellerDetails->phone_number}}"/>
                                        <label for="input" class="control-label">Phone Number</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <textarea type="text" name="address" required="required" >{{@$user->sellerDetails->address}}</textarea>
                                        <label for="input" class="control-label">Address</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="availabilityMF" required="required" id="availabilityMF" value="{{@$user->sellerDetails->availabilityMF}}">
                                        <label for="input" class="control-label">Availability Mon - Fri</label><i class="bar"></i>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="availabilitySS" required="required" id="availabilitySS" value="{{@$user->sellerDetails->availabilitySS}}">
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
            $('#saveBtn').on('click', function(e) {
                e.preventDefault();
                $(this).html('Updating..');
                $.ajax({
                    data: $('#userForm').serialize(),
                    url: "{{ route('selleruser.update') }}", // Corrected the URL here
                    type: 'POST',
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === true) {
                            $('#userForm')[0].reset();
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
                            $.each(data.errors, function(index, error) {
                                formattedErrors += "<li>" + error + "</li>";
                            });
                            formattedErrors += "</ul>";

                            Swal.fire({
                                title: "Error!",
                                html: formattedErrors,
                                icon: "error",
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
