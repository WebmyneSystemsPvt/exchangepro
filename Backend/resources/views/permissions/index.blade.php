@extends('layouts.app')

@section('page_title', 'Permissions')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h3>Permissions</h3>
            <a href="{{ route('permissions.create') }}" class="btn btn-primary">Create Permission</a>
        </div>
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <table class="table table-bordered" id="permissions-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#permissions-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: '{{ route('permissions.index') }}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endsection
