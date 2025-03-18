@extends('layouts.app')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sales.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


  
@endpush

@section('content')
<div class="container-fluid">
    <div class="content">
        <div class="d-flex justify-content-between mb-3">
            <h3>Onboarding</h3>
        </div>

        <div class="d-flex align-items-center gap-2">

    <button id="bulkEditBtn" class="btn btn-primary">Bulk Edit</button>
    <button id="deleteSelectedBtn" class="btn btn-danger d-none">Delete</button>

    <!-- Change Stage Dropdown -->
    <div class="dropdown d-none" id="stageFilterContainer">
        <button class="btn btn-outline-secondary dropdown-toggle" id="stageFilterBtn" data-bs-toggle="dropdown">
             Stage
        </button>
        <ul class="dropdown-menu" id="stageDropdown">
            @foreach($stages->whereBetween('id', [1, 6]) as $stage)
                <li>
                    <a class="dropdown-item change-stage" href="#" data-stage-id="{{ $stage->id }}">
                        {{ $stage->name }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Assign To Dropdown -->
    <div class="dropdown d-none" id="assignedFilterContainer">
        <button class="btn btn-outline-secondary dropdown-toggle" id="assignedFilterBtn" data-bs-toggle="dropdown">
            Assign
        </button>
        <ul class="dropdown-menu" id="assignedDropdown">
            @foreach($users as $user)
                <li>
                    <a class="dropdown-item assign-user" href="#" data-user-id="{{ $user->id }}">
                        {{ $user->name }}
                    </a>
                </li>
            @endforeach
        </ul>

</div>


            <div class="vr mx-2"></div>
            <div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" id="sourceFilterBtn" data-bs-toggle="dropdown">
        Source
    </button>
    <ul class="dropdown-menu p-3" id="sourceDropdown">
        @foreach($sources as $source)
            @if($source) <!-- To avoid null sources -->
            <li>
                <input type="checkbox" class="source-checkbox" value="{{ $source }}" id="source-{{ $loop->index }}">
                <label for="source-{{ $loop->index }}">{{ ucfirst($source) }}</label>
            </li>
            @endif
        @endforeach
        <li class="mt-2">
            <button class="btn btn-sm btn-primary w-100" id="applySourceFilter">Apply</button>
        </li>
    </ul>
</div>


            <!-- Stage Filter Button -->
<div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" id="stageFilterBtn" data-bs-toggle="dropdown">
        Stage
    </button>
    <ul class="dropdown-menu p-3" id="stageDropdown">
    @foreach($stages->whereBetween('id', [6, 15]) as $stage)

            <li>
                <input type="checkbox" class="stage-checkbox" value="{{ $stage->id }}" id="stage-{{ $stage->id }}">
                <label for="stage-{{ $stage->id }}">{{ $stage->name }}</label>
            </li>
        @endforeach
        <li class="mt-2">
            <button class="btn btn-sm btn-primary w-100" id="applyStageFilter">Apply</button>
        </li>
    </ul>
</div>

         <!-- Assigned To Filter -->
<div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" id="assignedFilterBtn" data-bs-toggle="dropdown">
        Assigned To
    </button>
    <ul class="dropdown-menu p-3" id="assignedDropdown">
        @foreach($users as $user)
            <li>
                <input type="checkbox" class="assigned-checkbox" value="{{ $user->id }}" id="assigned-{{ $user->id }}">
                <label for="assigned-{{ $user->id }}">{{ $user->name }}</label>
            </li>
        @endforeach
        <li class="mt-2">
            <button class="btn btn-sm btn-primary w-100" id="applyAssignedFilter">Apply</button>
        </li>
    </ul>
</div>

<!-- Date Filter -->
<button class="btn btn-outline-secondary" id="dateFilterBtn">Date</button>
<input type="date" id="datePicker" class="d-none">



            <button class="btn btn-outline-secondary">Activity</button>
        </div>

    <!-- Pipeline View -->
    <!-- Pipeline View -->
<div id="pipelineView" class="d-block">
    <br><h6>Pipeline View</h6>
    <div class="pipeline-container">
        @foreach($stages->whereBetween('id', [6, 15]) as $stage)
            <div class="pipeline-stage border p-3">
                <h6 class="d-flex align-items-center">
                    {{ $stage->name }} 
                    <span class="text-success fw-bold">({{ $stage->leads->count() }})</span>
                    
                    <!-- Info Icon -->
                   <div class="info-icon" data-tooltip="
    @if($stage->id == 6) Onboarding stage details... 
    @elseif($stage->id == 7) Design stage details...
    @elseif($stage->id == 8) P&O stage details...
    @elseif($stage->id == 9) C&SW stage details...
    @elseif($stage->id == 10) C&CW stage details...
    @elseif($stage->id == 11) P&F stage details...
    @else No details available.
    @endif
">
    i
</div>

                </h6>
                <ul class="list-unstyled lead-list" data-stage-id="{{ $stage->id }}">
                    @foreach($stage->leads as $lead)
                        <li class="border p-2 my-2 lead-card" 
                            data-lead-id="{{ $lead->id }}" 
                            data-name="{{ $lead->name }}"  
                            draggable="true">
                            {{ $lead->name }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>


        <!-- Table View -->
        <div id="tableView" class="d-none">
            <br><h6>Table View</h6>
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th><input type="checkbox" id="selectAllCheckbox" class="d-none"></th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Stage</th>
                        <th>Source</th>
                        <th>Assigned To</th>
                        <th>Remember</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Leads will be populated here -->
                </tbody>
            </table>
        </div>
    </div>


    <!-- Details Panel -->
    <div id="detailsPanel" class="details-panel">
        <span class="close-icon" id="closeDetails"><i class="fas fa-times"></i></span>
        <div class="details-content">
            <p><span id="detailName"></span> <a href="#" class="activity-link" id="viewActivity">View Activity</a></p>
            <div class="horizontal-icons">
                <i class="fas fa-phone" title="Call"></i>
                <i class="fas fa-comment" title="Message"></i>
                <i class="fas fa-envelope" title="Email"></i>
                <i class="fab fa-whatsapp" title="WhatsApp"></i>
            </div>
            <a href="#" class="form-link" id="viewFormSubmission">View Form Submission</a>
            <textarea class="note-textarea" id="note" placeholder="Enter notes here..."></textarea>

            <div class="vertical-icons">
    <h6>Logs</h6>
    <div class="d-flex flex-column align-items-start gap-1">
        <p class="mb-1"><i class="fas fa-phone" title="Call Log"></i> Call Log</p>
        <p class="mb-1"><i class="fab fa-whatsapp" title="WhatsApp Log"></i> WhatsApp Log</p>
        <p class="mb-1"><i class="fas fa-envelope" title="Mail Log"></i> Mail Log</p>
    </div>
</div>


            <textarea class="task-textarea" id="task" placeholder="Enter task details..."></textarea>
            <div class="recent-activity mt-3">
            <h6>Recent Activity</h6>
            <ul id="activityList" class="list-unstyled">
                <li>No recent activity</li>
            </ul>
        </div>
            <button class="btn btn-sm btn-primary save-btn" id="saveBtn">Save</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Include SweetAlert2
    $.getScript("https://cdn.jsdelivr.net/npm/sweetalert2@11");

    $(document).on('change', '.stage-dropdown', function () {
        let leadId = $(this).data('lead-id');
        let stageId = $(this).val();

        $.ajax({
            url: "{{ route('leads.updateStage') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                lead_id: leadId,
                stage_id: stageId
            },
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Stage updated successfully!',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });

 

    // Update Assigned User
    $(document).on('change', '.assign-dropdown', function () {
        let leadId = $(this).data('lead-id');
        let assignedTo = $(this).val();

        $.ajax({
            url: "{{ route('leads.updateAssigned') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                lead_id: leadId,
                assigned_to: assignedTo
            },
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: 'Assigned user updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    });

    function fetchLeads() {
    $.ajax({
        url: "{{ route('leads.fetch') }}",
        method: "GET",
        success: function (response) {
            let tableBody = '';
            response.leads.forEach(lead => {
                // Stage options dropdown
                let stageOptions = response.stages.map(stage =>
                    `<option value="${stage.id}" ${lead.stage_id == stage.id ? 'selected' : ''}>${stage.name}</option>`
                ).join('');

                // User options dropdown with default "Select" option
                let userOptions = `<option value="" ${lead.assigned_to == null ? 'selected' : ''}>Select</option>`;
                userOptions += response.users.map(user =>
                    `<option value="${user.id}" ${lead.assigned_to == user.id ? 'selected' : ''}>${user.name}</option>`
                ).join('');

                tableBody += `
                    <tr>
                     <td><input type="checkbox" class="lead-checkbox d-none" data-lead-id="${lead.id}"></td>
                        <td>${new Date(lead.created_at).toLocaleDateString()}</td>
                        <td>${lead.name}</td>
                        <td>
                            <select class="form-select stage-dropdown" data-lead-id="${lead.id}">
                                ${stageOptions}
                            </select>
                        </td>
                        <td>${lead.source || 'N/A'}</td>
                        <td>
                            <select class="form-select assign-dropdown" data-lead-id="${lead.id}">
                                ${userOptions}
                            </select>
                        </td>
                        <td>${lead.remember || 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-info viewBtn"
                            data-lead-id="${lead.id}"
                                data-name="${lead.name}"
                                data-status="${lead.stage_name || 'N/A'}"
                                data-assigned="${lead.assigned_to || 'Unassigned'}"
                                data-date="${lead.created_at}">View</button>
                        </td>
                    </tr>`;
            });
            $('#tableBody').html(tableBody);
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Error fetching leads data',
            });
        }
    });
}

$(document).on('click', '.viewBtn, .lead-card', function () {
    let leadId = $(this).attr('data-lead-id'); // Get lead ID
    let name = $(this).attr('data-name') || 'N/A';
    let status = $(this).attr('data-status') || 'N/A';
    let assignedTo = $(this).attr('data-assigned') || 'Unassigned';
    let date = $(this).attr('data-date') || 'N/A';

    $('#detailName').text(name);
    $('#detailsPanel').data('lead-id', leadId); // Store lead ID in detailsPanel
    $('#detailsPanel').fadeIn();

    // ** Fetch and Display Notes & Tasks **
    $.ajax({
        url: "{{ route('leads.getDetails') }}", // Create this route in backend
        method: "GET",
        data: { lead_id: leadId },
        success: function (response) {
            $('#note').val(response.note || ''); // Populate note field
            $('#task').val(response.task || ''); // Populate task field
        },
        error: function () {
            Swal.fire('Error!', 'Could not fetch lead details.', 'error');
        }
    });
});


    $('#closeDetails').click(function () {
        $('#detailsPanel').fadeOut();
    });

    $('#pipelineBtn').click(() => {
        $('#tableView').addClass("d-none");
        $('#pipelineView').removeClass("d-none");

        $('#pipelineBtn').removeClass("btn-secondary").addClass("btn-primary");
        $('#tableBtn').removeClass("btn-primary").addClass("btn-secondary");
    });

    $('#tableBtn').click(() => {
        $('#pipelineView').addClass("d-none");
        $('#tableView').removeClass("d-none");

        $('#tableBtn').removeClass("btn-secondary").addClass("btn-primary");
        $('#pipelineBtn').removeClass("btn-primary").addClass("btn-secondary");
    });

    fetchLeads();
});
</script>

<script>
    $(document).ready(function () {
        let pipelineStages = $(".lead-list");

        pipelineStages.each(function () {
            new Sortable(this, {
                group: "shared",
                animation: 150,
                onEnd: function (evt) {
                    let leadId = $(evt.item).data("lead-id");
                    let newStageId = $(evt.to).data("stage-id");

                    // Send AJAX request using jQuery
                    $.ajax({
                        url: "{{ route('leads.updateStage') }}",  // Ensure this route is named correctly in web.php
                        method: "POST",
                        data: {
                            lead_id: leadId,
                            stage_id: newStageId,
                            _token: "{{ csrf_token() }}"  // CSRF token for Laravel
                        },
                        success: function (response) {
                            console.log("Lead updated successfully:", response);
                        },
                        error: function (xhr) {
                            console.error("Error updating lead:", xhr.responseText);
                        }
                    });
                }
            });
        });
    });
</script>

<!--filter stages --->

<script>

$('#applyStageFilter').click(function () {
    let selectedStages = [];

    $('.stage-checkbox:checked').each(function () {
        selectedStages.push($(this).val()); // Collect checked values
    });

    // If no checkboxes are selected, send an empty array (to fetch all data)
    if (selectedStages.length === 0) {
        selectedStages = []; 
    }

    $.ajax({
        url: "{{ route('leads.filterByStage') }}",
        method: "GET",
        data: { stages: selectedStages },  
        dataType: "json",
        success: function (response) {
    let tableBody = '';

    if (response.leads.length === 0) {
        tableBody = '<tr><td colspan="7" class="text-center">No leads found.</td></tr>';
    } else {
        response.leads.forEach(lead => {
            tableBody += `
                <tr>
                    <td>${new Date(lead.created_at).toLocaleDateString()}</td>
                    <td>${lead.name}</td>
                    <td>${lead.stage ? lead.stage.name : 'N/A'}</td> <!-- ✅ Fixed -->
                    <td>${lead.source || 'N/A'}</td>
                    <td>${lead.assigned_to ? lead.assigned_to.name : 'Unassigned'}</td> <!-- ✅ Fixed -->
                    <td>${lead.remember || 'N/A'}</td>
                    <td>
                        <button class="btn btn-sm btn-info viewBtn" 
                            data-name="${lead.name}"
                            data-status="${lead.stage ? lead.stage.name : 'N/A'}"
                            data-assigned="${lead.assigned_to ? lead.assigned_to.name : 'Unassigned'}"
                            data-date="${lead.created_at}">View</button>
                    </td>
                </tr>`;
        });
    }
    $('#tableBody').html(tableBody);
},
        error: function (xhr) {
            console.error(xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Could not filter data.',
            });
        }
    });
});


    </script>


<script>
    
    $('#applySourceFilter').on('click', function () {
    let selectedSources = [];
    $('.source-checkbox:checked').each(function () {
        selectedSources.push($(this).val());
    });

    fetchLeads(selectedSources);
});

function fetchLeads(sources = []) {
    $.ajax({
        url: "{{ route('leads.fetch') }}",
        method: "GET",
        data: { sources: sources },
        success: function (response) {
            // Render table rows with filtered data
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Error fetching leads data',
            });
        }
    });
}

</script>


<script>
$(document).ready(function () {
    // Assigned To Filter
    $('#applyAssignedFilter').click(function () {
        let selectedUsers = [];

        $('.assigned-checkbox:checked').each(function () {
            selectedUsers.push($(this).val());
        });

        $.ajax({
            url: "{{ route('leads.filterByAssigned') }}",
            method: "GET",
            data: { assigned_to: selectedUsers },
            dataType: "json",
            success: function (response) {
                updateLeadsTableAndPipeline(response);
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Could not filter assigned data.',
                });
            }
        });
    });

    function updateLeadsTableAndPipeline(response) {
        let tableBody = '';

        if (response.leads.length === 0) {
            tableBody = '<tr><td colspan="7" class="text-center">No leads found.</td></tr>';
        } else {
            response.leads.forEach(lead => {
                tableBody += `
                    <tr>
                        <td>${new Date(lead.created_at).toLocaleDateString()}</td>
                        <td>${lead.name}</td>
                        <td>${lead.stage ? lead.stage.name : 'N/A'}</td>
                        <td>${lead.source || 'N/A'}</td>
                        <td>${lead.assigned_to ? lead.assigned_to.name : 'Unassigned'}</td>
                        <td>${lead.remember || 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-info viewBtn" data-name="${lead.name}">View</button>
                        </td>
                    </tr>`;
            });
        }
        $('#tableBody').html(tableBody);

        let pipelineHTML = '';
        response.stages.forEach(stage => {
            let leadsHTML = '';
            stage.leads.forEach(lead => {
                leadsHTML += `<li class="border p-2 my-2 lead-card" draggable="true">${lead.name}</li>`;
            });

            pipelineHTML += `<div class="pipeline-stage border p-3"><h6>${stage.name}</h6><ul class="list-unstyled lead-list">${leadsHTML}</ul></div>`;
        });

        $('.pipeline-container').html(pipelineHTML);
    }
});
</script>

