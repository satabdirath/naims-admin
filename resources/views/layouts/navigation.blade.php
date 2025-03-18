<nav class="navbar bg-primary text-white px-4 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">
        <span>Hello, {{ Auth::user()->name ?? 'Guest' }}!</span>
    </h5>

    <!-- Search Box -->
    <div class="position-relative w-25">
        <input type="text" class="form-control" id="searchInput" placeholder="Search...">
        <div id="searchResults" class="position-absolute bg-white border rounded w-100 d-none"
            style="max-height: 200px; overflow-y: auto; z-index: 1050;"></div>
    </div>

    <div class="d-flex align-items-center">
        <button class="btn btn-success me-3" id="addLeadBtn" data-bs-toggle="modal" data-bs-target="#addLeadModal">Add Leads</button>

        <!-- Notification Icon -->
      <!-- Notification Icon -->
<div class="position-relative me-3">
    <i class="bi bi-bell" id="notificationIcon" style="font-size: 1.5rem; cursor: pointer;"></i>
    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notificationBadge">0</span>

    <!-- Notification Popup -->
    <div id="notificationPopup" class="position-absolute bg-white text-dark p-3 shadow rounded d-none"
        style="top: 40px; right: 0px; width: 250px; z-index: 1050;">
        <h6>Notifications</h6>
        <ul class="list-unstyled" id="notificationList">
            <li class="text-muted text-center">No new notifications</li>
        </ul>
        <button class="btn btn-sm btn-danger w-100 mt-2 d-none" id="clearNotifications">Clear All</button>
    </div>
</div>


        <!-- User Profile Dropdown -->
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Log Out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Add Lead Modal -->
<div class="modal fade" id="addLeadModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="leadName" placeholder="Name" class="form-control mb-2">
                <input type="text" id="leadMobile" placeholder="Mobile Number" class="form-control mb-2">
                <input type="email" id="leadEmail" placeholder="Email" class="form-control mb-2">
                <input type="text" id="leadAddress" placeholder="Address" class="form-control mb-2">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="saveLead">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
$(document).ready(function () {
    // ✅ Fix Bootstrap Dropdown Issue
    $('#userDropdown').on('click', function (event) {
        event.stopPropagation(); // Prevent immediate closing
    });

    // Close dropdown when clicking outside
    $(document).on('click', function (event) {
        if (!$(event.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });

    // ✅ Ensure Logout Works Properly
    $('.dropdown-menu form').on('click', function (event) {
        event.stopPropagation(); // Prevent dropdown from closing before submitting
    });

    // ✅ Live Search Feature
    $('#searchInput').on('keyup', function () {
        let query = $(this).val().trim();

        if (query.length < 2) {
            $('#searchResults').addClass('d-none');
            return;
        }

        $.ajax({
            url: "{{ route('search') }}",
            method: "GET",
            data: { query: query },
            success: function (data) {
                let results = data.results;
                let html = '';

                if (results.length === 0) {
                    html = '<div class="p-2 text-muted">No results found</div>';
                } else {
                    results.forEach(item => {
                        html += `<div class="p-2 border-bottom search-item" style="cursor: pointer; color: black;" data-value="${item.name}">
                                    <strong>${item.name}</strong> <br>
                                    <small>${item.mobile_number} | ${item.email}</small>
                                 </div>`;
                    });
                }

                $('#searchResults').html(html).removeClass('d-none');
            }
        });
    });

    // Select search item
    $(document).on('click', '.search-item', function () {
        $('#searchInput').val($(this).data('value'));
        $('#searchResults').addClass('d-none');
    });

    // Close search results when clicking outside
    $(document).on('click', function (event) {
        if (!$(event.target).closest('#searchInput, #searchResults').length) {
            $('#searchResults').addClass('d-none');
        }
    });


});
</script>
<script>
    $(document).ready(function () {
    $('#saveLead').on('click', function () {
        let name = $('#leadName').val().trim();
        let mobile = $('#leadMobile').val().trim();
        let email = $('#leadEmail').val().trim();
        let address = $('#leadAddress').val().trim();

        if (!name || !mobile) {
            Swal.fire('Error!', 'Name and Mobile are required!', 'error');
            return;
        }

        $.ajax({
            url: "{{ route('leads.store') }}",
            method: "POST",
            data: {
                name: name,
                mobile_number: mobile,
                email: email,
                address: address,
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                Swal.fire('Success!', response.message, 'success').then(() => {
                    location.reload(); // Refresh page after adding lead
                });
            },
            error: function (xhr) {
    let response = xhr.responseJSON;
    let message = response && response.error ? response.error : 'Something went wrong!';
    Swal.fire('Error!', message, 'error');
}

        });
    });
});

</script>
<script>
    $(document).ready(function () {
    function fetchNotifications() {
        $.ajax({
            url: "{{ route('notifications.fetch') }}", // Replace with your Laravel route
            method: "GET",
            dataType: "json",
            success: function (response) {
                let notifications = response.notifications;
                let badge = $('#notificationBadge');
                let list = $('#notificationList');
                let clearBtn = $('#clearNotifications');
                list.empty();

                if (notifications.length > 0) {
                    notifications.forEach(notification => {
                        list.append(`<li class="border-bottom py-1">${notification.message}</li>`);
                    });
                    badge.text(notifications.length).removeClass('d-none');
                    clearBtn.removeClass('d-none');
                } else {
                    list.html('<li class="text-muted text-center">No new notifications</li>');
                    badge.addClass('d-none');
                    clearBtn.addClass('d-none');
                }
            }
        });
    }

    // Fetch notifications every 10 seconds
    setInterval(fetchNotifications, 10000);
    fetchNotifications();

    // Show/hide notification popup
    $('#notificationIcon').click(function (e) {
        e.stopPropagation();
        $('#notificationPopup').toggleClass('d-none');
    });

    $(document).click(function (event) {
        if (!$(event.target).closest('#notificationIcon, #notificationPopup').length) {
            $('#notificationPopup').addClass('d-none');
        }
    });

    // Clear notifications
    $('#clearNotifications').click(function () {
        $.ajax({
            url: "{{ route('notifications.clear') }}", // Replace with your Laravel route
            method: "POST",
            data: { _token: "{{ csrf_token() }}" },
            success: function () {
                $('#notificationList').html('<li class="text-muted text-center">No new notifications</li>');
                $('#notificationBadge').addClass('d-none');
                $('#clearNotifications').addClass('d-none');
            }
        });
    });
});

</script>





