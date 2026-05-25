@extends('layouts.public')
@section('content')
<section class="hero">
    <div id="heroSlider" class="carousel slide carousel-fade" data-bs-ride="carousel">
        <div class="carousel-inner">
            @php
                $slideImages = [
                    'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1800&q=80',
                    'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1800&q=80',
                    'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1800&q=80',
                ];
            @endphp
            @foreach(__('site.home.slides') as $i => $slide)
            <div class="carousel-item {{ $i === 0 ? 'active' : '' }}" style="background-image:linear-gradient(90deg,rgba(0,75,156,.9),rgba(0,75,156,.35)),url('{{ $slideImages[$i] }}')">
                <div class="container hero-content">
                    <div class="hero-accreditation"><img src="{{ asset('images/epim-accreditation.webp') }}" alt="{{ __('site.accredited') }}"><span>{{ __('site.accredited') }}</span></div>
                    <span class="eyebrow">{{ __('site.home.eyebrow') }}</span><h1>{{ $slide['title'] }}</h1><p>{{ $slide['text'] }}</p><div class="hero-actions"><a class="btn btn-gold btn-lg" href="{{ route('admission') }}">{{ __('site.actions.request_registration') }}</a><a class="btn btn-light btn-lg" href="{{ route('formations') }}">{{ __('site.actions.view_formations') }}</a></div></div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<section class="stats-band">
    <div class="container grid-4">
        @foreach(__('site.home.stats') as $stat)
            <div class="stat reveal"><strong data-count="{{ preg_replace('/\D/','',$stat['value']) }}">{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span></div>
        @endforeach
    </div>
</section>

<section class="section">
    <div class="container split">
        <div class="reveal"><span class="eyebrow">{{ __('site.home.presentation') }}</span><h2>{{ __('site.home.presentation_title') }}</h2><p>{{ __('site.home.presentation_text') }}</p><a href="{{ route('about') }}" class="btn btn-primary">{{ __('site.actions.discover_epim') }}</a></div>
        <div class="video-card video-embed reveal"><iframe src="https://www.youtube.com/embed/7DmLRoZG11M" title="{{ __('site.home.video_title') }}" allowfullscreen loading="lazy"></iframe></div>
    </div>
</section>

<section class="section muted">
    <div class="container"><div class="section-head"><span class="eyebrow">{{ __('site.home.formations_eyebrow') }}</span><h2>{{ __('site.home.formations_title') }}</h2></div><div class="cards-3">
        @foreach($formations as $formation)
            <article class="program-card reveal"><img src="{{ $formation->image }}" alt="{{ $formation->title }}" loading="lazy"><div><span>{{ $formation->duration }}</span><h3>{{ $formation->title }}</h3><p>{{ $formation->description }}</p><a href="{{ route('formations.show', $formation) }}">{{ __('site.home.program_details') }} <i class="bi bi-arrow-right"></i></a></div></article>
        @endforeach
    </div></div>
</section>

<section class="section">
    <div class="container"><div class="section-head"><span class="eyebrow">{{ __('site.home.why_eyebrow') }}</span><h2>{{ __('site.home.why_title') }}</h2></div><div class="cards-4">
        @foreach(__('site.home.features') as $item)
            <div class="feature reveal"><i class="bi {{ $item['icon'] }}"></i><h3>{{ $item['title'] }}</h3><p>{{ __('site.home.why_text') }}</p></div>
        @endforeach
    </div></div>
</section>

<section class="section muted">
    <div class="container"><div class="section-head"><span class="eyebrow">{{ __('site.home.news_eyebrow') }}</span><h2>{{ __('site.home.news_title') }}</h2></div><div class="cards-3">
        @foreach($posts as $post)
            <article class="news-card reveal"><img src="{{ $post->image }}" alt="{{ $post->title }}" loading="lazy"><div><span>{{ $post->category }}</span><h3>{{ $post->title }}</h3><p>{{ $post->excerpt }}</p><a href="{{ route('blog.show', $post) }}">{{ __('site.actions.read') }}</a></div></article>
        @endforeach
    </div></div>
</section>

<section class="cta"><div class="container"><h2>{{ __('site.home.cta_title') }}</h2><div><a href="{{ route('admission') }}" class="btn btn-gold btn-lg">{{ __('site.home.registration') }}</a><a href="{{ route('contact') }}" class="btn btn-light btn-lg">{{ __('site.nav.contact') }}</a></div></div></section>
<section class="map"><iframe loading="lazy" src="https://www.google.com/maps?q=EPIM%20Meknes&output=embed"></iframe><a class="map-link" href="https://maps.app.goo.gl/L5pTj3tq9T6aHXsi6" target="_blank" rel="noopener">{{ __('site.actions.open_maps') }}</a></section>
@endsection
