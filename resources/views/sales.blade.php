@extends('layouts.app')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sales.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>


  
@endpush

@section('content')
<div class="container-fluid">
    <div class="content">
    <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Sales</h3>
    <div>
    <button class="btn btn-primary" id="pipelineBtn">Pipeline View</button>
    <button class="btn btn-secondary" id="tableBtn">Table View</button><br>

    <!-- Added mt-2 for spacing -->
    <div class="dropdown w-100 mt-2">
        <button class="btn btn-outline-danger dropdown-toggle w-100" id="lostLeadsBtn" data-bs-toggle="dropdown">
            Lost <span class="text-danger fw-bold">({{ $lostLeads->count() }})</span>
        </button>
        <ul class="dropdown-menu p-2 lead-list w-100" id="lostLeadsDropdown" data-stage-id="0">
            @if($lostLeads->count() > 0)
                @foreach($lostLeads as $lead)
                    <li class="border p-2 my-1 lead-card bg-danger text-white" 
                        data-lead-id="{{ $lead->id }}" 
                        data-name="{{ $lead->name }}"  
                        draggable="true">
                        {{ $lead->name }}
                    </li>
                @endforeach
            @else
                <li class="text-center text-muted p-2">No lost leads</li>
            @endif
        </ul>
    </div>
</div>
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
    @foreach($stages->whereBetween('id', [1, 6]) as $stage)

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


<div class="dropdown">
    <button class="btn btn-outline-secondary dropdown-toggle" id="activityFilterBtn" data-bs-toggle="dropdown">
        Activity
    </button>
    <ul class="dropdown-menu" id="activityDropdown">
        <li><a class="dropdown-item activity-option" data-value="today" style="cursor: pointer;">Today</a></li>
        <li><a class="dropdown-item activity-option" data-value="this_week" style="cursor: pointer;">This Week</a></li>
        <li><a class="dropdown-item activity-option" data-value="last_week" style="cursor: pointer;">Last Week</a></li>
        <li><a class="dropdown-item activity-option" data-value="this_month" style="cursor: pointer;">This Month</a></li>
        <li><a class="dropdown-item activity-option" data-value="last_month" style="cursor: pointer;">Last Month</a></li>
    </ul>
</div>






        </div>

<!-- Pipeline View -->
<div id="pipelineView" class="d-block">
    <br><h6>Pipeline View</h6>
    <div class="pipeline-container">
        @foreach($stages->whereBetween('id', [1, 6]) as $stage)
            <div class="pipeline-stage border p-3">
                <h6>
                    {{ $stage->name }} 
                    <span class="text-success fw-bold">({{ $stage->leads->count() }})</span>
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

        <!-- Lost Stage -->
      
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
        <p><span id="detailName"></span> 
            <a href="#" class="activity-link" id="viewActivity">View Activity</a>
        </p>
      <!-- Communication Icons -->
      <div class="horizontal-icons d-flex gap-3 my-3">
    <i class="fas fa-phone communication-icon text-primary fs-4" data-type="call" title="Call" style="cursor: pointer;"></i>
    <i class="fas fa-comment communication-icon text-success fs-4" data-type="message" title="Message" style="cursor: pointer;"></i>
    <i class="fas fa-envelope communication-icon text-danger fs-4" data-type="email" title="Email" style="cursor: pointer;"></i>
    <i class="fab fa-whatsapp communication-icon text-success fs-4" data-type="whatsapp" title="WhatsApp" style="cursor: pointer;"></i>
</div>


          <!-- Communication Logs -->
          <div id="communicationLogs" class="communication-logs mt-3" style="display: none;">
            <h6>Communication Logs</h6>
            <ul id="logList" class="list-unstyled border p-2 rounded bg-light">
                <li>No logs available</li>
            </ul>
        </div>
        
        <a href="#" class="form-link" data-lead-id="{{ $lead->id }}">View Form Submission</a>

        <textarea class="note-textarea" id="note" placeholder="Enter notes here..."></textarea>

       <div class="vertical-icons">
    <h6>Logs</h6>
    <div class="d-flex flex-column align-items-start gap-1">
        <p class="mb-1 log-link" data-log-type="call" style="cursor: pointer;">
            <i class="fas fa-phone" title="Call Log"></i> Call Log
        </p>
        <p class="mb-1 log-link" data-log-type="whatsapp" style="cursor: pointer;">
            <i class="fab fa-whatsapp" title="WhatsApp Log"></i> WhatsApp Log
        </p>
        <p class="mb-1 log-link" data-log-type="mail" style="cursor: pointer;">
            <i class="fas fa-envelope" title="Mail Log"></i> Mail Log
        </p>
        <p class="mb-1 log-link" data-log-type="meeting" style="cursor: pointer;">
            <i class="fas fa-calendar-alt" title="Meeting Log"></i> Meeting Log
        </p>
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

