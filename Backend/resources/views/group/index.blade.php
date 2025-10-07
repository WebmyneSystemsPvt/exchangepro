@extends('layouts.app')
@section('page_title','Seller Storage Request')
@section('content')
    <div class="row">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title">Group</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('groupAdd') }}" class="btn btn-primary btn-sm float-right">Add</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <form id="group-filter-form">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="created_by" class="form-label">Created By:</label>
                                    <select class="form-select" id="created_by" name="created_by">
                                        <option value="">All</option>
                                        @foreach (\App\Models\User::get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="role" class="form-label">Role:</label>
                                    <select class="form-select" id="role" name="role">
                                        <option value="">All</option>
                                        @foreach (\Spatie\Permission\Models\Role::whereNotIn('name', [config('constants.BORROWER')])->get() as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status:</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label><br>
                                    <button type="button" id="apply-filter-btn" class="btn btn-primary btn-sm">Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table" id="groups-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Group Name</th>
                                    <th>Created By</th>
                                    <th>Group Request</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {
            var table = $('#groups-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: "{{ route('groupList') }}",
                    type: "GET",
                    data: function (d) {
                        d.created_by = $('#created_by').val();
                        d.role = $('#role').val();
                        d.status = $('#status').val();
                    },
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'created_by', name: 'created_by' },
                    { data: 'groupRequest', name: 'groupRequest' },
                    { data: 'status', name: 'status', orderable: false, visible: true, searchable: false,
                        render: function (data, type, row) {
                            var checked = data === 1 ? 'checked' : '';
                            return `<div class="checkbox">
                              <input type="checkbox" class="status-toggle checkbox-toggle" id="status-${row.id}" data-id="${row.id}" ${checked} />
                              <label for="status-${row.id}"><i class="helper"></i></label>
                            </div>`;
                        } },
                    { data: 'action', name: 'action', visible: true, orderable: false, searchable: false },
                ],
                scrollX: true,
                autoWidth: false
            });

            // Apply filter on button click
            $('#apply-filter-btn').click(function () {
                table.ajax.reload();
            });

            // Handle status toggle
            $('body').on('change', '.status-toggle', function () {
                var userId = $(this).data("id");
                var status = this.checked ? 1 : 0;
                $.ajax({
                    type: "POST",
                    url: "{{ route('group.status') }}",
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: userId,
                        status: status
                    },
                    success: function (data) {
                        if (data.status === true) {
                            var message = status === 1 ? 'Group status active!' : 'Group status Inactive!';
                            Swal.fire(
                                'Updated!',
                                message,
                                'success'
                            );
                            table.ajax.reload();
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message,
                                'error'
                            );
                        }
                    },
                    error: function (data) {

                        Swal.fire(
                            'Error!',
                            'There was an error updating the status.',
                            'error'
                        );
                    }
                });
            });

            // Handle delete action
            $('body').on('click', '.deleteBtn', function () {
                var userId = $(this).data("id");
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('groupdelete') }}/" + userId,
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (data) {
                                if (data.status === true) {
                                    Swal.fire(
                                        'Deleted!',
                                        data.message,
                                        'success'
                                    );
                                    table.ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        data.message,
                                        'error'
                                    );
                                }
                            },
                            error: function (data) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the user.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
