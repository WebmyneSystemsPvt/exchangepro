@extends('layouts.app')

@section('page_title', 'Roles')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h3>Roles</h3>
            <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-bordered" id="roles-table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
            </thead>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#roles-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('roles.index') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'permissions', name: 'permissions' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endsection
