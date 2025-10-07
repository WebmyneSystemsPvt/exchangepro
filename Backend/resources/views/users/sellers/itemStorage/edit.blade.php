@extends('layouts.app')

@section('page_title', 'Create Item Storage')

@section('content')
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bootstrap-tagsinput { /* do not remove this css, it will apply when page is fully loaded. */
            width: 100% !important;
        }

        .bootstrap-tagsinput textarea {
            min-height: 100px; /* Set your desired height */
            resize: none; /* Prevent resizing */
        }
        .required-label::after {
            content: " *";
            color: red;
        }
        textarea {
            height: 5rem !important;
        }
        .card-custom {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-custom .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
            padding: 10px 15px;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
        }
        .card-custom .card-body {
            padding: 15px;
        }
        .remove-button {
            float: right;
        }

        .image-wrapper {
            position: relative;
            display: inline-block;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            transition: opacity 0.3s;
        }

        .image-wrapper:hover .overlay {
            opacity: 1;
        }

        .overlay button {
            color: white;
            border: none;
            background: none;
            cursor: pointer;
        }


    </style>
    <div class="container">
        <div class="row">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="container">
                <div class="row">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title">Edit Item Storage</h4>
                                <form id="userForm" method="POST" action="{{ route('store.item.storage') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="editId" id="editId" value="{{@$data->id}}">
                                    <input type="hidden" name="country" id="country" value="{{@$data->country}}">
                                    <input type="hidden" name="state" id="state" value="{{@$data->state}}">
                                    <input type="hidden" name="city" id="city" value="{{@$data->city}}">
                                    <input type="hidden" name="pincode" id="pincode" value="{{@$data->pincode}}">
                                    <input type="hidden" name="landmark" id="landmark" value="{{@$data->landmark}}">
                                    <input type="hidden" name="latitude" id="latitude" value="{{@$data->latitude}}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{@$data->longitude}}">
                                    <div class="row">
                                        <!-- Left Column -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="listing_type" class="control-label">Listing Name</label>
                                                <input type="text" name="listing_type" id="listing_type" class="form-control" placeholder="Enter listing Name" required value="{{@$data->listing_type}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="user_id" class="control-label">Seller</label>
                                                <select name="user_id" id="user_id" class="form-control" required>
                                                    <option value="">Select</option>
                                                    @foreach($sellers as $seller)
                                                        <option value="{{ $seller->id }}" {{$seller->id === $data->sellers->id ? 'selected' : ''}}>{{ $seller->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="categories_id" class="control-label">Category</label>
                                                <select name="categories_id" id="categories_id" class="form-control" required onchange="getSubCategories(this.value)">
                                                    <option value="">Select</option>
                                                    @foreach($category as $cat)
                                                        <option value="{{ $cat->id }}" {{$cat->id === $data->category->id ? 'selected' : ''}}>{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="sub_categories_id" class="control-label">Sub Category</label>
                                                <select name="sub_categories_id" id="sub_categories_id" class="form-control" required>
                                                    @foreach($subcategory as $cat)
                                                        <option value="{{ $cat->id }}" {{$cat->id === $data->subCategory->id ? 'selected' : ''}}>{{ $cat->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="item_id" class="control-label">Items</label><br>
                                                <select name="item_id[]" id="item_id" class="item_id" multiple required style="width: 100%;">
                                                    @foreach($items as $item)
                                                        <option value="{{ $item->id }}" {{ in_array($item->id, $itemsArray) ? 'selected' : '' }}>
                                                            {{ $item->item_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="default_storage_photo" class="control-label">Default Storage Photo (Single)</label>
                                                <input type="file" name="default_storage_photo" id="default_storage_photo" class="form-control" required/>
                                                @if(!empty($data->default_storage_photo))
                                                    <img id="preview_image" src="{{@$data->default_storage_photo}}" height="100px;" class="mt-1" width="100px;">
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="storage_photos" class="control-label">Storage Photos (Multiple)</label>
                                                <input type="file" name="storage_photos[]" id="storage_photos" class="form-control" multiple required/>
                                                <div id="preview_container">
                                                    @if(count($data->photos) > 0)
                                                        @foreach($data->photos as $photo)
                                                            <div class="image-wrapper">
                                                                <img src="{{@$photo->item_photo}}" height="100px;" class="mt-1" width="100px;">
                                                                <div class="overlay">
                                                                    <button class="btn btn-danger btn-remove" data-id="{{@$photo->id}}">
                                                                        <i class="fa fa-trash"></i> <!-- Trash icon -->
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rate" class="control-label">Rate in $</label>
                                                <input type="number" name="rate" id="rate" class="form-control" required placeholder="Enter rate" value="{{@$data->rate}}"/>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="rented_max_allow_days" class="control-label">Rented Max Allow Days</label>
                                                <input type="number" value="{{@$data->rented_max_allow_days}}" name="rented_max_allow_days" id="rented_max_allow_days" required class="form-control" placeholder="Enter maximum allowed rented days" />
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="blocked_days" class="control-label">Blocked Days</label>
                                                <input type="text" value="{{@$blockDaysArray}}" name="blocked_days" id="blocked_days" class="form-control" required readonly placeholder="Enter blocked days" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="exception_details" class="control-label">Exception Details</label>
                                                <textarea rows="4" name="exception_details" id="exception_details" class="form-control" required placeholder="Enter exception details">{{@$data->exception_details}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="description" class="control-label">Description</label>
                                                <textarea rows="4" name="description" id="description" class="form-control" required placeholder="Enter Description">{{@$data->description}}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="location" class="control-label">Location</label>
                                                <input type="text" name="location" id="location" class="form-control" placeholder="Enter location" required value="{{@$data->location}}" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="tags" class="control-label required-label">Tags</label>
                                                <textarea name="tags" id="tags" data-role="tagsinput" class="form-control" placeholder="Add tags here...">{{$tags}}</textarea>
                                                <small class="form-text text-muted">Type & Press enter to add multiple tags.</small>
                                            </div>
                                        </div>
                                        <div id="googleMap" style="width:100%;height:400px;"></div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Terms and Conditions -->
                                            <div id="terms_conditions_container" class="mt-3">
                                                <h5>Terms and Conditions</h5>
                                                @php $i = 1; @endphp
                                                @foreach($data->termsConditions as $terms_conditions)
                                                    <div class="card-custom term-condition-item" data-index="{{ $i }}">
                                                        <div class="card-header">
                                                            Term Condition {{ $i }}
                                                            <button type="button" class="btn btn-danger btn-sm remove-button remove-term-condition"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <label for="terms_conditions[{{ $i }}][title]" class="control-label required-label">Title</label>
                                                                <input type="text" name="terms_conditions[{{ $i }}][title]" class="form-control"  value="{{ $terms_conditions->title }}" readonly/>
                                                                <label for="terms_conditions[{{ $i }}][description]" class="control-label required-label">Description</label>
                                                                <textarea name="terms_conditions[{{ $i }}][description]" class="form-control"  readonly>{{ $terms_conditions->description }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php $i++; @endphp
                                                @endforeach
                                            </div>
                                            <button type="button" id="add_term_condition" class="btn btn-secondary btn-sm mt-2 add-button"><i class="fas fa-plus"></i> Add More Terms and Conditions</button>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <!-- Facility Offers -->
                                            <div id="facility_offers_container" class="mt-3">
                                                <h5>Facility Offers</h5>
                                                @php $j = 1; @endphp
                                                @foreach($data->facilityOffers as $facility_offers)
                                                    <div class="card-custom facility-offer-item">
                                                        <div class="card-header">
                                                            Facility Offer {{ $j }}
                                                            <button type="button" class="btn btn-danger btn-sm remove-button remove-facility-offer"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <label for="facility_offers[{{ $j }}][title]" class="control-label required-label">Title</label>
                                                                <input type="text" name="facility_offers[{{ $j }}][title]" class="form-control" readonly value="{{$facility_offers->title}}"/>
                                                                <label for="facility_offers[{{ $j }}][photo]" class="control-label required-label">Photo</label>
                                                                <input type="file" name="facility_offers[{{ $j }}][photo]" class="form-control" readonly value="{{$facility_offers->photo}}"/>
                                                                <label for="facility_offers[{{ $j }}][description]" class="control-label required-label">Description</label>
                                                                <textarea name="facility_offers[{{ $j }}][description]" class="form-control" readonly>{{$facility_offers->description}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @php $j++; @endphp
                                                @endforeach
                                            </div>
                                            <button type="button" id="add_facility_offer" class="btn btn-secondary btn-sm mt-2 add-button"><i class="fas fa-plus"></i> Add More Facility Offers</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <!-- Submit and Back Buttons -->
                                    <div class="button-container mt-3">
                                        <button type="button" class="btn btn-primary" id="saveBtn">Submit</button>
                                        <a href="{{ route('usersListing') }}" class="btn btn-danger">Back</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ config('constants.GOOGLE_MAP_API_KEY') }}&libraries=places&callback=initializeMap"></script>

    <script type="text/javascript">
        $('#default_storage_photo').on('change', function(event) {
            var file = event.target.files[0];
            var previewImage = $('#preview_image');

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.attr('src', e.target.result).show(); // Show the image
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.attr('src', '').hide(); // Hide the image if no file is selected
            }
        });

        $('#storage_photos').on('change', function(event) {
            var files = event.target.files;
            var previewContainer = $('#preview_container');

            $.each(files, function(index, file) {
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        var img = $('<img>').attr('src', e.target.result).css({
                            'height': '100px',
                            'width': '100px',
                            'margin-top': '10px'
                        });
                        var wrapper = $('<div>').addClass('image-wrapper');
                        var overlay = $('<div>').addClass('overlay');
                        var viewButton = $('<button>').addClass('btn btn-primary btn-view').text('View');
                        var removeButton = $('<button>').addClass('btn btn-danger btn-remove').text('Remove');

                        overlay.append(viewButton, removeButton);
                        wrapper.append(img, overlay);
                        previewContainer.append(wrapper);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        $(document).on('click', '.btn-remove', function() {
            var photoId = $(this).data('id');
            var $this = $(this);

            if (confirm('Are you sure you want to remove this photo?')) {
                $.ajax({
                    url: '/remove-single-item-storage-photo', // Update with your endpoint
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        id: photoId
                    },
                    success: function(response) {
                        if (response.success) {
                            $this.closest('.image-wrapper').remove();
                        } else {
                            alert('Error removing photo.');
                        }
                    },
                    error: function() {
                        alert('Error removing photo.');
                    }
                });
            }
        });


        $('#blocked_days').multiDatesPicker({
            altField: '#altField',
            defaultViewDate: { year: 2024, month: 0, day: 1 }, // Start from a specific month/year
            minDate: null, // Allow any date
            maxDate: null, // Allow any date
            onSelect: function(dateText, inst) {
                // Custom handling for selected dates if needed
            }
        });


        let map;
        let marker;
        let autocomplete;

        function initializeMap() {
            const latitude = {{ $data->latitude ?? '0' }};
            const longitude = {{ $data->longitude ?? '0' }};
            const initialPosition = new google.maps.LatLng(latitude, longitude);

            // Set map properties with initial position and zoom level
            const mapProp = {
                center: initialPosition,
                zoom: {{ config('constants.MAP_DEFAULT_ZOOM_SIZE') }}, // Initial zoom level
            };

            map = new google.maps.Map(document.getElementById("googleMap"), mapProp);

            // Initialize the marker
            marker = new google.maps.Marker({
                position: initialPosition,
                map: map,
                draggable: true
            });

            // Initialize the autocomplete
            autocomplete = new google.maps.places.Autocomplete(document.getElementById('location'), {
                types: ['geocode']
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (place.geometry) {
                    map.setCenter(place.geometry.location);
                    map.setZoom(15); // Zoom into the selected place
                    marker.setPosition(place.geometry.location);
                    document.getElementById('location').value = place.formatted_address;
                    updateHiddenFields(place);
                } else {
                    resetFields();
                }
            });

            google.maps.event.addListener(marker, 'dragend', function(event) {
                updateLocationFromLatLng(event.latLng.lat(), event.latLng.lng());
            });

            // Set zoom level based on the current center
            map.setZoom(15); // Adjust zoom level if needed
        }

        function updateHiddenFields(place) {
            const addressComponents = place.address_components;
            let country = '';
            let state = '';
            let city = '';
            let pincode = '';
            let landmark = '';

            addressComponents.forEach(component => {
                if (component.types.includes('country')) {
                    country = component.long_name;
                }
                if (component.types.includes('administrative_area_level_1')) {
                    state = component.long_name;
                }
                if (component.types.includes('locality')) {
                    city = component.long_name;
                }
                if (component.types.includes('postal_code')) {
                    pincode = component.long_name;
                }
                if (component.types.includes('route') || component.types.includes('neighborhood')) {
                    landmark = component.long_name;
                }
            });

            document.getElementById('country').value = country;
            document.getElementById('state').value = state;
            document.getElementById('city').value = city;
            document.getElementById('pincode').value = pincode;
            document.getElementById('landmark').value = landmark;
            document.getElementById('latitude').value = place.geometry.location.lat();
            document.getElementById('longitude').value = place.geometry.location.lng();
        }

        function updateLocationFromLatLng(lat, lng) {
            const geocoder = new google.maps.Geocoder();
            const latLng = new google.maps.LatLng(lat, lng);
            geocoder.geocode({'location': latLng}, function(results, status) {
                if (status === 'OK' && results[0]) {
                    document.getElementById('location').value = results[0].formatted_address;
                    updateHiddenFields(results[0]);
                } else {
                    resetFields();
                }
            });
        }

        function resetFields() {
            document.getElementById('location').value = '';
            document.getElementById('country').value = '';
            document.getElementById('state').value = '';
            document.getElementById('city').value = '';
            document.getElementById('pincode').value = '';
            document.getElementById('landmark').value = '';
            document.getElementById('latitude').value = '';
            document.getElementById('longitude').value = '';
        }

        // Initialize the map when the window loads
        window.onload = initializeMap;

    </script>


    <script type="text/javascript">
        $('#tags').tagsinput({
            tagClass: 'badge badge-primary',
            confirmKeys: [13, 44], // Enter and comma to add tags
            allowDuplicates: false, // Prevent duplicates
            trimValue: true // Optional: Trim spaces from tags
        });

        var tags = $('#tags').val();
        if (tags) {
            var tagArray = tags.split(',').map(function(tag) {
                return tag.trim();
            });
            tagArray.forEach(function(tag) {
                $('#tags').tagsinput('add', tag);
            });
        }

        $('input[required], select[required], textarea[required]').each(function() {
            var inputId = $(this).attr('id');
            if (inputId) {
                $('label[for="' + inputId + '"]').addClass('required-label');
            }
        });

        var options = {
            csvDispCount: 0,
            captionFormat: '{0} Selected',
            captionFormatAllSelected: '{0} selected!',
            nativeOnDevice: ['Android', 'BlackBerry', 'iPhone', 'iPad', 'iPod', 'Opera Mini', 'IEMobile', 'Silk'],
            okCancelInMulti: true,
        };

        $('.item_id').SumoSelect(options);

        var categoryText = $('#categories_id option:selected').text();
        if (categoryText.toLowerCase() === 'storage') {
            $('#item_id').closest('.form-group').show();
        } else {
            $('#item_id').closest('.form-group').hide();
        }

        function getSubCategories(categoryId) {
            $.ajax({
                url: '/get-subcategories/' + categoryId,
                type: 'GET',
                success: function (data) {
                    var options = '<option value="">Select</option>';
                    data.forEach(function (subcategory) {
                        options += '<option value="' + subcategory.id + '">' + subcategory.name + '</option>';
                    });
                    document.getElementById('sub_categories_id').innerHTML = options;

                    // Check if the selected category is 'Storage'
                    var categoryText = $('#categories_id option:selected').text();
                    if (categoryText.toLowerCase() === 'storage') {
                        $('#item_id').closest('.form-group').show();
                    } else {
                        $('#item_id').closest('.form-group').hide();
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                }
            });
        }

        $(document).ready(function() {
            let termCount = @json($data->termsConditions->count());
            let facilityCount = @json($data->facilityOffers->count()); // Initialize with 1 if you're starting with no facility offers.

            function addTermCondition() {
                let termHtml = `<div class="term-condition-item mt-2 card-custom">
                    <div class="card-header">
                        Term Condition ${termCount + 1}
                        <button type="button" class="btn btn-danger btn-sm remove-button remove-term-condition"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="terms_conditions[${termCount}][title]" class="control-label required-label">Title ${termCount + 1}</label>
                            <input type="text" name="terms_conditions[${termCount}][title]" class="form-control" required/>
                            <label for="terms_conditions[${termCount}][description]" class="control-label required-label">Description ${termCount + 1}</label>
                            <textarea name="terms_conditions[${termCount}][description]" class="form-control" required></textarea>
                        </div>
                    </div>
                </div>`;
                $('#terms_conditions_container').append(termHtml);
                termCount++;
            }

            function addFacilityOffer() {
                let facilityHtml = `<div class="facility-offer-item mt-2 card-custom">
                        <div class="card-header">
                            Facility Offer ${facilityCount + 1}
                            <button type="button" class="btn btn-danger btn-sm remove-button remove-facility-offer"><i class="fas fa-trash"></i></button>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="facility_offers[${facilityCount}][title]" class="control-label required-label">Title ${facilityCount + 1}</label>
                                <input type="text" name="facility_offers[${facilityCount}][title]" class="form-control" required/>
                                <label for="facility_offers[${facilityCount}][photo]" class="control-label required-label">Photo ${facilityCount + 1}</label>
                                <input type="file" name="facility_offers[${facilityCount}][photo]" class="form-control" required/>
                                <label for="facility_offers[${facilityCount}][description]" class="control-label required-label">Description ${facilityCount + 1}</label>
                                <textarea name="facility_offers[${facilityCount}][description]" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>`;
                $('#facility_offers_container').append(facilityHtml);
                facilityCount++;
            }

            $('#add_term_condition').click(addTermCondition);
            $('#add_facility_offer').click(addFacilityOffer);

            $(document).on('click', '.remove-term-condition', function() {
                $(this).closest('.term-condition-item').remove();
                termCount--;
            });

            $(document).on('click', '.remove-facility-offer', function() {
                $(this).closest('.facility-offer-item').remove();
                facilityCount--;
            });


            $('#saveBtn').click(function (e) {
                e.preventDefault();
                $(this).html('Sending..');
                $.ajax({
                    data: new FormData($('#userForm')[0]),
                    url: "{{ route('update.item.storage') }}",
                    type: "POST",
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        if (data.status === true) {
                            Swal.fire({
                                title: "Success!",
                                text: data.message,
                                icon: "success",
                            }).then(function() {
                                $('#userForm').trigger("reset");
                                window.location.href = "{{ route('seller.item.storage.list', ['seller_id' => ':seller_id']) }}".replace(':seller_id', data.seller_id);
                            });
                        } else {
                            let formattedErrors = "<ul>";
                            $.each(data.message, function(index, error) {
                                formattedErrors += "<li>" + error + "</li>";
                            });
                            formattedErrors += "</ul>";
                            Swal.fire({
                                title: "Warning!",
                                html: formattedErrors,
                                icon: "warning",
                            });
                        }
                    },
                    complete: function() {
                        $('#saveBtn').html('Submit');
                    }
                });
            });
        });

    </script>


@endsection
