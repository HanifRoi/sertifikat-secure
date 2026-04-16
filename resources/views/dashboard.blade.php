@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Total Sertifikat</p>
        <h3 class="text-3xl font-bold text-gray-800 mt-2">300+</h3>
        <p class="text-xs text-green-500 mt-1">Data dari Cazh Academy</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <p class="text-sm text-gray-500 uppercase font-bold tracking-wider">Status Sistem</p>
        <h3 class="text-3xl font-bold text-indigo-600 mt-2">Active</h3>
        <p class="text-xs text-gray-400 mt-1">SHA-3 & RSA Ready</p>
    </div>
</div>
@endsection