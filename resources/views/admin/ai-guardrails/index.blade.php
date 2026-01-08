@extends('layouts.master')

@section('title') AI Guardrails Settings @endsection

@section('css')
<style>
.guardrail-card {
    transition: all 0.3s ease;
    border-left: 4px solid #f46a6a;
}
.guardrail-card.enabled {
    border-left-color: #34c38f;
}
.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 10px;
    padding: 20px;
}
.threshold-input {
    max-width: 150px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="ri-shield-check-line"></i> AI Guardrails Settings
                </h4>

                <div class="page-title-right">
                    <form action="{{ route('ai.guardrails.cache.clear') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-soft-warning">
                            <i class="ri-refresh-line"></i> Clear Cache
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-2 opacity-75">Total Checks</p>
                        <h3 class="mb-0">{{ number_format($statistics['total_checks']) }}</h3>
                    </div>
                    <div>
                        <i class="ri-shield-check-line font-size-48 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-2 opacity-75">Violations</p>
                        <h3 class="mb-0">{{ number_format($statistics['total_violations']) }}</h3>
                    </div>
                    <div>
                        <i class="ri-alert-line font-size-48 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-2 opacity-75">Violation Rate</p>
                        <h3 class="mb-0">{{ $statistics['violation_rate'] }}%</h3>
                    </div>
                    <div>
                        <i class="ri-pie-chart-line font-size-48 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-2 opacity-75">Active Rules</p>
                        <h3 class="mb-0">{{ $statistics['rules_count'] }}/4</h3>
                    </div>
                    <div>
                        <i class="ri-list-check-2 font-size-48 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guardrail Rules -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-list-settings-line"></i> Guardrail Rules
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Rule 1: No Data Deletion -->
                        <div class="col-md-6">
                            <div class="guardrail-card card {{ $rules['no_data_deletion'] ? 'enabled' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title">
                                                <i class="ri-delete-bin-line text-danger"></i> No Data Deletion
                                            </h5>
                                            <p class="text-muted mb-2">
                                                Prevents AI from suggesting or executing actions that involve deleting data.
                                            </p>
                                            <span class="badge bg-{{ $rules['no_data_deletion'] ? 'success' : 'secondary' }}">
                                                {{ $rules['no_data_deletion'] ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </div>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input guardrail-toggle" type="checkbox" 
                                                   data-rule="no_data_deletion"
                                                   {{ $rules['no_data_deletion'] ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rule 2: No Critical Changes -->
                        <div class="col-md-6">
                            <div class="guardrail-card card {{ $rules['no_critical_changes'] ? 'enabled' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title">
                                                <i class="ri-error-warning-line text-warning"></i> No Critical Changes
                                            </h5>
                                            <p class="text-muted mb-2">
                                                Blocks AI from making critical system or project changes without review.
                                            </p>
                                            <span class="badge bg-{{ $rules['no_critical_changes'] ? 'success' : 'secondary' }}">
                                                {{ $rules['no_critical_changes'] ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </div>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input guardrail-toggle" type="checkbox" 
                                                   data-rule="no_critical_changes"
                                                   {{ $rules['no_critical_changes'] ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rule 3: No Mass Changes -->
                        <div class="col-md-6">
                            <div class="guardrail-card card {{ $rules['no_mass_changes'] ? 'enabled' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title">
                                                <i class="ri-group-line text-info"></i> No Mass Changes
                                            </h5>
                                            <p class="text-muted mb-2">
                                                Prevents AI from affecting multiple items beyond threshold.
                                            </p>
                                            <span class="badge bg-{{ $rules['no_mass_changes'] ? 'success' : 'secondary' }}">
                                                {{ $rules['no_mass_changes'] ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </div>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input guardrail-toggle" type="checkbox" 
                                                   data-rule="no_mass_changes"
                                                   {{ $rules['no_mass_changes'] ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Rule 4: No Unverified Actions -->
                        <div class="col-md-6">
                            <div class="guardrail-card card {{ $rules['no_unverified_actions'] ? 'enabled' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title">
                                                <i class="ri-shield-cross-line text-primary"></i> No Unverified Actions
                                            </h5>
                                            <p class="text-muted mb-2">
                                                Blocks low-confidence AI decisions from auto-execution.
                                            </p>
                                            <span class="badge bg-{{ $rules['no_unverified_actions'] ? 'success' : 'secondary' }}">
                                                {{ $rules['no_unverified_actions'] ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </div>
                                        <div class="form-check form-switch form-switch-lg">
                                            <input class="form-check-input guardrail-toggle" type="checkbox" 
                                                   data-rule="no_unverified_actions"
                                                   {{ $rules['no_unverified_actions'] ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Thresholds -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-settings-3-line"></i> Thresholds & Limits
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Setting</th>
                                    <th>Description</th>
                                    <th style="width: 200px;">Value</th>
                                    <th style="width: 100px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $threshold_settings = [
                                        'mass_change_limit' => [
                                            'name' => 'Mass Change Limit',
                                            'description' => 'Maximum items affected in one action',
                                            'type' => 'number',
                                            'min' => 1,
                                            'max' => 100,
                                        ],
                                        'min_confidence_score' => [
                                            'name' => 'Minimum Confidence Score',
                                            'description' => 'Minimum confidence for auto-execution (0.0 - 1.0)',
                                            'type' => 'number',
                                            'min' => 0,
                                            'max' => 1,
                                            'step' => 0.01,
                                        ],
                                    ];
                                @endphp

                                @foreach($threshold_settings as $key => $setting)
                                <tr>
                                    <td><strong>{{ $setting['name'] }}</strong></td>
                                    <td class="text-muted">{{ $setting['description'] }}</td>
                                    <td>
                                        <input type="{{ $setting['type'] }}" 
                                               class="form-control threshold-input" 
                                               id="threshold_{{ $key }}"
                                               value="{{ $thresholds[$key] }}"
                                               min="{{ $setting['min'] }}"
                                               max="{{ $setting['max'] }}"
                                               step="{{ $setting['step'] ?? 1 }}">
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-sm btn-primary update-threshold-btn"
                                                data-key="{{ $key }}">
                                            <i class="ri-save-line"></i> Update
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// Toggle guardrail rules
document.querySelectorAll('.guardrail-toggle').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const rule = this.dataset.rule;
        const enabled = this.checked;
        const card = this.closest('.guardrail-card');

        axios.post('{{ route("ai.guardrails.rule.update") }}', {
            rule: rule,
            enabled: enabled,
            _token: '{{ csrf_token() }}'
        })
        .then(response => {
            toastr.success(response.data.message);
            
            // Update card styling
            if (enabled) {
                card.classList.add('enabled');
            } else {
                card.classList.remove('enabled');
            }
            
            // Update badge
            const badge = card.querySelector('.badge');
            if (enabled) {
                badge.classList.remove('bg-secondary');
                badge.classList.add('bg-success');
                badge.textContent = 'Enabled';
            } else {
                badge.classList.remove('bg-success');
                badge.classList.add('bg-secondary');
                badge.textContent = 'Disabled';
            }
        })
        .catch(error => {
            console.error('Toggle failed:', error);
            toastr.error('Failed to update rule. Please try again.');
            this.checked = !enabled; // Revert toggle
        });
    });
});

// Update thresholds
document.querySelectorAll('.update-threshold-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const key = this.dataset.key;
        const input = document.getElementById(`threshold_${key}`);
        const value = parseFloat(input.value);

        axios.post('{{ route("ai.guardrails.threshold.update") }}', {
            key: key,
            value: value,
            _token: '{{ csrf_token() }}'
        })
        .then(response => {
            toastr.success(response.data.message);
        })
        .catch(error => {
            console.error('Update failed:', error);
            if (error.response && error.response.data.message) {
                toastr.error(error.response.data.message);
            } else {
                toastr.error('Failed to update threshold. Please try again.');
            }
        });
    });
});
</script>
@endsection
