@extends('layouts.app')
@section('page_title', 'Create Language')
@section('content')
    <div class="container">
        <div class="row">
             <div class="card">
                <div class="card-body">
            <div class="col-md-6">
                <form action="{{ route('languages.store') }}" method="POST" class="material-form">
                    @csrf
                    <div class="form-group">
                        <input type="text" required="required" name="name" id="gender"/>
                        <label for="input" class="control-label">Language Name</label><i class="bar"></i>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Submit</button>
                        <a href="{{route('languages.index')}}" class="btn btn-danger" id="backBtn">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
