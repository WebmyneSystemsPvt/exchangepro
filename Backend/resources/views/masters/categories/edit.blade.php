@extends('layouts.app')
@section('page_title', 'Edit Category')
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
                <form action="{{ route('categories.update', $category->id) }}" method="POST" class="material-form"> <!-- Corrected variable -->
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <input type="text" required="required" name="name" id="name" value="{{ $category->name }}"/>
                        <label for="input" class="control-label">Name</label><i class="bar"></i>
                    </div>
                    <div class="button-container">
                        <button type="submit" class="btn btn-primary" id="saveBtn">Submit</button>
                        <a href="{{ route('categories.index') }}" class="btn btn-danger" id="backBtn">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
