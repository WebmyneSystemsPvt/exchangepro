@extends('layouts.app')
@section('page_title', 'Create Settings')
@section('content')
    <div class="container">
        <div class="row">
            <h4 class="card-title">Settings</h4>
            <div class="col-md-6">
                <form action="{{ route('settings.store') }}" method="POST" class="material-form">
                    @csrf
                    <div class="form-group">
                        <input type="text" required="required" name="margin_percentage" id="name"/>
                        <label for="input" class="control-label">Name</label><i class="bar"></i>
                    </div>
                    <div class="form-group">
                        <input type="text" required="required" name="application_fee" id="application_fee"/>
                        <label for="input" class="control-label">Application Fee</label><i class="bar"></i>
                    </div>
                    <div class="form-group">
                        <input type="text" required="required" name="others_fee" id="others_fee"/>
                        <label for="input" class="control-label">Others Fee</label><i class="bar"></i>
                    </div>
                    <div class="form-group">
                        <input type="text" required="required" name="tax" id="tax"/>
                        <label for="input" class="control-label">Tax</label><i class="bar"></i>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="button btn btn-primary" id="saveBtn">Submit</button>
                        <a href="{{route('settings.index')}}" class="button btn btn-primary" id="backBtn">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
