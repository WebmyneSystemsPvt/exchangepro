@extends('layouts.app')
@section('page_title', 'Seller Storage Request')

@section('content')
    <style>
        #users-table {
            width: 100% !important;
        }
        #users-table th, #users-table td {
            text-align: left; /* Adjust as needed */
        }
        .dataTables_wrapper .dataTables_scroll .dataTables_scrollBody table {
            width: 100% !important;
        }
    </style>
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
                            <h4 class="card-title">Listing of {{$userName}}</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{route('add.item.storage')}}" class="btn btn-primary btn-sm float-right">Add</a>
                            <a href="{{route('sellerusersListing')}}" class="btn btn-primary btn-sm float-right">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="users-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Listing Name</th>
                                    <th>Location</th>
                                    <th>Reviews</th>
                                    <th>Status (Disapprove/Approve)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                $('#users-table').DataTable({
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('seller.item.storage.list', ['seller_id' => $user_id]) }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'listing_type', name: 'listing_type'},
                        {data: 'location', name: 'location',visible:false},
                        {data: 'ratingsCount', name: 'ratingsCount'},
                        {data: 'status', name: 'status', orderable: false, searchable: false, render: function(data, type, row) {
                                var checked = data === 1 ? 'checked' : '';
                                return `<div class="checkbox">
                      <input type="checkbox" class="status-toggle checkbox-toggle" id="status-${row.id}" data-id="${row.id}" ${checked} />
                      <label for="status-${row.id}"><i class="helper"></i></label>
                    </div>`;
                            }},
                        {data: 'action', name: 'action', orderable: false, searchable: false}
                    ],
                    scrollX: true,
                    autoWidth: false,
                    columnDefs: [
                        { targets: 0, width: '20%' },
                        { targets: 1, width: '20%' },
                        { targets: 2, width: '20%' },
                        { targets: 3, width: '20%' },
                        { targets: 4, width: '20%' },
                        { targets: 5, width: '20%' }
                    ]
                });

                $('body').on('change', '.status-toggle', function () {
                    var userId = $(this).data("id");
                    var status = this.checked ? 1 : 0;
                    $.ajax({
                        type: "POST",
                        url: "{{ route('seller.item.storage.update.status') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            user_id: userId,
                            status: status
                        },
                        success: function (data) {
                            if (data.status === true) {
                                var message = status === 1 ? 'Seller Storage Request approved!' : 'Seller Storage Request disapproved!';
                                Swal.fire(
                                    'Updated!',
                                    message,
                                    'success'
                                );
                                $('#users-table').DataTable().ajax.reload();
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
                                url: "{{ url('sellerdelete') }}/" + userId,
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
                                        $('#users-table').DataTable().ajax.reload();
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
    </div>
@endsection
