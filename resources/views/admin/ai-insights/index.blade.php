@extends('layouts.master')

@section('title') AI Insights Dashboard @endsection

@section('css')
<style>
.insight-card {
    transition: all 0.3s ease;
    border-radius: 10px;
}
.insight-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}
.metric-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 20px;
}
.chart-container {
    position: relative;
    height: 300px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">
                    <i class="ri-lightbulb-flash-line"></i> AI Insights Dashboard
                </h4>
                <div class="page-title-right">
                    <button class="btn btn-sm btn-primary" onclick="refreshData()">
                        <i class="ri-refresh-line"></i> Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Row -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="metric-card">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-2 opacity-75 text-white">Health Score</p>
                        <h2 class="mb-0 text-white">{{ number_format($metrics['health_score'], 1) }}%</h2>
                    </div>
                    <div>
                        <i class="ri-heart-pulse-line font-size-48 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-2 opacity-75 text-white">Acceptance Rate</p>
                        <h2 class="mb-0 text-white">{{ number_format($metrics['acceptance_rate'], 1) }}%</h2>
                    </div>
                    <div>
                        <i class="ri-checkbox-circle-line font-size-48 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-2 opacity-75 text-white">Avg Confidence</p>
                        <h2 class="mb-0 text-white">{{ number_format($metrics['avg_confidence'] * 100, 1) }}%</h2>
                    </div>
                    <div>
                        <i class="ri-pie-chart-line font-size-48 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="metric-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <p class="mb-2 opacity-75 text-white">Pending Review</p>
                        <h2 class="mb-0 text-white">{{ $metrics['pending_count'] }}</h2>
                    </div>
                    <div>
                        <i class="ri-time-line font-size-48 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Decision Trend Chart -->
        <div class="col-xl-6">
            <div class="card insight-card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-line-chart-line text-primary"></i> Decision Trend (Last 7 Days)
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="decisionTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confidence Distribution Chart -->
        <div class="col-xl-6">
            <div class="card insight-card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-pie-chart-line text-success"></i> Confidence Distribution
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="confidenceDistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row">
        <!-- Decision Types Breakdown -->
        <div class="col-xl-6">
            <div class="card insight-card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-bar-chart-line text-info"></i> Decision Types
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="typeBreakdownChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Actions Distribution -->
        <div class="col-xl-6">
            <div class="card insight-card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-donut-chart-line text-warning"></i> User Actions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="actionDistChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Recent Decisions Timeline -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="ri-history-line"></i> Recent AI Decisions
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Type</th>
                                    <th>Related To</th>
                                    <th>Recommendation</th>
                                    <th>Confidence</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDecisions as $decision)
                                <tr>
                                    <td>
                                        <span class="badge bg-soft-primary text-primary">
                                            {{ ucfirst(str_replace('_', ' ', $decision->decision_type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($decision->task)
                                            <i class="ri-task-line"></i> {{ Str::limit($decision->task->title, 25) }}
                                        @elseif($decision->project)
                                            <i class="ri-folder-3-line"></i> {{ Str::limit($decision->project->title, 25) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($decision->recommendation, 40) }}</td>
                                    <td>
                                        <div class="progress" style="height: 8px; width: 60px;">
                                            <div class="progress-bar bg-{{ $decision->confidence_score >= 0.7 ? 'success' : ($decision->confidence_score >= 0.5 ? 'warning' : 'danger') }}" 
                                                 style="width: {{ $decision->confidence_score * 100 }}%"></div>
                                        </div>
                                        <small>{{ number_format($decision->confidence_score * 100) }}%</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ 
                                            $decision->user_action === 'accepted' ? 'success' : 
                                            ($decision->user_action === 'rejected' ? 'danger' : 
                                            ($decision->user_action === 'modified' ? 'info' : 'warning')) 
                                        }}">
                                            {{ ucfirst($decision->user_action) }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">{{ $decision->created_at->diffForHumans() }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        No recent decisions found
                                    </td>
                                </tr>
                                @endforelse
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart.js configuration
const chartData = @json($chartData);

// Decision Trend Chart (Line)
const decisionTrendCtx = document.getElementById('decisionTrendChart').getContext('2d');
new Chart(decisionTrendCtx, {
    type: 'line',
    data: {
        labels: chartData.decision_trend.labels,
        datasets: [{
            label: 'Decisions',
            data: chartData.decision_trend.data,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Confidence Distribution Chart (Doughnut)
const confidenceDistCtx = document.getElementById('confidenceDistChart').getContext('2d');
new Chart(confidenceDistCtx, {
    type: 'doughnut',
    data: {
        labels: ['High (≥80%)', 'Medium (60-80%)', 'Low (<60%)'],
        datasets: [{
            data: [
                chartData.confidence_distribution.high,
                chartData.confidence_distribution.medium,
                chartData.confidence_distribution.low
            ],
            backgroundColor: [
                'rgb(52, 195, 143)',
                'rgb(241, 180, 76)',
                'rgb(244, 106, 106)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Decision Types Breakdown (Bar)
const typeBreakdownCtx = document.getElementById('typeBreakdownChart').getContext('2d');
new Chart(typeBreakdownCtx, {
    type: 'bar',
    data: {
        labels: Object.keys(chartData.type_breakdown).map(k => k.replace('_', ' ')),
        datasets: [{
            label: 'Count',
            data: Object.values(chartData.type_breakdown),
            backgroundColor: 'rgba(91, 115, 232, 0.7)',
            borderColor: 'rgb(91, 115, 232)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// User Actions Distribution (Pie)
const actionDistCtx = document.getElementById('actionDistChart').getContext('2d');
new Chart(actionDistCtx, {
    type: 'pie',
    data: {
        labels: Object.keys(chartData.action_distribution).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
        datasets: [{
            data: Object.values(chartData.action_distribution),
            backgroundColor: [
                'rgb(52, 195, 143)',
                'rgb(244, 106, 106)',
                'rgb(91, 115, 232)',
                'rgb(241, 180, 76)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Refresh data function
function refreshData() {
    location.reload();
}
</script>
@endsection