<script>

$(document).ready(function () {
    // Show Date Picker when button is clicked
    $('#dateFilterBtn').click(function () {
        $('#datePicker').toggleClass('d-none').focus(); // Toggle visibility and focus
    });

    // Date Filter - Trigger on change
    $('#datePicker').on('change', function () {
        let selectedDate = $(this).val();
        console.log("Selected Date:", selectedDate); // Debugging

        if (!selectedDate) return; // Prevent empty request

        $.ajax({
            url: "{{ route('leads.filterByDate') }}",
            method: "GET",
            data: { date: selectedDate },
            dataType: "json",
            success: function (response) {
                console.log("Response:", response); // Debugging
                updateLeadsTableAndPipeline(response);
            },
            error: function (xhr) {
                console.error("AJAX Error:", xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Could not filter by date.',
                });
            }
        });
    });

    function updateLeadsTableAndPipeline(response) {
        let tableBody = '';

        if (response.leads.length === 0) {
            tableBody = '<tr><td colspan="7" class="text-center">No leads found.</td></tr>';
        } else {
            response.leads.forEach(lead => {
                tableBody += `
                    <tr>
                        <td>${new Date(lead.created_at).toLocaleDateString()}</td>
                        <td>${lead.name}</td>
                        <td>${lead.stage ? lead.stage.name : 'N/A'}</td>
                        <td>${lead.source || 'N/A'}</td>
                        <td>${lead.assigned_to ? lead.assigned_to.name : 'Unassigned'}</td>
                        <td>${lead.remember || 'N/A'}</td>
                        <td>
                            <button class="btn btn-sm btn-info viewBtn" data-name="${lead.name}">View</button>
                        </td>
                    </tr>`;
            });
        }
        $('#tableBody').html(tableBody);

        let pipelineHTML = '';
        response.stages.forEach(stage => {
            let leadsHTML = '';
            stage.leads.forEach(lead => {
                leadsHTML += `<li class="border p-2 my-2 lead-card" draggable="true">${lead.name}</li>`;
            });

            pipelineHTML += `<div class="pipeline-stage border p-3"><h6>${stage.name}</h6><ul class="list-unstyled lead-list">${leadsHTML}</ul></div>`;
        });

        $('.pipeline-container').html(pipelineHTML);
    }
});

