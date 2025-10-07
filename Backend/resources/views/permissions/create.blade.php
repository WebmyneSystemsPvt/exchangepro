@extends('layouts.app')

@section('page_title', 'Create Permission')

@section('content')
    <div class="container">
        <h3>Create Permission</h3>
        <form action="{{ route('permissions.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Permission Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Create Permission</button>
        </form>
    </div>
@endsection
