@extends('layouts.public')
@section('content')
@include('public.partials')
@php epimHero('Galerie', 'Campus, salles, événements, workshops, vidéos et remise de diplômes.'); @endphp
<section class="section muted"><div class="container"><div class="pills"><a href="{{ route('gallery') }}">Tous</a>@foreach($categories as $c)<a href="{{ route('gallery',['category'=>$c]) }}">{{ $c }}</a>@endforeach</div><div class="gallery-grid">@foreach($items as $item)<figure><img src="{{ $item->path }}" alt="{{ $item->title }}"><figcaption>{{ $item->title }}<span>{{ $item->category }}</span></figcaption></figure>@endforeach</div><div class="mt-4">{{ $items->links() }}</div></div></section>
@endsection
