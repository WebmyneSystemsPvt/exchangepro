@extends('layouts.app')
@section('page_title', 'FAQ List')

@section('content')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" defer></script>

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
                            <h4 class="card-title">FAQ List</h4>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('faqs.create') }}" class="btn btn-primary btn-sm float-right mr-2">Add</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="faq-table">
                            <thead>
                            <tr>
                                <th>Order</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($faqs as $faq)
                                <tr data-id="{{ $faq->id }}">
                                    <td>{{ $faq->order }}</td>
                                    <td>{{ $faq->question }}</td>
                                    <td>{{ $faq->answer }}</td>
                                    <td>
                                        <a href="{{ route('faqs.getEdit', $faq->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                        <button class="btn btn-danger btn-sm deleteBtn" data-id="{{ $faq->id }}">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#faq-table').DataTable({
                scrollX: true,
                autoWidth: false
            });

            $('#faq-table tbody').sortable({
                update: function(event, ui) {
                    var order = $(this).sortable('toArray', {
                        attribute: 'data-id'
                    });
                    $.ajax({
                        url: "{{ route('faqs.updateOrder') }}",
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}',
                            faqIds: order
                        },
                        success: function(data) {
                            if (data.status === true) {
                                toastr.success(data.message, 'Success');
                                $('#faq-table tbody tr').each(function(index) {
                                    $(this).find('td:first').html(index + 1);
                                });
                                table.rows().invalidate().draw(); // Redraw the table after sorting
                            } else {
                                toastr.error(data.message, 'Error');
                            }
                        },
                        error: function(xhr, status, error) {
                            toastr.error('Failed to update FAQ order.', 'Error');
                        }
                    });
                }
            });

            // Delete button click handler
            $('body').on('click', '.deleteBtn', function () {
                var faqId = $(this).data("id");
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
                            url: "{{ url('faqs') }}/" + faqId,
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
                                    // Hide the row in the table instead of removing it
                                    var row = $('#faq-table tbody tr[data-id="'+faqId+'"]');
                                    table.row(row).remove().draw(false); // Draw without updating
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
                                    'There was an error deleting the FAQ.',
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
