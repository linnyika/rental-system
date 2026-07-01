@extends('layouts.admin')

@section('title', 'Profile')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">My Profile</h1>
            <p class="text-muted mb-0">Manage your profile information</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card panel text-center">
                <div class="card-body">
                    <div
                        class="rounded-circle bg-primary mx-auto mb-3 d-flex align-items-center justify-content-center text-white avatar-120">
                        AU
                    </div>
                    <h5>Admin User</h5>
                    <p class="text-muted small">admin@system.com</p>
                    <span class="badge bg-primary">Administrator</span>
                    <div class="mt-3">
                        <button class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-camera me-2"></i> Change Photo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card panel">
                <div class="card-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <x-forms.input name="first_name" label="First Name" value="Admin" required />
                            </div>
                            <div class="col-md-6">
                                <x-forms.input name="last_name" label="Last Name" value="User" required />
                            </div>
                            <div class="col-12">
                                <x-forms.input name="email" label="Email Address" value="admin@system.com" type="email"
                                    required />
                            </div>
                            <div class="col-12">
                                <x-forms.input name="phone" label="Phone Number" value="+254 712 345 678" />
                            </div>
                            <div class="col-12">
                                <x-forms.text-area name="bio" label="Bio" rows="3"
                                    value="System administrator managing the rental platform." />
                            </div>
                        </div>
                        <x-forms.form-actions submitLabel="Update Profile" />
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