<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="activityModalLabel">Recent Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul id="modalActivityList" class="list-unstyled">
                    <li>No activity found.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Form Submission Modal -->
<div class="modal fade" id="formSubmissionModal" tabindex="-1" aria-labelledby="formSubmissionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formSubmissionModalLabel">Form Submission Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Name:</strong> <span id="leadName"></span></p>
                <p><strong>Email:</strong> <span id="leadEmail"></span></p>
                <p><strong>Phone:</strong> <span id="leadPhone"></span></p>
                <p><strong>Address:</strong> <span id="leadAddress"></span></p>
                <p><strong>Source:</strong> <span id="leadSource"></span></p>
                <p><strong>Stage:</strong> <span id="leadStage"></span></p>
            </div>
        </div>
    </div>
</div>


<!-- Logs Modal -->
<div class="modal fade" id="logsModal" tabindex="-1" aria-labelledby="logsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logsModalLabel">Log Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="logDetails">Log details will be displayed here.</p>
            </div>
        </div>
    </div>
</div>




@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function () {
        $(".communication-icon").on("click", function () {
            let type = $(this).data("type");
            let logs = getLogs(type);

            // If the same icon is clicked again, toggle visibility
            if ($("#communicationLogs").is(":visible") && $("#logList").children().length > 0) {
                $("#communicationLogs").slideUp();
                return;
            }

            $("#logList").empty();
            if (logs.length > 0) {
                logs.forEach(log => {
                    $("#logList").append(`<li class="py-1">${log}</li>`);
                });
            } else {
                $("#logList").append("<li>No logs available</li>");
            }

            $("#communicationLogs").slideDown();
        });

        // Close logs when clicking outside the log container
        $(document).on("click", function (event) {
            if (!$(event.target).closest(".communication-icon, #communicationLogs").length) {
                $("#communicationLogs").slideUp();
            }
        });

        function getLogs(type) {
            let sampleLogs = {
                call: ["Call to John - 10 min", "Missed call from Jane"],
                message: ["Sent: Are you available?", "Received: Yes, call me."],
                email: ["Sent: Proposal email", "Received: Confirmation from client"],
                whatsapp: ["Sent: Follow-up message", "Received: Thanks, will review."]
            };
            return sampleLogs[type] || [];
        }
    });
