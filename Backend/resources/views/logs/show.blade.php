@extends('layouts.app')

@section('page_title', 'Application Logs')

@section('content')
    <div class="container">
        <h2>Application Logs</h2>
        @if (session('status'))
            <div class="alert alert-success mt-2" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#text-logs">Text Logs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#json-logs">JSON Logs</a>
            </li>
        </ul>

        <div class="tab-content mt-3">
            <div id="text-logs" class="tab-pane fade show active">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('clear.logs') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Clear Log</button>
                        </form>
                        <pre style="white-space: pre-wrap; word-wrap: break-word;">{{ $content }}</pre>
                    </div>
                </div>
            </div>

            <div id="json-logs" class="tab-pane fade">
                <div class="card">
                    <div class="card-body">
                        <ul id="json-log-list" class="list-group">
                            <!-- JSON files will be listed here -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for displaying JSON file content -->
    <div class="modal fade" id="jsonLogModal" tabindex="-1" aria-labelledby="jsonLogModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jsonLogModalLabel">JSON File Content</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre id="json-log-content" style="white-space: pre-wrap; word-wrap: break-word;"></pre>
                </div>
            </div>
        </div>
    </div>
    <script>
        var $ = jQuery.noConflict();

        function fetchJsonLogs() {
            $.get('{{ route('json.log.list') }}', function(data) {
                const list = $('#json-log-list');
                list.empty(); // Clear previous items if any
                if (data.length === 0) {
                    list.append('<li class="list-group-item">No JSON logs found.</li>');
                } else {
                    data.forEach(file => {
                        list.append(`<li class="list-group-item">
                            <a href="#" class="view-json-file" data-file="${file}">${file}</a>
                        </li>`);
                    });
                }
            }).fail(function() {
                $('#json-log-list').append('<li class="list-group-item">Error loading JSON files.</li>');
            });
        }

        $(document).ready(function() {
            fetchJsonLogs();

            $('#json-log-list').on('click', '.view-json-file', function(e) {
                e.preventDefault();
                const fileName = $(this).data('file');
                $.get('{{ url('/view-json') }}/' + fileName, function(data) {
                    $('#json-log-content').text(JSON.stringify(data, null, 2));
                    $('#jsonLogModal').modal('show');
                }).fail(function() {
                    $('#json-log-content').text('Error loading file.');
                    $('#jsonLogModal').modal('show');
                });
            });
        });
    </script>
@endsection


