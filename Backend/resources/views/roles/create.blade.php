@extends('layouts.app')

@section('page_title', 'Create Role')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Create Role</h3>
                        <hr>
                        <form action="{{ route('roles.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Role Name</label>
                                <input type="text" name="name" id="name" class="form-control" required>
                            </div>
{{--                            <div class="form-group">--}}
{{--                                <label for="permissions">Permissions</label>--}}
{{--                                <select name="permissions[]" id="permissions" class="form-control" multiple>--}}
{{--                                    @foreach ($permissions as $permission)--}}
{{--                                        <option value="{{ $permission->id }}">{{ $permission->name }}</option>--}}
{{--                                    @endforeach--}}
{{--                                </select>--}}
{{--                            </div>--}}
                            <div class="form-group">
                                <label for="permissions">Permissions</label>
                                <ul class="list-group" id="permissionList">
                                    @foreach ($permissions as $permission)
                                        <li class="list-group-item">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="permission_{{ $permission->id }}"
                                                       name="permissions[]" value="{{ $permission->id }}">
                                                <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3">Create Role</button>
                        </form>
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
@endsection
