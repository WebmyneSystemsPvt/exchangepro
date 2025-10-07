@extends('layouts.app')
@section('page_title', 'Group Request')

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
                            <h4 class="card-title">Group Request</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{route('groupList')}}" class="btn btn-primary btn-sm float-right">Back</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="users-table">
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Group Name</th>
                                <th>Seller</th>
                                <th>Status</th>
                                <th>Document</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                var table = $('#users-table').DataTable({
                    processing: false,
                    serverSide: true,
                    ajax: "{{ route('group.request.list', ['group_id' => $group_id]) }}",
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'group.name', name: 'group.name'},
                        {data: 'seller.name', name: 'seller.name'},
                        { data: 'status', name: 'status', orderable: false, searchable: false,
                            render: function (data, type, row) {
                                var selectedPending = data === 0 ? 'selected' : '';
                                var selectedActive = data === 1 ? 'selected' : '';
                                var selectedInactive = data === 2 ? 'selected' : '';
                                return `<select class="form-control status-select" style="width: 90px;" data-id="${row.id}">
                                    <option value="0" ${selectedPending}>Pending</option>
                                    <option value="1" ${selectedActive}>Active</option>
                                    <option value="2" ${selectedInactive}>Inactive</option>
                                </select>`;
                            } },
                        {data: 'photo', name: 'photo'},
                    ],
                    scrollX: true,
                    autoWidth: false
                });

                function updateSelectBgColor(selectElement) {
                    selectElement.css('color', '#fff'); // Light gray
                    var value = selectElement.val();
                    switch (value) {
                        case '0': // Pending
                            selectElement.css('background-color', '#dea833'); // Light gray

                            break;
                        case '1': // Active
                            selectElement.css('background-color', '#78af83'); // Light green
                            break;
                        case '2': // Inactive
                            selectElement.css('background-color', '#ad565e'); // Light red
                            break;
                    }
                }

                $('#users-table').on('draw.dt', function () {
                    $('.status-select').each(function() {
                        updateSelectBgColor($(this));
                    });
                });

                // Handle status change
                $('body').on('change', '.status-select', function () {
                    var selectElement = $(this);
                    var userId = selectElement.data("id");
                    var status = selectElement.val();
                    $.ajax({
                        type: "POST",
                        url: "{{ route('group.approve.status') }}",
                        data: {
                            _token: '{{ csrf_token() }}',
                            id: userId,
                            status: status
                        },
                        success: function (data) {
                            if (data.status === true) {
                                var message = status == 1 ? 'Member group join status active!' :
                                    status == 2 ? 'Member group join status inactive!' :
                                        'Member group join status pending!';
                                Swal.fire(
                                    'Updated!',
                                    message,
                                    'success'
                                );
                                table.ajax.reload(null, false); // Reload table without resetting the pagination
                                updateSelectBgColor(selectElement);
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

                // Initial call to set the background color for the existing elements
                $('.status-select').each(function() {
                    updateSelectBgColor($(this));
                });
            });
        </script>
    </div>
@endsection
