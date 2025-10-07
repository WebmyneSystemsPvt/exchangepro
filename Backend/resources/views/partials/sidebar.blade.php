<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item {{ request()->is('home') ? 'active' : '' }}">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="mdi mdi-grid-large menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item nav-category">Menu</li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#group-management" aria-expanded="false" aria-controls="group-management">
                <i class="menu-icon mdi mdi-google-circles-group"></i>
                <span class="menu-title">Groups</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="group-management">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('groupList') ? 'active' : '' }}" href="{{ route('groupList') }}">Groups List</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#pages-management" aria-expanded="false" aria-controls="pages-management">
                <i class="menu-icon mdi mdi-book-open-page-variant"></i>
                <span class="menu-title">Pages</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="pages-management">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('faqs.index') ? 'active' : '' }}" href="{{ route('faqs.index') }}">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('testimonials.index') ? 'active' : '' }}" href="{{ route('testimonials.index') }}">Testimonials</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#pending-listing" aria-expanded="false" aria-controls="pending-listing">
                <i class="menu-icon mdi mdi-playlist-play"></i>
                <span class="menu-title">Pending Listing</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="pending-listing">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('pending.item.storage.list') ? 'active' : '' }}" href="{{ route('pending.item.storage.list') }}">Pending Listing</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#user-management" aria-expanded="false" aria-controls="user-management">
                <i class="menu-icon mdi mdi-account-circle-outline"></i>
                <span class="menu-title">User Management</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="user-management">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('usersListing') ? 'active' : '' }}" href="{{ route('usersListing') }}">Borrowers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('sellerusersListing') ? 'active' : '' }}" href="{{ route('sellerusersListing') }}">Seller</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#masters" aria-expanded="false" aria-controls="masters">
                <i class="menu-icon mdi mdi-briefcase-check"></i>
                <span class="menu-title">Masters</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="masters">
                <ul class="nav flex-column sub-menu">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('itemsData') ? 'active' : '' }}" href="{{ route('items.index') }}">Items</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('banners') ? 'active' : '' }}" href="{{ route('banners.index') }}">Banner Slider</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('gendersData') ? 'active' : '' }}" href="{{ route('genders.index') }}">Gender</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('languagesData') ? 'active' : '' }}" href="{{ route('languages.index') }}">Language</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('categoriesData') ? 'active' : '' }}" href="{{ route('categories.index') }}">Category</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('subCategoriesData') ? 'active' : '' }}" href="{{ route('subCategories.index') }}">Sub Category</a>
                    </li>
                </ul>
            </div>
        </li>

        <li class="nav-item">
            <a class="nav-link" href="{{ url('/settings') }}">
                <i class="menu-icon mdi mdi-file-document"></i>
                <span class="menu-title">Settings</span>
            </a>
        </li>
    </ul>
</nav>
