<x-app-layout>
    <x-slot name="pageTitle">Dashboard</x-slot>
    <x-slot name="breadcrumb">
        @if(auth()->user()->isVehicleOwner())
            Selamat datang kembali
        @elseif(auth()->user()->isWorkshop())
            Panel Bengkel
        @elseif(auth()->user()->isSuperAdmin())
            Admin Panel
        @endif
    </x-slot>

    @if(auth()->user()->isVehicleOwner())
        @include('dashboard.partials.vehicle-owner')
    @elseif(auth()->user()->isWorkshop())
        @include('dashboard.partials.workshop')
    @elseif(auth()->user()->isSuperAdmin())
        @include('dashboard.partials.super-admin')
    @else
        @include('dashboard.partials.vehicle-owner')
    @endif
</x-app-layout>
