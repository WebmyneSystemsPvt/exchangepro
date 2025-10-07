@extends('layouts.app')
@section('page_title', 'Create Category')
@section('content')
    <div class="container">
        <div class="row">
            <div class="card">
                <div class="card-body">
            <h4 class="card-title">Category Master</h4>
            <div class="col-md-6">
                <form action="{{ route('categories.store') }}" method="POST" class="material-form">
                    @csrf
                    <div class="form-group">
                        <input type="text" required="required" name="name" id="name"/>
                        <label for="input" class="control-label">Name</label><i class="bar"></i>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Submit</button>
                        <a href="{{route('categories.index')}}" class="btn btn-danger" id="backBtn">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
     </div>
</div>
@endsection
