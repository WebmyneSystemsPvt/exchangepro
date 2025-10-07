@extends('layouts.app')
@section('page_title', 'Pending List')

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
                            <h4 class="card-title">Pending List</h4>
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
                                    <th>Seller Name</th>
                                    <th>Item Name</th>
                                    <th>Listing Type</th>
                                    <th>Status (Disapprove/Approve)</th>
                                    <th>Location</th>
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
                    ajax: "{{ route('pending.item.storage.list')}}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'seller', name: 'seller'},
                        {data: 'itemName', name: 'itemName'},
                        {data: 'listing_type', name: 'listing_type'},
                        {data: 'status', name: 'status', orderable: false, visible: true, searchable: false, render: function(data, type, row) {
                                var checked = data === 1 ? 'checked' : '';
                                return `<div class="checkbox">
                              <input type="checkbox" class="status-toggle checkbox-toggle" id="status-${row.id}" data-id="${row.id}" ${checked} />
                              <label for="status-${row.id}"><i class="helper"></i></label>
                            </div>`;
                            }},
                        {data: 'location', name: 'location', width:20},

                    ],
                    scrollX: true,
                    autoWidth: false
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
