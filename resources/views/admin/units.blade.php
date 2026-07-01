@extends('layouts.admin')

@section('title', 'Units Management')

@section('page-title', 'Units')
@section('breadcrumb-items',
    '
    <li class="breadcrumb-item"><a href="' .
        route('admin.dashboard') .
        '">Dashboard</a></li>
    <li class="breadcrumb-item active">Units</li>
    ')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <x-cards.stat-card icon="door-open" iconClass="primary" number="48" label="Total Units" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <x-cards.stat-card icon="people" iconClass="success" number="34" label="Occupied" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <x-cards.stat-card icon="house" iconClass="warning" number="10" label="Vacant" />
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <x-cards.stat-card icon="wrench" iconClass="danger" number="4" label="Maintenance" />
        </div>
    </div>

    @php
        $columns = ['Unit', 'Property', 'Type', 'Tenant', 'Rent', 'Status'];
        $rows = [
            ['A-101', 'Sunset Apartments', 'Studio', 'James Wilson', '$1,200', '<span class="badge-status active">Occupied</span>'],
            ['A-102', 'Sunset Apartments', '1 Bedroom', 'Maria Rodriguez', '$1,350', '<span class="badge-status active">Occupied</span>'],
            ['B-201', 'Green Valley Estate', '2 Bedroom', '—', '$1,500', '<span class="badge-status pending">Vacant</span>'],
            ['C-301', 'Harbor View Towers', '3 Bedroom', 'Robert Taylor', '$1,950', '<span class="badge-status active">Occupied</span>'],
            ['D-101', 'Royal Gardens', '2 Bedroom', 'Amanda Lee', '$1,600', '<span class="badge-status info">Maintenance</span>'],
        ];
    @endphp

    <x-tables.table :columns="$columns" :rows="$rows" :actions="true" searchPlaceholder="Search units..."
        :filters="[
            ['label' => 'Status', 'options' => ['All', 'Occupied', 'Vacant', 'Maintenance']],
            ['label' => 'Type', 'options' => ['Studio', '1 Bedroom', '2 Bedroom', '3 Bedroom']],
        ]" addRoute="#" addLabel="Add Unit" />

    <x-tables.pagination total="48" perPage="10" currentPage="1" />
@endsection
