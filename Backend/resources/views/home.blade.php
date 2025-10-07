@extends('layouts.app')

@section('page_title', 'Home')

@section('content')
    <style>
        .card-rounded {
            border-radius: 15px;
            overflow: hidden; /* Ensure overflow is hidden to contain shadow */
        }
        .card-body {
            position: relative;
            padding-bottom: 2rem;
            background-color: rgba(255, 255, 255, 0.1); /* Semi-transparent background */
            backdrop-filter: blur(10px); /* Blur effect for glass */
            border: 1px solid rgba(255, 255, 255, 0.1); /* Border for separation */
        }
        .card-title-dash {
            font-size: 1.25rem;
            color: #fff; /* White text */
        }
        .status-summary-ight-white {
            color: rgba(255, 255, 255, 0.8);
        }
        .card-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.6);
        }
        .card h2 {
            font-size: 2.5rem;
            color: #fff; /* White text */
        }
        .text-white-font {
            color: white;
        }
        a.card-link {
            text-decoration: none;
            color: inherit;
        }
        .view-more-link {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 3px 8px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
        }
        .view-more-link:hover {
            background-color: rgba(255, 255, 255, 0.4);
        }
        .view-more-icon {
            margin-left: 5px;
        }
    </style>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <div class="row">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success card-rounded">
                <div class="card-body pb-0">
                    <i class="fas fa-users card-icon"></i>
                    <h4 class="card-title card-title-dash text-white mb-4">Total Active Borrowers</h4>
                    <p class="status-summary-ight-white mb-1">Value</p>
                    <h2 class="text-white-font">{{ \App\Models\User::where('status', 1)->whereHas('roles', function ($query) { $query->where('name', config('constants.BORROWER')); })->count() }}</h2>
                    <a href="{{ route('usersListing') }}" class="view-more-link"><span>View More</span> <i class="fas fa-arrow-right view-more-icon"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info card-rounded">
                <div class="card-body pb-0">
                    <i class="fas fa-store card-icon"></i>
                    <h4 class="card-title card-title-dash text-white mb-4">Total Active Sellers</h4>
                    <p class="status-summary-ight-white mb-1">Value</p>
                    <h2 class="text-white-font">{{ \App\Models\User::where('status', 1)->whereHas('roles', function ($query) { $query->where('name', config('constants.SELLER')); })->count() }}</h2>
                    <a href="{{ route('sellerusersListing') }}" class="view-more-link"><span>View More</span> <i class="fas fa-arrow-right view-more-icon"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning card-rounded">
                <div class="card-body pb-0">
                    <i class="fas fa-users-cog card-icon"></i>
                    <h4 class="card-title card-title-dash text-white mb-4">Active Group</h4>
                    <p class="status-summary-ight-white mb-1">Value</p>
                    <h2 class="text-white-font">{{ \App\Models\Groups::where('status', 1)->count() }}</h2>
                    <a href="{{ route('groupList') }}" class="view-more-link"><span>View More</span> <i class="fas fa-arrow-right view-more-icon"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary card-rounded">
                <div class="card-body pb-0">
                    <i class="fas fa-list-alt card-icon"></i>
                    <h4 class="card-title card-title-dash text-white mb-4">Category</h4>
                    <p class="status-summary-ight-white mb-1">Value</p>
                    <h2 class="text-white-font">{{ \App\Models\Category::count() }}</h2>
                    <a href="{{ route('categories.index') }}" class="view-more-link"><span>View More</span> <i class="fas fa-arrow-right view-more-icon"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary card-rounded">
                <div class="card-body pb-0">
                    <i class="fas fa-list-ul card-icon"></i>
                    <h4 class="card-title card-title-dash text-white mb-4">Sub Category</h4>
                    <p class="status-summary-ight-white mb-1">Value</p>
                    <h2 class="text-white-font">{{ \App\Models\SubCategory::count() }}</h2>
                    <a href="{{ route('subCategories.index') }}" class="view-more-link"><span>View More</span> <i class="fas fa-arrow-right view-more-icon"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success card-rounded">
                <div class="card-body pb-0">
                    <i class="fas fa-list-ul card-icon"></i>
                    <h4 class="card-title card-title-dash text-white mb-4">Total Listing</h4>
                    <p class="status-summary-ight-white mb-1">Value</p>
                    <h2 class="text-white-font">{{ \App\Models\ItemStorage::where('status',1)->count() }}</h2>
                    <a href="#" class="view-more-link"><span>View More</span> <i class="fas fa-arrow-right view-more-icon"></i></a>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-danger card-rounded">
                <div class="card-body pb-0">
                    <i class="fas fa-list-ul card-icon"></i>
                    <h4 class="card-title card-title-dash text-white mb-4">Pending Listing</h4>
                    <p class="status-summary-ight-white mb-1">Value</p>
                    <h2 class="text-white-font">{{ \App\Models\ItemStorage::where('status',0)->count() }}</h2>
                    <a href="{{ route('pending.item.storage.list') }}" class="view-more-link"><span>View More</span> <i class="fas fa-arrow-right view-more-icon"></i></a>
                </div>
            </div>
        </div>
    </div>
@endsection
