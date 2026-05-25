@extends('layouts.public')
@section('content')
@include('public.partials')
@php epimHero($section, $title); @endphp

<section class="section muted">
    <div class="container">
        <div class="construction-panel reveal">
            <div class="construction-icon"><i class="bi bi-tools"></i></div>
            <span class="eyebrow">{{ $section }}</span>
            <h2>{{ $title }}</h2>
            <p>{{ $message }}</p>
            <div class="hero-actions">
                <a class="btn btn-gold" href="{{ route('home') }}">Retour à l’accueil</a>
                <a class="btn btn-outline-primary" href="{{ route('contact') }}">Contacter EPIM</a>
            </div>
        </div>
    </div>
</section>
@endsection
