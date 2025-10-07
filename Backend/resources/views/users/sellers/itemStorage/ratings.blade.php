@extends('layouts.app')

@section('page_title', 'Item Storage Rating')

@section('content')
    <style>
        .review-item {
            position: relative;
        }
        .review-item .form-select {
            position: absolute;
            right: 15px;
            top: 30px;
            width: 150px; /* Adjust as needed */
        }
        .review-item .d-flex {
            justify-content: space-between;
        }
        .review-item .form-group {
            margin-bottom: 0;
        }
        .review-item .form-label {
            margin-bottom: 0;
            font-size: 0.875rem;
        }
        h4::before {
            content: '•';
            color: #f2ab27;
            display: inline-block;
            width: 1em;
            margin-left: -1em;
        }
        .card-body {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 5px;
        }
        .card {
            border: none;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #f2ab27;
            border-color: #f2ab27;
        }
        .btn-primary:hover {
            background-color: #f2ab27cc;
            border-color: #f2ab27cc;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
    <div class="container">
        <div class="row">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="my-1">Item Storage Ratings</h3>
                            <a href="#" class="btn btn-primary btn-sm" onclick="window.history.back();">Back</a>
                        </div>
                        <hr>

                        <!-- Item Storage Information -->
                        <div class="mb-4">
                            <h4>Item Storage Information</h4>
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <p><strong>Listing Name :</strong> {{ @$data->listing_type }}</p>
                                    <p><strong>Category :</strong> {{ @$data->category->name }}</p>
                                    <p><strong>Rate (Price) :</strong> {{ '$'.@$data->rate }}</p>
                                    <p><strong>Rented Max Allow Days:</strong> {{ @$data->rented_max_allow_days }}</p>
                                    <p><strong>Latitude :</strong> {{ @$data->latitude }}</p>
                                    <p><strong>Longitude :</strong> {{ @$data->longitude }}</p>
                                </div>
                                <div class="col-md-6 text-right">
                                    <p><strong>Status:</strong>
                                        <span class="badge {{ @$data->status == 1 ? 'badge-success' : 'badge-danger' }}">{{ @$data->status == 1 ? 'Active' : 'Inactive' }}</span></p>
                                    <p><strong> Sub Category :</strong> {{ @$data->subCategory->name }}<br></p>
                                    <p><strong> Full Location :</strong> {{ @$data->location }}<br></p>
                                    <p><strong> Country :</strong> {{ @$data->country }}<br></p>
                                    <p><strong> State :</strong> {{ @$data->state }}<br></p>
                                    <p><strong> City :</strong> {{ @$data->city }}<br></p>
                                    <p><strong> Pincode :</strong> {{ @$data->pincode }}<br></p>
                                    <p><strong> Landmark :</strong> {{ @$data->landmark }}<br></p>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div>
                            <h4>Reviews</h4>
                            @if($paginator->count() > 0)
                                @foreach($paginator as $review)
                                    <div class="review-item mb-3 border p-3 rounded">
                                        <div class="d-flex align-items-start">
                                            <img src="{{ @$review->seller->avatar }}" alt="{{ @$review->seller->name }}" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                                            <div class="w-100">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <div>
                                                        <h5 class="mb-1">{{ @$review->seller->name }}</h5>
                                                        <h6 class="mb-1">{{ @$review->title }}</h6>
                                                        <div class="d-flex align-items-center">
                                                            <span class="badge bg-warning text-dark me-2">{{ number_format(@$review->rating, 1) }}</span>
                                                            <span class="text-muted">{{ @$review->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="status_{{ @$review->id }}" class="form-label visually-hidden">Status</label>
                                                        <select id="status_{{ @$review->id }}" class="form-select status-select" data-review-id="{{ @$review->id }}">
                                                            <option value="0" {{ @$review->status === 0 ? 'selected' : '' }}>Inactive</option>
                                                            <option value="1" {{ @$review->status === 1 ? 'selected' : '' }}>Active</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <p class="mb-0">{{ @$review->description ?: 'No review text available.' }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Pagination Links -->
                                <div class="mt-4 float-end">
                                    {{ $paginator->links() }}
                                </div>
                            @else
                                <p>No reviews available.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Handle status change event
            $('.status-select').on('change', function() {
                var reviewId = $(this).data('review-id');
                var status = $(this).val();

                $.ajax({
                    url: '/update-review-status',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: reviewId,
                        status: status
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success('Review status updated successfully!');
                        } else {
                            toastr.error('Failed to update status.');
                        }
                    },
                    error: function() {
                        toastr.error('An error occurred.');
                    }
                });
            });
        });
    </script>
@endsection
