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
            <h3>Contact</h3>
            <div>
                <button class="btn btn-sm btn-success" onclick="downloadCSV()">Download CSV</button>
                <button class="btn btn-sm btn-primary" onclick="downloadExcel()">Download Excel</button>
            </div>
        </div>

        <!-- Table View -->
        <div id="tableView" class="d-block">
            <br><h6>Table View</h6>
            <table class="table table-striped" id="leadTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leads as $lead)
                        <tr>
                            <td>{{ $lead->name }}</td>
                            <td>{{ $lead->email }}</td>
                            <td>{{ $lead->mobile_number }}</td>
                            <td>{{ $lead->address }}</td>
                            <td>{{ $lead->source }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function downloadCSV() {
    let table = document.getElementById("leadTable");
    let rows = table.querySelectorAll("tr");
    let csvData = [];

    rows.forEach(row => {
        let rowData = [];
        row.querySelectorAll("td, th").forEach(cell => rowData.push(cell.innerText));
        csvData.push(rowData.join(","));
    });

    let csvContent = "data:text/csv;charset=utf-8," + csvData.join("\n");
    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "leads.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function downloadExcel() {
    let table = document.getElementById("leadTable");
    let rows = table.querySelectorAll("tr");
    let excelData = "<table border='1'>";

    rows.forEach(row => {
        excelData += "<tr>";
        row.querySelectorAll("td, th").forEach(cell => {
            excelData += `<td>${cell.innerText}</td>`;
        });
        excelData += "</tr>";
    });

    excelData += "</table>";
    let blob = new Blob([excelData], { type: "application/vnd.ms-excel" });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "leads.xls";
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endpush


