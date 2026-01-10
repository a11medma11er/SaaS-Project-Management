@extends('layouts.master')

@section('title') AI Features @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="mb-0 font-size-18">
                    <i class="ri-magic-line"></i> AI Advanced Features
                </h4>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Development Plan Feature -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="ri-road-map-line"></i> Development Plan Generator
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Generate comprehensive AI-powered development plans for your projects.</p>
                    
                    <form id="devPlanForm" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label">Select Project</label>
                            <select class="form-select" name="project_id" required>
                                <option value="">Choose project...</option>
                                @foreach(\App\Models\Project::all() as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Additional Requirements (Optional)</label>
                            <textarea class="form-control" name="requirements" rows="3" placeholder="Any specific requirements..."></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-sparkling-line"></i> Generate Plan
                        </button>
                    </form>
                    
                    <div id="devPlanResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Project Breakdown Feature -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="ri-list-check"></i> Project Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Break down projects into detailed, manageable tasks automatically.</p>
                    
                    <form id="breakdownForm" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label">Select Project</label>
                            <select class="form-select" name="project_id" required>
                                <option value="">Choose project...</option>
                                @foreach(\App\Models\Project::all() as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Granularity</label>
                            <select class="form-select" name="granularity">
                                <option value="low">Low (Broad tasks)</option>
                                <option value="medium" selected>Medium (Balanced)</option>
                                <option value="high">High (Very detailed)</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100">
                            <i class="ri-split-cells-horizontal"></i> Break Down
                        </button>
                    </form>
                    
                    <div id="breakdownResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Studies Feature -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="ri-file-search-line"></i> AI Studies Generator
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Generate feasibility, technical, and risk assessment studies.</p>
                    
                    <form id="studyForm" class="mt-3">
                        <div class="mb-3">
                            <label class="form-label">Select Project</label>
                            <select class="form-select" name="project_id" required>
                                <option value="">Choose project...</option>
                                @foreach(\App\Models\Project::all() as $project)
                                    <option value="{{ $project->id }}">{{ $project->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Study Type</label>
                            <select class="form-select" name="study_type" required>
                                <option value="feasibility">Feasibility Study</option>
                                <option value="technical">Technical Study</option>
                                <option value="risk">Risk Assessment</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-info w-100">
                            <i class="ri-file-add-line"></i> Generate Study
                        </button>
                    </form>
                    
                    <div id="studyResult" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Results Modal -->
<div class="modal fade" id="resultsModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">AI Results</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Results will be inserted here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="downloadResults()">
                    <i class="ri-download-line"></i> Download
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Development Plan Form
document.getElementById('devPlanForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
    
    try {
        const response = await axios.post('{{ route("ai.features.development_plan") }}', {
            project_id: formData.get('project_id'),
            requirements: formData.get('requirements'),
            _token: '{{ csrf_token() }}'
        });
        
        if (response.data.success) {
            showResults('Development Plan', formatDevPlan(response.data.plan, response.data.ai_info));
        }
    } catch (error) {
        toastr.error('Failed to generate plan');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-sparkling-line"></i> Generate Plan';
    }
});

// Project Breakdown Form
document.getElementById('breakdownForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
    
    try {
        const response = await axios.post('{{ route("ai.features.breakdown") }}', {
            project_id: formData.get('project_id'),
            granularity: formData.get('granularity'),
            _token: '{{ csrf_token() }}'
        });
        
        if (response.data.success) {
            showResults('Project Breakdown', formatBreakdown(response.data.breakdown));
        }
    } catch (error) {
        toastr.error('Failed to breakdown project');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-split-cells-horizontal"></i> Break Down';
    }
});

// Study Form
document.getElementById('studyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generating...';
    
    try {
        const response = await axios.post('{{ route("ai.features.study") }}', {
            project_id: formData.get('project_id'),
            study_type: formData.get('study_type'),
            _token: '{{ csrf_token() }}'
        });
        
        if (response.data.success) {
            showResults('AI Study', formatStudy(response.data.study));
        }
    } catch (error) {
        toastr.error('Failed to generate study');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-file-add-line"></i> Generate Study';
    }
});

// Show results in modal
function showResults(title, content) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML = content;
    new bootstrap.Modal(document.getElementById('resultsModal')).show();
}

// Format development plan
function formatDevPlan(plan, aiInfo) {
    let html = '<div class="ai-results">';
    
    // Show AI Provider Info
    if (aiInfo) {
        html += '<div class="alert alert-info mb-3">';
        html += '<strong>🤖 AI Provider:</strong> ' + (aiInfo.provider || 'Unknown');
        if (aiInfo.model) {
            html += ' <span class="badge bg-primary">' + aiInfo.model + '</span>';
        }
        html += '</div>';
    }
    
    html += `<h5>${plan.overview.title}</h5>`;
    html += `<p class="text-muted">${plan.overview.summary}</p>`;
    html += `<div class="row mb-3">`;
    html += `<div class="col-md-4"><strong>Duration:</strong> ${plan.overview.estimated_duration} weeks</div>`;
    html += `<div class="col-md-4"><strong>Complexity:</strong> ${plan.overview.complexity}</div>`;
    html += `<div class="col-md-4"><strong>Confidence:</strong> ${(plan.overview.confidence * 100).toFixed(0)}%</div>`;
    html += `</div>`;
    
    html += '<h6 class="mt-3">Phases:</h6>';
    plan.phases.forEach(phase => {
        html += `<div class="card mb-2">`;
        html += `<div class="card-body">`;
        html += `<h6>${phase.name} <span class="badge bg-info">${phase.duration}</span></h6>`;
        html += `<ul>`;
        phase.tasks.forEach(task => html += `<li>${task}</li>`);
        html += `</ul>`;
        html += `</div></div>`;
    });
    
    html += '</div>';
    return html;
}

// Format breakdown
function formatBreakdown(breakdown) {
    let html = '<div class="ai-results">';
    html += `<h5>Project Breakdown</h5>`;
    html += `<p class="text-muted">Total Tasks: ${breakdown.total_estimated_tasks}</p>`;
    
    Object.entries(breakdown.categories).forEach(([category, data]) => {
        html += `<div class="card mb-2">`;
        html += `<div class="card-header"><strong>${category}</strong> <span class="badge bg-primary">${data.priority}</span></div>`;
        html += `<div class="card-body">`;
        html += `<p><strong>Duration:</strong> ${data.estimated_duration}</p>`;
        html += `<ul>`;
        data.tasks.forEach(task => html += `<li>${task}</li>`);
        html += `</ul></div></div>`;
    });
    
    html += '</div>';
    return html;
}

// Format study
function formatStudy(study) {
    let html = '<div class="ai-results">';
    html += `<h5>${study.type.charAt(0).toUpperCase() + study.type.slice(1)} Study</h5>`;
    html += `<p class="text-muted">Project: ${study.project}</p>`;
    
    Object.entries(study.analysis).forEach(([key, value]) => {
        html += `<div class="mb-3">`;
        html += `<strong>${key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}:</strong><br>`;
        if (Array.isArray(value)) {
            html += `<ul>`;
            value.forEach(item => {
                if (typeof item === 'object') {
                    html += `<li>${JSON.stringify(item)}</li>`;
                } else {
                    html += `<li>${item}</li>`;
                }
            });
            html += `</ul>`;
        } else {
            html += `<p>${value}</p>`;
        }
        html += `</div>`;
    });
    
    html += '</div>';
    return html;
}

function downloadResults() {
    const content = document.getElementById('modalBody').innerText;
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'ai-results.txt';
    a.click();
}
</script>
@endsection