</script>

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

                    // If moved to Lost stage, set stage_id to 0
                    if (newStageId === 0) {
                        newStageId = 0;
                    }

                    $.ajax({
                        url: "{{ route('leads.updateStage') }}",  
                        method: "POST",
                        data: {
                            lead_id: leadId,
                            stage_id: newStageId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            console.log("Lead updated successfully:", response);
                            location.reload();
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
                    tableBody += 
                        `<tr>
                        <td></td>
                            <td>${new Date(lead.created_at).toLocaleDateString()}</td>
                            <td>${lead.name}</td>
                            <td>${lead.stage ? lead.stage.name : 'N/A'}</td>
                            <td>${lead.source || 'N/A'}</td>
                            <td>${lead.assigned_to ? lead.assigned_to.name : 'Unassigned'}</td>
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

            // ✅ Update Pipeline View
            let pipelineHtml = '';
            response.stages.forEach(stage => {
                pipelineHtml += `
                    <div class="pipeline-stage border p-3">
                        <h6>${stage.name} <span class="text-success fw-bold">(${stage.leads.length})</span></h6>
                        <ul class="list-unstyled lead-list" data-stage-id="${stage.id}">`;
                
                stage.leads.forEach(lead => {
                    pipelineHtml += `
                        <li class="border p-2 my-2 lead-card" 
                            data-lead-id="${lead.id}" 
                            data-name="${lead.name}"  
                            draggable="true">
                            ${lead.name}
                        </li>`;
                });

                pipelineHtml += `</ul></div>`;
            });

            $('.pipeline-container').html(pipelineHtml);
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
                    <td></td>
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
    $('#applySourceFilter').click(function () {
        let selectedSources = [];

        $('.source-checkbox:checked').each(function () {
            selectedSources.push($(this).val()); // Collect checked sources
        });

        $.ajax({
            url: "{{ route('leads.filterBySource') }}",
            method: "GET",
            data: { sources: selectedSources },
            dataType: "json",
            success: function (response) {
                updateLeadsTable(response);
                updatePipelineView(response); // ✅ Update pipeline view
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

    function updateLeadsTable(response) {
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
    }

    function updatePipelineView(response) {
    let pipelineHTML = '';

    response.stages.forEach(stage => {
        let leadsHTML = stage.leads.map(lead => `
            <li class="border p-2 my-2 lead-card" 
                data-lead-id="${lead.id}" 
                data-name="${lead.name}"  
                draggable="true">
                ${lead.name}
            </li>
        `).join('');

        pipelineHTML += `
            <div class="pipeline-stage border p-3">
                <h6>${stage.name} 
                    <span class="text-success fw-bold">(${stage.leads.length})</span>
                </h6>
                <ul class="list-unstyled lead-list" data-stage-id="${stage.id}">
                    ${leadsHTML}
                </ul>
            </div>`;
    });

    $('.pipeline-container').html(pipelineHTML);

    // Update lost leads dropdown
    let lostLeadsHTML = response.lostLeads.length > 0 ? response.lostLeads.map(lead => `
        <li class="border p-2 my-1 lead-card bg-danger text-white" 
            data-lead-id="${lead.id}" 
            data-name="${lead.name}"  
            draggable="true">
            ${lead.name}
        </li>
    `).join('') : `<li class="text-center text-muted p-2">No lost leads</li>`;

    $('#lostLeadsDropdown').html(lostLeadsHTML);

    // Reinitialize drag-and-drop
    initializeDragAndDrop();
}

function initializeDragAndDrop() {
    $(".lead-card").attr("draggable", true);

    $(".lead-card").on("dragstart", function (event) {
        event.originalEvent.dataTransfer.setData("leadId", $(this).data("lead-id"));
    });

    $(".pipeline-stage").on("dragover", function (event) {
        event.preventDefault();
        $(this).addClass("drag-over"); // Visual feedback
    });

    $(".pipeline-stage").on("dragleave", function () {
        $(this).removeClass("drag-over"); // Remove feedback
    });

    $(".pipeline-stage").on("drop", function (event) {
        event.preventDefault();
        $(this).removeClass("drag-over"); // Remove visual feedback

        let leadId = event.originalEvent.dataTransfer.getData("leadId");
        let leadElement = $(`.lead-card[data-lead-id="${leadId}"]`);
        let leadList = $(this).find(".lead-list");

        if (leadElement.length) {
            // Ensure the lead list exists
            if (leadList.length === 0) {
                leadList = $("<ul class='list-unstyled lead-list'></ul>");
                $(this).append(leadList);
            }

            leadList.append(leadElement);

            let newStageId = $(this).data("stage-id") || leadList.data("stage-id");
            updateLeadStage(leadId, newStageId);
        }
    });

    $("#lostLeadsBtn").on("dragover", function (event) {
        event.preventDefault();
    });

    $("#lostLeadsBtn").on("drop", function (event) {
        event.preventDefault();
        let leadId = event.originalEvent.dataTransfer.getData("leadId");
        let leadElement = $(`.lead-card[data-lead-id="${leadId}"]`);

        if (leadElement.length) {
            $("#lostLeadsDropdown").append(leadElement);
            updateLeadStage(leadId, 0);
        }
    });
}

function updateLeadStage(leadId, newStageId) {
    $.ajax({
        url: "/update-lead-stage",
        type: "POST",
        data: {
            lead_id: leadId,
            stage_id: newStageId,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            console.log("Lead moved successfully.");
        },
        error: function (error) {
            console.error("Error updating lead stage:", error);
        }
    });
}


});


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
                    <td></td>
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
    // Set the max date to today to disable future dates
    let today = new Date().toISOString().split('T')[0];
    $('#datePicker').attr('max', today);

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
                    <td></td>
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
    
    

<script>
$(document).ready(function () {
    // Open Activity Modal
    $("#viewActivity").click(function (e) {
        e.preventDefault();
        $("#activityModal").modal("show");
    });

    // Open Form Submission Modal
    $("#viewFormSubmission").click(function (e) {
        e.preventDefault();
        $("#formSubmissionModal").modal("show");
    });

    // Open Logs Modal
    $(".log-link").click(function (e) {
        e.preventDefault();
        let logType = $(this).data("log-type");
        let logText = "";

        switch (logType) {
            case "call":
                logText = "Call Log details here.";
                break;
            case "whatsapp":
                logText = "WhatsApp Log details here.";
                break;
            case "mail":
                logText = "Mail Log details here.";
                break;
            case "meeting":
                logText = "Meeting Log details here.";
                break;
        }

        $("#logDetails").text(logText);
        $("#logsModal").modal("show");
    });
});
</script>

<script>
    $(document).ready(function () {
    $('.form-link').click(function (e) {
        e.preventDefault();
        
        let leadId = $(this).data('lead-id'); // Ensure this ID is set in the button/link
        $.ajax({
            url: `/leads/${leadId}/details`, // Update with your actual route
            method: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    $('#leadName').text(response.lead.name);
                    $('#leadEmail').text(response.lead.email || 'N/A');
                    $('#leadPhone').text(response.lead.mobile_number || 'N/A');
                    $('#leadAddress').text(response.lead.address || 'N/A');
                    $('#leadSource').text(response.lead.source || 'N/A');
                    $('#leadStage').text(response.lead.stage ? response.lead.stage.name : 'N/A');
                    
                    $('#formSubmissionModal').modal('show'); // Show the modal
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Lead details could not be loaded.',
                    });
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while fetching lead details.',
                });
            }
        });
    });
});

</script>

<script>
$(document).ready(function () {
    $('.activity-option').click(function () {
        let selectedActivity = $(this).data('value'); // Get selected activity

        $.ajax({
            url: "{{ route('leads.filterByActivity') }}",
            method: "GET",
            data: { activity: selectedActivity },
            dataType: "json",
            success: function (response) {
                let tableBody = '';

                // ✅ Update Table View
                if (response.leads.length === 0) {
                    tableBody = '<tr><td colspan="7" class="text-center">No leads found.</td></tr>';
                } else {
                    response.leads.forEach(lead => {
                        tableBody += 
                            `<tr>
                                <td></td>
                                <td>${new Date(lead.created_at).toLocaleDateString()}</td>
                                <td>${lead.name}</td>
                                <td>${lead.stage ? lead.stage.name : 'N/A'}</td>
                                <td>${lead.source || 'N/A'}</td>
                                <td>${lead.assigned_to ? lead.assigned_to.name : 'Unassigned'}</td>
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

                // ✅ Update Pipeline View (Ensure Stages 1-6)
                let pipelineHtml = '';
                response.stages.forEach(stage => {
                    if (stage.id >= 1 && stage.id <= 6) { // ✅ Only show stages 1-6
                        pipelineHtml += `
                            <div class="pipeline-stage border p-3">
                                <h6>${stage.name} <span class="text-success fw-bold">(${stage.leads.length})</span></h6>
                                <ul class="list-unstyled lead-list" data-stage-id="${stage.id}">`;

                        stage.leads.forEach(lead => { // ✅ Display leads in the correct stage
                            pipelineHtml += `
                                <li class="border p-2 my-2 lead-card" 
                                    data-lead-id="${lead.id}" 
                                    data-name="${lead.name}"  
                                    draggable="true">
                                    ${lead.name}
                                </li>`;
                        });

                        pipelineHtml += `</ul></div>`;
                    }
                });

                $('.pipeline-container').html(pipelineHtml); // ✅ Replace Pipeline View
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
});

</script>




@endpush


