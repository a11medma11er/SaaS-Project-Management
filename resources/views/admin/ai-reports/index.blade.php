@extends('layouts.master')

@section('title') AI Reports & Analytics @endsection

@section('css')
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
.report-card {
    cursor: pointer;
    transition: all 0.3s ease;
}
.report-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}
.report-card.selected {
    border: 2px solid #556ee6;
    background: #f8f9fe;
}
#reportResult {
    max-height: 600px;
    overflow-y: auto;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 font-size-18">
                    <i class="ri-file-chart-line"></i> AI Reports & Analytics
                </h4>
                <p class="text-muted mt-2">Generate comprehensive reports and export analytics</p>
            </div>
        </div>
    </div>

    <!-- Report Generator -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="ri-file-add-line text-primary"></i> Generate New Report
                    </h5>
                </div>
                <div class="card-body">
                    <form id="reportForm">
                        <div class="row">
                            <!-- Date Range -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="text" class="form-control" id="startDate" name="start_date" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">End Date</label>
                                <input type="text" class="form-control" id="endDate" name="end_date" required>
                            </div>

                            <!-- Decision Types Filter -->
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Decision Types (Optional)</label>
                                <select class="form-select" id="decisionTypes" name="decision_types[]" multiple>
                                    @foreach($decisionTypes as $type)
                                    <option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Leave empty to include all types</small>
                            </div>
                        </div>

                        <!-- Report Templates -->
                        <div class="mb-3">
                            <label class="form-label">Select Report Template</label>
                            <div class="row g-3">
                                @foreach($templates as $key => $template)
                                <div class="col-md-3">
                                    <div class="report-card card h-100" data-template="{{ $key }}">
                                        <div class="card-body text-center">
                                            <i class="ri-file-chart-line font-size-48 text-primary mb-3"></i>
                                            <h6 class="card-title">{{ $template['name'] }}</h6>
                                            <p class="card-text small text-muted">{{ $template['description'] }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" id="selectedTemplate" name="template" required>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-bar-chart-line"></i> Generate Report
                            </button>
                            <button type="button" class="btn btn-success" id="exportPDF">
                                <i class="ri-file-pdf-line"></i> Export PDF
                            </button>
                            <button type="button" class="btn btn-info" id="exportExcel">
                                <i class="ri-file-excel-line"></i> Export Excel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Result -->
    <div class="row" id="reportContainer" style="display: none;">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="ri-file-text-line text-success"></i> Report Results
                    </h5>
                    <button class="btn btn-sm btn-light" onclick="$('#reportContainer').hide()">
                        <i class="ri-close-line"></i> Close
                    </button>
                </div>
                <div class="card-body" id="reportResult">
                    <!-- Report will be dynamically loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Initialize date pickers
flatpickr("#startDate", {
    dateFormat: "Y-m-d",
    defaultDate: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
});

flatpickr("#endDate", {
    dateFormat: "Y-m-d",
    defaultDate: new Date()
});

// Template selection
$('.report-card').click(function() {
    $('.report-card').removeClass('selected');
    $(this).addClass('selected');
    $('#selectedTemplate').val($(this).data('template'));
});

// Generate Report
$('#reportForm').submit(function(e) {
    e.preventDefault();
    
    if (!$('#selectedTemplate').val()) {
        toastr.error('Please select a report template');
        return;
    }

    const formData = {
        start_date: $('#startDate').val(),
        end_date: $('#endDate').val(),
        template: $('#selectedTemplate').val(),
        decision_types: $('#decisionTypes').val()
    };

    axios.post('{{ route("ai.reports.generate") }}', formData)
        .then(response => {
            if (response.data.success) {
                displayReport(response.data.report);
                toastr.success('Report generated successfully');
            }
        })
        .catch(error => {
            toastr.error('Failed to generate report');
            console.error(error);
        });
});

// Export PDF
$('#exportPDF').click(function() {
    const formData = {
        start_date: $('#startDate').val(),
        end_date: $('#endDate').val(),
        decision_types: $('#decisionTypes').val()
    };

    axios.post('{{ route("ai.reports.export.pdf") }}', formData)
        .then(response => {
            if (response.data.success) {
                window.open(response.data.url, '_blank');
                toastr.success(response.data.message);
            }
        })
        .catch(error => {
            toastr.error('Failed to export PDF');
            console.error(error);
        });
});

// Export Excel
$('#exportExcel').click(function() {
    const formData = {
        start_date: $('#startDate').val(),
        end_date: $('#endDate').val(),
        decision_types: $('#decisionTypes').val()
    };

    axios.post('{{ route("ai.reports.export.excel") }}', formData)
        .then(response => {
            if (response.data.success) {
                window.open(response.data.url, '_blank');
                toastr.success(response.data.message);
            }
        })
        .catch(error => {
            toastr.error('Failed to export Excel');
            console.error(error);
        });
});

// Display Report
function displayReport(report) {
    $('#reportContainer').show();
    let html = `
        <div class="mb-4">
            <h6>Report Period: ${report.period.start} to ${report.period.end}</h6>
            <small class="text-muted">Generated at: ${new Date(report.generated_at).toLocaleString()}</small>
        </div>
    `;

    // Summary
    if (report.data.summary) {
        html += `
            <div class="mb-4">
                <h5 class="border-bottom pb-2">Summary</h5>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3>${report.data.summary.total_decisions}</h3>
                                <small>Total Decisions</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3>${report.data.summary.acceptance_rate}%</h3>
                                <small>Acceptance Rate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3>${report.data.summary.rejection_rate}%</h3>
                                <small>Rejection Rate</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3>${(report.data.summary.avg_confidence * 100).toFixed(1)}%</h3>
                                <small>Avg Confidence</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Accuracy
    if (report.data.accuracy) {
        html += `
            <div class="mb-4">
                <h5 class="border-bottom pb-2">Accuracy Metrics</h5>
                <table class="table">
                    <tr>
                        <td>Overall Accuracy</td>
                        <td><strong>${report.data.accuracy.overall_accuracy}%</strong></td>
                    </tr>
                    <tr>
                        <td>High Confidence Accuracy</td>
                        <td>${report.data.accuracy.high_confidence_accuracy}%</td>
                    </tr>
                    <tr>
                        <td>Medium Confidence Accuracy</td>
                        <td>${report.data.accuracy.medium_confidence_accuracy}%</td>
                    </tr>
                    <tr>
                        <td>Low Confidence Accuracy</td>
                        <td>${report.data.accuracy.low_confidence_accuracy}%</td>
                    </tr>
                </table>
            </div>
        `;
    }

    $('#reportResult').html(html);
}
</script>
@endsection
