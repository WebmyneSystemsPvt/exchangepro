@extends('layouts.app')
@section('titpage_titlele', 'Settings')
@section('content')
    <div class="row">
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
    </div>
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col">
                        <h4 class="card-title">Settings</h4>
                    </div>
                    <div class="col-auto">
{{--                        <a href="{{ route('settings.create') }}"  class="btn btn-primary btn-sm float-right" id="addUserBtn">Add</a>--}}
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table" id="languages-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Margin Percentage</th>
                                <th>Application Fee</th>
                                <th>Others Fee</th>
                                <th>TAX</th>
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
            var table = $('#languages-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('settingsData') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex'},
                    {data: 'margin_percentage', name: 'margin_percentage'},
                    {data: 'application_fee', name: 'application_fee'},
                    {data: 'others_fee', name: 'others_fee'},
                    {data: 'tax', name: 'tax'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                ],
                scrollX: true,
                autoWidth: false
            });

            // $('#gender_filter').on('change', function() {
            //     var genderId = $(this).val();
            //     table.column(1).search(genderId).draw();
            // });

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
                            url: 'settings/' + userId,
                            type: 'DELETE',
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
                                    $('#languages-table').DataTable().ajax.reload();
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
