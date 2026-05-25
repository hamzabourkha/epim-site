@extends('layouts.public')
@section('content')
@include('public.partials')
@php epimHero(__('site.formations.hero_title'), __('site.formations.hero_text')); @endphp

<section class="section muted">
    <div class="container">
        <div class="formation-family-grid">
            @foreach($categories as $key => $category)
                <a class="formation-family-card reveal" href="#{{ \Illuminate\Support\Str::slug($key) }}">
                    <i class="bi {{ $category['icon'] }}"></i>
                    <span>{{ $category['badge'] }}</span>
                    <h2>{{ $category['title'] }}</h2>
                    <p>{{ $category['subtitle'] }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

@foreach($categories as $key => $category)
    <section class="section {{ $loop->even ? 'muted' : '' }}" id="{{ \Illuminate\Support\Str::slug($key) }}">
        <div class="container">
            <div class="formation-category-head">
                <div>
                    <span class="eyebrow">{{ $category['badge'] }}</span>
                    <h2>{{ $category['title'] }}</h2>
                    <p>{{ $category['description'] }}</p>
                </div>
                @if($key === 'Formations diplômantes')
                    <div class="state-accredited">
                        <img src="{{ asset('images/epim-accreditation.webp') }}" alt="{{ __('site.accredited') }}">
                        <strong>{{ __('site.formations.technician') }}</strong>
                        <span>{{ __('site.formations.two_years') }}</span>
                    </div>
                @endif
            </div>

            <div class="cards-3">
                @forelse($formations->get($category['db_key'], collect()) as $formation)
                    <article class="program-card reveal">
                        <img src="{{ $formation->image }}" alt="{{ $formation->title }}" loading="lazy">
                        <div>
                            <span>{{ $formation->duration }} - {{ $formation->insertion_rate }}% insertion</span>
                            <h3>{{ $formation->title }}</h3>
                            <p>{{ $formation->description }}</p>
                            <a href="{{ route('formations.show', $formation) }}">{{ __('site.formations.complete_sheet') }} <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </article>
                @empty
                    <div class="feature">
                        <h3>{{ __('site.formations.request_program') }}</h3>
                        <p>{{ __('site.formations.request_program_text') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endforeach
@endsection
