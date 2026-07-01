@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Notification Settings</h1>
            <p class="text-muted mb-0">Configure your notification preferences</p>
        </div>
    </div>

    <div class="card panel">
        <div class="card-body">
            <form>
                <h6 class="fw-semibold mb-3">Email Notifications</h6>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_payments" checked>
                    <label class="form-check-label" for="email_payments">
                        Payment confirmations
                        <div class="small text-muted">Receive email when payments are received</div>
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_maintenance" checked>
                    <label class="form-check-label" for="email_maintenance">
                        Maintenance updates
                        <div class="small text-muted">Receive email about maintenance requests</div>
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_new_users">
                    <label class="form-check-label" for="email_new_users">
                        New user registrations
                        <div class="small text-muted">Receive email when new users register</div>
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="email_reports" checked>
                    <label class="form-check-label" for="email_reports">
                        Report generation
                        <div class="small text-muted">Receive email when reports are ready</div>
                    </label>
                </div>

                <hr>

                <h6 class="fw-semibold mb-3">System Notifications</h6>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="system_updates" checked>
                    <label class="form-check-label" for="system_updates">
                        System updates
                        <div class="small text-muted">Notifications about system updates</div>
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="system_security" checked>
                    <label class="form-check-label" for="system_security">
                        Security alerts
                        <div class="small text-muted">Critical security notifications</div>
                    </label>
                </div>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="system_backup">
                    <label class="form-check-label" for="system_backup">
                        Backup notifications
                        <div class="small text-muted">Backup completion and failure alerts</div>
                    </label>
                </div>

                <x-forms.form-actions submitLabel="Save Preferences" />
            </form>
        </div>
    </div>
@endsection
