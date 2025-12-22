@extends('layouts.landing')

@section('title', 'SmartCare SIMRS - Pelayanan Kesehatan Modern & Terintegrasi')

@section('content')
    <!-- Navbar -->
    <x-navbar />

    <!-- Hero Section -->
    @include('partials.hero')

    <!-- Stats Section -->
    <x-stats-section :stats="$stats" />

    <!-- Services Section -->
    @include('partials.services', ['services' => $services])

    <!-- SOP Section -->
    @include('partials.sop-management')

    <!-- Features Section -->
    @include('partials.features')

    <!-- CTA Section -->
    @include('partials.cta')

    <!-- Footer -->
    @include('partials.footer')
@endsection
