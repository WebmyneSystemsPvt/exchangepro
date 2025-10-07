@extends('layouts.app')

@section('page_title', 'Item Storage')

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
        .table {
            margin-top: 15px;
            background-color: #fff;
            border-radius: 5px;
            overflow: hidden;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .img-thumbnail {
            border-radius: 5px;
        }
        .image-popup {
            margin: 5px;
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
                            <h3 class="my-1">Item Storage Request</h3>
                            <a href="#" class="btn btn-primary btn-sm" onclick="window.history.back();">Back</a>
                        </div>
                        <hr>

                        <!-- Accordion -->
                        <div class="accordion" id="accordionExample">
                            <!-- Item Storage Information -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        Item Storage Information
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
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
                                </div>
                            </div>

                            <!-- Seller Information -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                        Seller Information
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @if(isset($data->sellers))
                                            <p><strong>Seller Name:</strong> {{ @$data->sellers->name }}</p>
                                            <p><strong>Seller Email:</strong> {{ @$data->sellers->email }}</p>
                                        @else
                                            <p>No seller information available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Blocked Days Information -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingBlockDays">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBlockDays" aria-expanded="true" aria-controls="collapseBlockDays">
                                        Blocked Days Information
                                    </button>
                                </h2>
                                <div id="collapseBlockDays" class="accordion-collapse collapse show" aria-labelledby="headingBlockDays" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @php $i = 0; @endphp
                                        @if(isset($data->itemStorageBlockDays) && count($data->itemStorageBlockDays) > 0)
                                            @foreach($data->itemStorageBlockDays as $blockDays)
                                                @php $i++; @endphp
                                                <p><strong>Blocked Days Date {{$i}}:</strong> {{ $blockDays->block_days_date }}</p>
                                            @endforeach
                                        @else
                                            <p>No blocked days information available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Item Storage Photos -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                        Item Default Storage Photo / Item Storage Photos
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-12 mb-4">
                                                <h5>Item Default Storage Photo</h5>
                                                <div class="d-flex flex-wrap">
                                                    @if(!empty($data->default_storage_photo))
                                                        <a href="{{ $data->default_storage_photo }}" class="image-popup">
                                                            <img src="{{ $data->default_storage_photo }}" class="img-thumbnail me-2 mb-2" style="height: 120px; width: 120px;">
                                                        </a>
                                                    @else
                                                        <p>No default storage photo available.</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <h5>Item Storage Photos</h5>
                                                <div class="d-flex flex-wrap">
                                                    @if(isset($data->photos) && count($data->photos) > 0)
                                                        @foreach($data->photos as $photo)
                                                            <a href="{{ $photo->item_photo }}" class="image-popup">
                                                                <img src="{{ $photo->item_photo }}" class="img-thumbnail me-2 mb-2" style="height: 120px; width: 120px;">
                                                            </a>
                                                        @endforeach
                                                    @else
                                                        <p>No storage photos available.</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                        Items
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse show" aria-labelledby="headingFour" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @if(isset($data->items) && count($data->items) > 0)
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Item Name</th>
                                                    <th>Item Description</th>
                                                    <th>Item Weight</th>
                                                    <th>Item Photo</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($data->items as $item)
                                                    <tr>
                                                        <td>{{ @$item->item_name }}</td>
                                                        <td>{{ @$item->item_description }}</td>
                                                        <td>{{ @$item->item_weight }}</td>
                                                        <td><img src="{{ @$item->item_photo }}" class="img-thumbnail" style="height: 50px; width: 50px;"></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p>No items available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Facilities Offers -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                                        Facilities Offers
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse show" aria-labelledby="headingFive" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @if(isset($data->facilityOffers) && count($data->facilityOffers) > 0)
                                            <table class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Description</th>
                                                    <th>Logo</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($data->facilityOffers as $item)
                                                    <tr>
                                                        <td>{{ @$item->title }}</td>
                                                        <td>{{ @$item->description }}</td>
                                                        <td><img src="{{ @$item->photo }}" class="img-thumbnail" style="height: 50px; width: 50px;"></td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p>No facilities offers available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Terms & Conditions -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSix">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="true" aria-controls="collapseSix">
                                        Terms & Conditions
                                    </button>
                                </h2>
                                <div id="collapseSix" class="accordion-collapse collapse show" aria-labelledby="headingSix" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @if(isset($data->termsConditions) && count($data->termsConditions) > 0)
                                            @foreach($data->termsConditions as $termsCondition)
                                                <p><strong>{{@$termsCondition->title}} : </strong> {{ @$termsCondition->description }}</p>
                                            @endforeach
                                        @else
                                            <p>No terms & conditions available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTags">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTags" aria-expanded="true" aria-controls="collapseTags">
                                        Tags
                                    </button>
                                </h2>
                                <div id="collapseTags" class="accordion-collapse collapse show" aria-labelledby="headingTags" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @if(isset($data->tags) && count($data->tags) > 0)
                                            @foreach($data->tags as $tag)
                                                <span class="badge badge-primary rounded-1">{{ @$tag->tag_name ?: 'N/A' }}</span>
                                            @endforeach
                                        @else
                                            <p>No tags available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Reviews -->
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingReviews">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseReviews" aria-expanded="true" aria-controls="collapseReviews">
                                        Reviews
                                    </button>
                                </h2>
                                <div id="collapseReviews" class="accordion-collapse collapse show" aria-labelledby="headingReviews" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        @if(isset($data->ratings) && count($data->ratings) > 0)
                                            @foreach($data->ratings as $review)
                                                <div class="review-item mb-3 border p-3 rounded">
                                                    <div class="d-flex align-items-start">
                                                        <img src="{{ @$review->seller->avatar }}" alt="{{ @$review->seller->name }}" class="rounded-circle me-3" style="width: 50px; height: 50px;">
                                                        <div class="w-100">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <div>
                                                                    <h5 class="mb-1">{{ @$review->seller->name }}</h5>
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="badge bg-warning text-dark me-2">{{ number_format(@$review->rating, 1) }}</span>
                                                                        <span class="text-muted">{{ @$review->created_at->diffForHumans() }}</span>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="status_{{ @$review->id }}" class="form-label visually-hidden">Status</label>
                                                                    <select id="status_{{ @$review->id }}" class="form-select status-select" data-review-id="{{ @$review->id }}">
                                                                        <option value="0" {{ @$review->status === 0 ? 'selected' : '' }}>Pending</option>
                                                                        <option value="1" {{ @$review->status === 1 ? 'selected' : '' }}>Approved</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <p class="mb-0">{{ @$review->description ?: 'No review text available.' }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p>No reviews available.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
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
