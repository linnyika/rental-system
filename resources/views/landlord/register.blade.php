@extends('layouts.landlord')

@section('title', 'Register Property')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Register Property</h1>
            <p class="text-muted mb-0">Register a new property in the system</p>
        </div>
    </div>

    <div class="card panel">
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <x-forms.input name="property_name" label="Property Name" required />
                    </div>
                    <div class="col-md-6">
                        <x-forms.select name="property_type" label="Property Type" :options="[
                            'apartment' => 'Apartment',
                            'villa' => 'Villa',
                            'house' => 'House',
                            'commercial' => 'Commercial',
                            'land' => 'Land',
                        ]" />
                    </div>
                    <div class="col-12">
                        <x-forms.input name="address" label="Address" required />
                    </div>
                    <div class="col-md-4">
                        <x-forms.input name="city" label="City" required />
                    </div>
                    <div class="col-md-4">
                        <x-forms.input name="state" label="State/County" required />
                    </div>
                    <div class="col-md-4">
                        <x-forms.input name="zip_code" label="ZIP Code" />
                    </div>
                    <div class="col-md-4">
                        <x-forms.input name="total_units" label="Total Units" type="number" required />
                    </div>
                    <div class="col-md-4">
                        <x-forms.input name="base_rent" label="Base Rent (KES)" type="number" required />
                    </div>
                    <div class="col-md-4">
                        <x-forms.select name="status" label="Status" :options="['active' => 'Active', 'inactive' => 'Inactive']" />
                    </div>
                    <div class="col-12">
                        <x-forms.text-area name="description" label="Description" rows="4" />
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label class="form-label">Property Documents</label>
                            <input type="file" class="form-control" multiple accept=".pdf,.doc,.docx">
                            <div class="form-text">Upload registration documents, deeds, etc.</div>
                        </div>
                    </div>
                </div>

                <x-forms.form-actions submitLabel="Register Property" cancelUrl="{{ route('landlord.dashboard') }}" />
            </form>
        </div>
    </div>
@endsection
