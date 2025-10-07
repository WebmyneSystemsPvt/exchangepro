@extends('layouts.app')

@section('page_title', 'Create FAQ')

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
                        <h4 class="card-title">Create FAQ</h4>
                        <form class="forms-sample material-form" id="faqForm">
                            @csrf
                            <div id="faqEntries">
                                <div class="faq-entry">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <input type="text" name="question[]" class="form-control" required />
                                                <label class="control-label">Question</label><i class="bar"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <textarea name="answer[]" class="form-control" rows="2" required></textarea>
                                                <label class="control-label">Answer</label><i class="bar"></i>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <button type="button" class="btn btn-danger removeBtn">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="button-container">
                                <button type="button" class="btn btn-success mt-3" id="addBtn">Add More</button>
                                <button type="button" class="btn btn-primary mt-3" id="saveBtn">Submit</button>
                                <a href="{{ route('faqs.index') }}" class="btn btn-danger mt-3" id="backBtn">Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            var entryCount = 1; // Initialize entry count

            // Handle form submission
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                $(this).html('Sending..');
                $.ajax({
                    data: $('#faqForm').serialize(),
                    url: "{{ route('faqs.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        if (data.status === true) {
                            toastr.success(data.message, 'Success');
                            // Redirect to index page
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
                        toastr.error('Failed to save FAQ.', 'Error');
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
