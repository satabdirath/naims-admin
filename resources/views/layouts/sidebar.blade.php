<style>
    .sidebar {
        width: 250px;
        transition: width 0.3s;
    }

    .sidebar.collapsed {
        width: 85px;
    }

    .sidebar.collapsed h5,
    .sidebar.collapsed button span {
        display: none;
    }

    .menu-toggle {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
    }
</style>

<div class="sidebar border-end" id="sidebar">
    <div class="menu-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list" style="font-size: 1.5rem;"></i>
    </div>
    <h5>Naims</h5>
    <button class="btn btn-light {{ request()->routeIs('dashboard') ? 'active' : '' }}" 
        onclick="window.location.href='{{ route('dashboard') }}'">
        <i class="bi bi-house-door"></i> <span>Dashboard</span>
    </button>

    <button class="btn btn-light">
        <i class="bi bi-wallet"></i> <span>Accounting</span>
    </button>

    <button class="btn btn-light {{ request()->routeIs('sales.index') ? 'active' : '' }}" 
        onclick="window.location.href='{{ route('sales.index') }}'">
        <i class="bi bi-person-badge"></i> <span>Leads</span>
    </button>

    <button class="btn btn-light {{ request()->routeIs('onboarding.index') ? 'active' : '' }}" 
        onclick="window.location.href='{{ route('onboarding.index') }}'">
        <i class="bi bi-person-lines-fill"></i> <span>Onboarding</span>
    </button>

    <button class="btn btn-light">
        <i class="bi bi-file-earmark-lock"></i> <span>Policy</span>
    </button>

    <button class="btn btn-light {{ request()->routeIs('contact.index') ? 'active' : '' }}" 
        onclick="window.location.href='{{ route('contact.index') }}'">
        <i class="bi bi-telephone-fill"></i> <span>Contact</span>
    </button>

    <button class="btn btn-light {{ request()->routeIs('chats.index') ? 'active' : '' }}" 
        onclick="window.location.href='{{ route('chats.index') }}'">
        <i class="bi bi-chat-left-dots-fill"></i> <span>Chat</span>
    </button>

    <button class="btn btn-light">
        <i class="bi bi-graph-up-arrow"></i> <span>Opportunity</span>
    </button>
    <button class="btn btn-light">
        <i class="bi bi-gear-fill"></i> <span>Settings</span>
    </button>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('collapsed');
    }
</script>

