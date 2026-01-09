@extends('layouts.master')

@section('title') AI Settings @endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18"><i class="ri-settings-3-line"></i> AI Settings</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#general" role="tab">
                                <i class="ri-settings-line me-1 align-middle"></i> General
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#safety" role="tab">
                                <i class="ri-shield-line me-1 align-middle"></i> Safety
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#performance" role="tab">
                                <i class="ri-speed-line me-1 align-middle"></i> Performance
                            </a>
                        </li>
                    </ul>

                    <form id="settingsForm">
                        @csrf
                        <div class="tab-content p-3 text-muted">
                            <!-- General Settings -->
                            <div class="tab-pane active" id="general" role="tabpanel">
                                @foreach($settings['general'] ?? [] as $setting)
                                <div class="mb-3 row">
                                    <label class="col-md-3 col-form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control setting-input" 
                                            name="settings[{{ $loop->index }}][value]" 
                                            value="{{ $setting->value }}">
                                        <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                                        <input type="hidden" name="settings[{{ $loop->index }}][group]" value="general">
                                        @if($setting->description)
                                            <p class="text-muted mb-0"><small>{{ $setting->description }}</small></p>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                                @if(empty($settings['general']))
                                    <p class="text-muted">No general settings found.</p>
                                @endif
                            </div>

                            <!-- Safety Settings -->
                            <div class="tab-pane" id="safety" role="tabpanel">
                                @foreach($settings['safety'] ?? [] as $setting)
                                <div class="mb-3 row">
                                    <label class="col-md-3 col-form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control setting-input" 
                                            name="settings[{{ $loop->index + 1000 }}][value]" 
                                            value="{{ $setting->value }}">
                                        <input type="hidden" name="settings[{{ $loop->index + 1000 }}][key]" value="{{ $setting->key }}">
                                        <input type="hidden" name="settings[{{ $loop->index + 1000 }}][group]" value="safety">
                                    </div>
                                </div>
                                @endforeach
                                @if(empty($settings['safety']))
                                    <p class="text-muted">No safety settings found.</p>
                                @endif
                            </div>

                            <!-- Performance Settings -->
                            <div class="tab-pane" id="performance" role="tabpanel">
                                @foreach($settings['performance'] ?? [] as $setting)
                                <div class="mb-3 row">
                                    <label class="col-md-3 col-form-label">{{ ucwords(str_replace('_', ' ', $setting->key)) }}</label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control setting-input" 
                                            name="settings[{{ $loop->index + 2000 }}][value]" 
                                            value="{{ $setting->value }}">
                                        <input type="hidden" name="settings[{{ $loop->index + 2000 }}][key]" value="{{ $setting->key }}">
                                        <input type="hidden" name="settings[{{ $loop->index + 2000 }}][group]" value="performance">
                                    </div>
                                </div>
                                @endforeach
                                @if(empty($settings['performance']))
                                    <p class="text-muted">No performance settings found.</p>
                                @endif
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary" id="saveBtn">
                                    <i class="ri-save-line"></i> Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.getElementById('settingsForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('saveBtn');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

    // Collect data properly
    const formData = new FormData(this);
    const settings = [];
    
    // We need to parse the flat name="settings[i][key]" structure back into objects
    // A simple way is to rely on PHP parsing, but for JSON post we build it:
    // Actually, FormData handles array naming well, but let's just use form submission
    // Or axios with FormData object directly, which Laravel accepts.
    
    try {
        const response = await axios.post('{{ route("ai.settings.update") }}', formData);
        
        if (response.data.success) {
            toastr.success(response.data.message);
        }
    } catch (error) {
        toastr.error(error.response?.data?.message || 'Failed to update settings');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
});
</script>
@endsection
