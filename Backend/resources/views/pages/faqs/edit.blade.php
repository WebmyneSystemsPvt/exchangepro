@extends('layouts.app')

@section('page_title', 'Edit FAQ')
@section('content')
    <div class="container">
        <div class="row">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Edit FAQ</h4>
                        <form class="forms-sample material-form" id="faqForm">
                            @csrf
                            <input type="hidden" name="editId" value="{{$faq->id}}">
                            <div id="faqEntries">
                                <div class="faq-entry">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="question" class="form-control" value="{{ $faq->question }}" required />
                                                <label class="control-label">Question</label><i class="bar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <textarea name="answer" class="form-control" rows="2" required>{{ $faq->answer }}</textarea>
                                                <label class="control-label">Answer</label><i class="bar"></i>
                                            </div>
                                        </div>
{{--                                        <div class="col-md-6">--}}
{{--                                            <button type="button" class="btn btn-danger removeBtn">Remove</button>--}}
{{--                                        </div>--}}
                                    </div>
                                </div>
                            </div>
                            <div class="button-container">
{{--                                <button type="button" class="btn btn-success mt-3" id="addBtn">Add More</button>--}}
                                <button type="button" class="btn btn-primary mt-3" id="saveBtn">Submit</button>
                                <a href="{{ route('faqs.index') }}" class="btn btn-danger mt-3" id="backBtn">Back</a>
                            </div>
{{--                            <div class="col-md-6 mt-3">--}}
{{--                                <h6 class="card-title">FAQ List</h6>--}}
{{--                                <div id="dragula-event-left" class="py-2">--}}
{{--                                    <!-- FAQs will be dynamically loaded here -->--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </form>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            var entryCount = 1; // Initialize entry count

            // Initialize Dragula if needed
            var dragulaContainer = document.getElementById('dragula-event-left');
            var drake = dragula([dragulaContainer]);

            // Handle drop event to update order via AJAX (if using Dragula)
            drake.on('drop', function(el, target, source, sibling) {
                var faqIds = [];
                $('#dragula-event-left .card').each(function() {
                    faqIds.push($(this).data('faq-id'));
                });

                $.ajax({
                    url: "{{ route('faqs.updateOrder') }}",
                    type: "POST",
                    data: {
                        _token: '{{ csrf_token() }}',
                        faqIds: faqIds
                    },
                    success: function(data) {
                        if (data.status === true) {
                            toastr.success(data.message, 'Success');
                            // loadFAQs();
                        } else {
                            toastr.error(data.message, 'Error');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed to update FAQ order.', 'Error');
                    }
                });
            });

            // Load FAQs on page load
            {{--loadFAQs();--}}

            {{--// Function to load FAQs via AJAX--}}
            {{--function loadFAQs() {--}}
            {{--    $.ajax({--}}
            {{--        url: "{{ route('faqs.load') }}",--}}
            {{--        type: "GET",--}}
            {{--        success: function(data) {--}}
            {{--            $('#dragula-event-left').empty();--}}
            {{--            if (data.length > 0) {--}}
            {{--                data.forEach(function(faq, index) {--}}
            {{--                    var listItem = '<div class="card rounded border mb-2" data-faq-id="' + faq.id + '">' +--}}
            {{--                        '<div class="card-body p-3">' +--}}
            {{--                        '<div class="media">' +--}}
            {{--                        '<i class="ti ti-pin-alt icon-sm text-primary align-self-center me-3"></i>' +--}}
            {{--                        '<div class="media-body">' +--}}
            {{--                        '<h6 class="mb-1">' + (index + 1) + '. ' + faq.question + '</h6>' +--}}
            {{--                        '<p class="mb-0 text-muted">' + faq.answer + '</p>' +--}}
            {{--                        '</div>' +--}}
            {{--                        '</div>' +--}}
            {{--                        '</div>' +--}}
            {{--                        '</div>';--}}
            {{--                    $('#dragula-event-left').append(listItem);--}}
            {{--                });--}}
            {{--            } else {--}}
            {{--                $('#dragula-event-left').append('<div class="card rounded border mb-2"><div class="card-body p-3">No FAQs found.</div></div>');--}}
            {{--            }--}}
            {{--        },--}}
            {{--        error: function(xhr, status, error) {--}}
            {{--            $('#dragula-event-left').empty();--}}
            {{--            $('#dragula-event-left').append('<div class="card rounded border mb-2"><div class="card-body p-3">Failed to load FAQs.</div></div>');--}}
            {{--        }--}}
            {{--    });--}}
            {{--}--}}

            // Function to handle form submission
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');
                $.ajax({
                    data: $('#faqForm').serialize(),
                    url: "{{ route('faqs.update', $faq->id) }}", // Update route URL
                    type: "PUT", // Use PUT method for update
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === true) {
                            toastr.success(data.message, 'Success');
                            // loadFAQs(); // Reload FAQs after updating FAQ
                            window.location.href = "{{ route('faqs.index') }}";
                        } else {
                            let formattedErrors = "<ul>";
                            $.each(data.message, function(index, error) {
                                formattedErrors += "<li>" + error + "</li>";
                            });
                            formattedErrors += "</ul>";
                            toastr.warning(formattedErrors, 'Warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        toastr.error('Failed to update FAQ.', 'Error');
                    },
                    complete: function() {
                        $('#saveBtn').html('Submit');
                    }
                });
            });

            // Add more FAQ entries dynamically
            $('#addBtn').click(function() {
                entryCount++; // Increment entry count
                var newEntry = '<div class="faq-entry mt-3">' +
                    '<div class="row">' +
                    '<div class="col-md-6">' +
                    '<div class="form-group">' +
                    '<input type="text" name="question[]" class="form-control" required />' +
                    '<label class="control-label">Question</label><i class="bar"></i>' +
                    '</div>' +
                    '</div>' +
                    '</div>' +
                    '<div class="row">' +
                    '<div class="col-md-6">' +
                    '<div class="form-group">' +
                    '<textarea name="answer[]" class="form-control" rows="2" required></textarea>' +
                    '<label class="control-label">Answer</label><i class="bar"></i>' +
                    '</div>' +
                    '</div>' +
                    '<div class="col-md-6">' +
                    '<button type="button" class="btn btn-danger removeBtn">Remove</button>' +
                    '</div>' +
                    '</div>' +
                    '</div>';
                $('#faqEntries').append(newEntry);
                updateEntryCount();
            });

            // Remove FAQ entry
            $(document).on('click', '.removeBtn', function() {
                $(this).closest('.faq-entry').remove();
                entryCount--; // Decrement entry count
                updateEntryCount();
            });

            // Function to update entry count display
            function updateEntryCount() {
                $('#entryCount').text(entryCount);
            }

            // Initial update of entry count display
            updateEntryCount();
        });

    </script>
@endsection
