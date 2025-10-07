    @extends('layouts.app')

    @section('page_title', 'Edit Permission')

    @section('content')
        <div class="container">
            <h3>Edit Permission</h3>
            <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="name">Permission Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ $permission->name }}" required>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update Permission</button>
            </form>
        </div>
    @endsection
