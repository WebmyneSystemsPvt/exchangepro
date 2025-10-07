@extends('layouts.app')

@section('page_title', 'Edit Role')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Edit Role: {{ $role->name }}</h3>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Assigned Permissions:</h4>
                                <ul class="list-group" id="assignedPermissionsList">
                                    @foreach($role->permissions as $permission)
                                        <li class="list-group-item">{{ $permission->name }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h4>Available Permissions:</h4>
                                <input type="text" id="searchInput" class="form-control mb-3" placeholder="Search permissions...">
                                <form id="assignPermissionsForm">
                                    <ul class="list-group" id="permissionList">
                                        @foreach($permissions as $permission)
                                            <li class="list-group-item">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_{{ $permission->id }}"
                                                           value="{{ $permission->id }}" {{ $role->permissions->contains($permission) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <button type="submit" class="btn btn-primary mt-3">Save Permissions</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-check-input:not(:checked) {
            box-shadow: 2px 2px 2px 2px #f2ab27; /* Adjust shadow properties as needed */
        }
        .form-check {
            display: flex;
            align-items: center;
        }
        #assignedPermissionsList, #permissionList {
            max-height: 400px;
            overflow-y: auto;
            padding: 0;
        }
        #permissionList li {
            display: flex;
            align-items: center;
        }
        .list-group-item {
            padding: 0.5rem 1rem;
        }
        .form-check-input:checked[type="checkbox"] {
            margin-left: 10px !important;
            box-shadow: 2px 2px 2px 2px #f2ab27;
        }
        .form-check-input[type="checkbox"]{
            margin-left: 10px !important;
        }
    </style>

    <script>
        $(document).ready(function() {
            $('#assignPermissionsForm').on('submit', function(e) {
                e.preventDefault();

                var permissions = [];
                $('input[type=checkbox]:checked').each(function() {
                    permissions.push($(this).val());
                });

                var url = '{{ route("roles.updatePermissions", ["role" => $role->id]) }}';
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { permissions: permissions },
                    success: function(response) {
                        alert('Permissions updated successfully.');
                        // Update the assigned permissions list
                        $('#assignedPermissionsList').empty();
                        $('input[type=checkbox]:checked').each(function() {
                            $('#assignedPermissionsList').append('<li class="list-group-item">' + $(this).next('label').text() + '</li>');
                        });
                    },
                    error: function(xhr, status, error) {
                        alert('Error updating permissions: ' + error);
                    }
                });
            });

            // Search functionality
            $('#searchInput').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#permissionList li').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                });
            });
        });
    </script>
@endsection
