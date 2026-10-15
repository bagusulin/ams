@extends('layouts.admin')

@section('content')

<div class="mb-6">

    <h1 class="text-2xl font-bold text-gray-900">
        Dashboard Asset Management
    </h1>

    <p class="mt-1 text-sm text-gray-500">
        Selamat datang di Asset Management System.
    </p>

</div>


<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

    <!-- Total Asset -->
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

        <p class="text-sm font-medium text-gray-500">
            Total Asset
        </p>

        <p class="mt-2 text-3xl font-bold text-gray-900">
            {{ $totalAssets }}
        </p>

        <p class="mt-2 text-xs text-gray-400">
            Semua asset terdaftar
        </p>

    </div>


    <!-- Available -->
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

        <p class="text-sm font-medium text-gray-500">
            Available
        </p>

        <p class="mt-2 text-3xl font-bold text-gray-900">
            {{ $availableAssets }}
        </p>

        <p class="mt-2 text-xs text-gray-400">
            Siap digunakan
        </p>

    </div>


    <!-- Assigned -->
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

        <p class="text-sm font-medium text-gray-500">
            Assigned
        </p>

        <p class="mt-2 text-3xl font-bold text-gray-900">
            {{ $assignedAssets }}
        </p>

        <p class="mt-2 text-xs text-gray-400">
            Sedang digunakan
        </p>

    </div>


    <!-- Maintenance -->
    <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

        <p class="text-sm font-medium text-gray-500">
            Maintenance
        </p>

        <p class="mt-2 text-3xl font-bold text-gray-900">
            {{ $maintenanceAssets }}
        </p>

        <p class="mt-2 text-xs text-gray-400">
            Sedang diperbaiki
        </p>

    </div>

</div>


<!-- Category -->
<div class="mt-6 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

    <p class="text-sm font-medium text-gray-500">
        Active Asset Categories
    </p>

    <p class="mt-2 text-3xl font-bold text-gray-900">
        {{ $totalCategories }}
    </p>

</div>

@endsection