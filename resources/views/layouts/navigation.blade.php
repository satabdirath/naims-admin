<!-- resources/views/layouts/navigation.blade.php -->
<nav class="navbar bg-primary text-white px-4">
    <h5 class="mb-0 d-flex align-items-center">
        

        <span class="ms-auto">Hello, {{ Auth::user()->name ?? 'Guest' }} !</span>
    </h5>

    <div class="d-flex align-items-center ms-auto">
        <!-- User Dropdown -->
        <div class="d-flex align-items-center me-3">
            <!-- Notification Icon -->
            <div class="position-relative me-2">
                <i class="bi bi-bell" style="font-size: 1.5rem;"></i>
                <!-- Example notification badge -->
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                </span>
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <!-- Removed Profile from dropdown -->
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="dropdown-item">{{ __('Log Out') }}</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>



