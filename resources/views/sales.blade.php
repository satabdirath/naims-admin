@extends('layouts.app')

@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/sales.css') }}">
  
@endpush

@section('content')
<div class="container-fluid">
    <div class="content">
        <div class="d-flex justify-content-between mb-3">
            <h3>Sales</h3>
            <div>
                <button class="btn btn-primary" id="pipelineBtn">Pipeline View</button>
                <button class="btn btn-secondary" id="tableBtn">Table View</button>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadModal">Add Leads</button>
            <div class="vr mx-2"></div>
            <button class="btn btn-outline-secondary">Bulk Edit</button>
            <button class="btn btn-outline-secondary">Source</button>
            <button class="btn btn-outline-secondary">Status</button>
            <button class="btn btn-outline-secondary">Assigned to</button>
            <button class="btn btn-outline-secondary">Date</button>
            <button class="btn btn-outline-secondary">Activity</button>
        </div>

        <!-- Pipeline View -->
        <div id="pipelineView" class="d-none">
            <br><h6>Pipeline View</h6>
            <div class="pipeline-container">
                @foreach($stages as $stage)
                    <div class="pipeline-stage border p-3">
                        <h6>{{ $stage->name }}</h6>
                        <ul class="list-unstyled">
                            @foreach($stage->leads as $lead)
                                <li class="border p-2 my-2 lead-card"
                                    data-name="{{ $lead->name }}"
                                    data-status="{{ $lead->status ?? 'N/A' }}"
                                    data-assigned="{{ $lead->assigned_to ?? 'Unassigned' }}"
                                    data-date="{{ $lead->created_at }}">
                                    {{ $lead->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Table View -->
        <div id="tableView" class="d-block">
            <br><h6>Table View</h6>
            <table class="table table-striped">
                <thead>
                    <tr>
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
                alert('Stage updated successfully');
            }
        });
    });

 // Save Lead
    // Save Lead
    $('#saveLead').click(function () {
        if ($('#leadName').val() === '' || $('#leadMobile').val() === '' || $('#leadEmail').val() === '') {
            alert('Please fill all fields!');
            return;
        }

        $.ajax({
            url: "{{ route('leads.store') }}",
            method: "POST",
            data: {
                name: $('#leadName').val(),
                mobile_number: $('#leadMobile').val(),
                email: $('#leadEmail').val(),
                address: $('#leadAddress').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function (response) {
                alert('Lead added successfully!');
                $('#addLeadModal').modal('hide');
                fetchLeads();
            },
            error: function (xhr) {
                alert('Error: ' + xhr.responseText);
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
                alert('Assigned user updated successfully');
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
                let stageOptions = response.stages.map(stage =>
                    `<option value="${stage.id}" ${lead.stage_id == stage.id ? 'selected' : ''}>${stage.name}</option>`
                ).join('');

                let userOptions = response.users.map(user =>
                    `<option value="${user.id}" ${lead.assigned_to == user.id ? 'selected' : ''}>${user.name}</option>`
                ).join('');

                tableBody += `
                    <tr>
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
            console.error("Error fetching leads data");
        }
    });
}


    $(document).on('click', '.viewBtn, .lead-card', function () {
        let name = $(this).attr('data-name') || 'N/A';
        let status = $(this).attr('data-status') || 'N/A';
        let assignedTo = $(this).attr('data-assigned') || 'Unassigned';
        let date = $(this).attr('data-date') || 'N/A';

        $('#detailName').text(name);
        $('#detailsPanel').fadeIn();
    });

    $('#closeDetails').click(function () {
        $('#detailsPanel').fadeOut();
    });

    $('#pipelineBtn').click(() => { $('#tableView').addClass("d-none"); $('#pipelineView').removeClass("d-none"); });
    $('#tableBtn').click(() => { $('#pipelineView').addClass("d-none"); $('#tableView').removeClass("d-none"); });

    fetchLeads();
});
</script>
@endpush