</script>


<script>
$(document).ready(function () {
    let bulkEditMode = false; // Track bulk mode

    // Handle Bulk Edit Button Click
    $('#bulkEditBtn').click(function () {
        bulkEditMode = !bulkEditMode; // Toggle mode

        if (bulkEditMode) {
            $('.lead-checkbox').removeClass('d-none'); // Show checkboxes
            $('#deleteSelectedBtn, #stageFilterContainer, #assignedFilterContainer').removeClass('d-none'); // Show buttons
        } else {
            $('.lead-checkbox').addClass('d-none'); // Hide checkboxes
            $('#deleteSelectedBtn, #stageFilterContainer, #assignedFilterContainer').addClass('d-none'); // Hide buttons
        }
    });

    // Function to get selected leads
    function getSelectedLeads() {
        let selectedLeads = [];
        $('.lead-checkbox:checked').each(function () {
            selectedLeads.push($(this).data('lead-id'));
        });
        return selectedLeads;
    }

    // Handle Change Stage Click
    $('.change-stage').click(function () {
        let selectedLeads = getSelectedLeads();
        let stageId = $(this).data('stage-id');

        if (selectedLeads.length === 0) {
            Swal.fire('No leads selected!', 'Please select at least one lead.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Confirm Change Stage',
            text: 'Are you sure you want to change the stage for selected leads?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, change!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('leads.bulkUpdateStage') }}",
                    method: "POST",
                    data: {
                        leads: selectedLeads,
                        stage_id: stageId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        Swal.fire('Updated!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'Could not update stage.', 'error');
                    }
                });
            }
        });
    });

    // Handle Assign To Click
    $('.assign-user').click(function () {
        let selectedLeads = getSelectedLeads();
        let userId = $(this).data('user-id');

        if (selectedLeads.length === 0) {
            Swal.fire('No leads selected!', 'Please select at least one lead.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Confirm Assignment',
            text: 'Are you sure you want to assign selected leads to the chosen user?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, assign!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('leads.bulkAssign') }}",
                    method: "POST",
                    data: {
                        leads: selectedLeads,
                        user_id: userId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function (response) {
                        Swal.fire('Updated!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'Could not assign leads.', 'error');
                    }
                });
            }
        });
    });

    // Handle Delete Button Click
    $('#deleteSelectedBtn').click(function () {
        let selectedLeads = getSelectedLeads();

        if (selectedLeads.length === 0) {
            Swal.fire('No leads selected!', 'Please select at least one lead.', 'warning');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: 'Selected leads will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('leads.bulkDelete') }}",
                    method: "POST",
                    data: {
                        leads: selectedLeads,
                        _token: "{{ csrf_token() }}" // Include CSRF token
                    },
                    success: function (response) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            location.reload(); // Refresh the page after deletion
                        });
                    },
                    error: function (xhr) {
                        Swal.fire('Error!', 'Could not delete leads.', 'error');
                    }
                });
            }
        });
    });

});


</script>

<script>
    $('#saveBtn').click(function () {
    let leadId = $('#detailsPanel').data('lead-id'); // Retrieve lead ID from panel
    let note = $('#note').val().trim();
    let task = $('#task').val().trim();

    if (!leadId) {
        Swal.fire('Error', 'Lead ID is missing!', 'error');
        return;
    }

    $.ajax({
        url: "{{ route('leads.saveDetails') }}",
        method: "POST",
        data: {
            lead_id: leadId,
            note: note,
            task: task,
            _token: "{{ csrf_token() }}"
        },
        success: function (response) {
            Swal.fire('Success', 'Details saved successfully!', 'success');
        },
        error: function (xhr) {
            Swal.fire('Error', 'Could not save details.', 'error');
            console.error(xhr.responseText);
        }
    });
});

    </script>
@endpush