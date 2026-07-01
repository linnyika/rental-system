@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">System Settings</h1>
            <p class="text-muted mb-0">Configure system-wide settings</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card panel">
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <x-forms.input name="system_name" label="System Name" value="Rental System" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input name="system_email" label="System Email" value="support@rentalsystem.com" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input name="currency" label="Currency" value="KES" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input name="timezone" label="Timezone" value="Africa/Nairobi" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.select name="date_format" label="Date Format" :options="[
                                    'YYYY-MM-DD' => 'YYYY-MM-DD',
                                    'DD/MM/YYYY' => 'DD/MM/YYYY',
                                    'MM/DD/YYYY' => 'MM/DD/YYYY',
                                ]"
                                    selected="YYYY-MM-DD" />
                            </div>
                            <div class="col-md-6">
                                <x-forms.select name="language" label="Language" :options="['en' => 'English', 'sw' => 'Swahili']" selected="en" />
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="maintenance_mode">
                                    <label class="form-check-label" for="maintenance_mode">
                                        Enable Maintenance Mode
                                    </label>
                                </div>
                            </div>
                        </div>
                        <x-forms.form-actions submitLabel="Save Settings" />
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card panel">
                <div class="card-header bg-white">
                    <div class="section-title fw-semibold">Quick Actions</div>
                </div>
                <div class="list-group list-group-flush">
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="#">
                        <span><i class="fas fa-user me-2"></i>Profile Settings</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="#">
                        <span><i class="fas fa-bell me-2"></i>Notifications</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="#">
                        <span><i class="fas fa-database me-2"></i>Backup</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                        href="#">
                        <span><i class="fas fa-shield-alt me-2"></i>Security</span>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>

            <div class="card panel">
                <div class="card-body text-center">
                    <div class="small text-muted">System Version</div>
                    <div class="h5 mb-0">v1.0.0</div>
                    <div class="small text-muted">Last updated: Jan 15, 2024</div>
                </div>
            </div>
        </div>
    </div>
@endsection
