<div class="sidebar border-end">
    <h5>Naims</h5>
    <button class="btn btn-light">
        <i class="bi bi-house-door"></i> Dashboard
    </button>
    <button class="btn btn-light">
        <i class="bi bi-wallet"></i> Accounting
    </button>
    <button class="btn btn-light {{ request()->routeIs('sales.index') ? 'active' : '' }}" 
        onclick="window.location.href='{{ route('sales.index') }}'">
    <i class="bi bi-person-badge"></i> Leads
</button>


    <button class="btn btn-light">
        <i class="bi bi-person-lines-fill"></i> Onboarding
    </button>
    <button class="btn btn-light">
        <i class="bi bi-file-earmark-lock"></i> Policy
    </button>
    <button class="btn btn-light">
        <i class="bi bi-telephone-fill"></i> Contact
    </button>
    <button class="btn btn-light">
        <i class="bi bi-chat-left-dots-fill"></i> Team Chat
    </button>
    <button class="btn btn-light">
        <i class="bi bi-graph-up-arrow"></i> Opportunity
    </button>
    <button class="btn btn-light">
        <i class="bi bi-gear-fill"></i> Settings
    </button>
</div>
