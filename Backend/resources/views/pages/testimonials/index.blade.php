@extends('layouts.app')
@section('page_title', 'Testimonials List')

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
                            <h4 class="card-title">Testimonials List</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('testimonials.create') }}" class="btn btn-primary btn-sm float-right mr-2">Add</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="users-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>User Name</th>
                                <th>User Position</th>
                                <th>User Company</th>
                                <th>Testimonial</th>
                                <th>Photo</th>
                                <th>Created At</th>
                                <th>Updated At</th>
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
        $(document).ready(function() {
            $('#users-table').DataTable({
                processing: false,
                serverSide: true,
                ajax: "{{ route('testimonials.index')}}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'user_name', name: 'user_name' },
                    { data: 'user_position', name: 'user_position' },
                    { data: 'user_company', name: 'user_company' },
                    { data: 'testimonial', name: 'testimonial' },
                    { data: 'photo', name: 'photo', render: function(data) { return data ? '<img src="'+data+'" width="50">' : ''; } },
                    { data: 'created_at', name: 'created_at' },
                    { data: 'updated_at', name: 'updated_at' ,visible:false},
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                scrollX: true,
                autoWidth: false
            });

            $('body').on('click', '.deleteBtn', function() {
                var testimonialId = $(this).data("id");

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
                            url: "/testimonials/" + testimonialId,
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(data) {
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
                            error: function(data) {
                                Swal.fire(
                                    'Error!',
                                    'There was an error deleting the testimonial.',
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
