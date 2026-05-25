@extends('layouts.public')
@section('content')
@include('public.partials')
@php epimHero('Actualités', 'Blog dynamique, événements, conseils et vie de l’école.'); @endphp
<section class="section muted"><div class="container"><form class="filter-bar"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Recherche"><select name="category" class="form-select"><option value="">Toutes catégories</option>@foreach($categories as $c)<option @selected(request('category')===$c)>{{ $c }}</option>@endforeach</select><button class="btn btn-primary">{{ __('site.actions.filter') }}</button></form><div class="cards-3">@foreach($posts as $post)<article class="news-card"><img src="{{ $post->image }}" alt="{{ $post->title }}"><div><span>{{ $post->category }}</span><h3>{{ $post->title }}</h3><p>{{ $post->excerpt }}</p><a href="{{ route('blog.show',$post) }}">{{ __('site.actions.read_article') }}</a></div></article>@endforeach</div><div class="mt-4">{{ $posts->links() }}</div></div></section>
@endsection
