@extends('layouts.public')
@section('content')
@include('public.partials')
@php epimHero($post->title, $post->excerpt, $post->image); @endphp
<section class="section"><div class="container narrow"><span class="badge text-bg-warning">{{ $post->category }}</span><article class="article-body">{!! nl2br(e($post->body)) !!}</article><div class="share"><span>Partager</span><i class="bi bi-facebook"></i><i class="bi bi-linkedin"></i><i class="bi bi-whatsapp"></i></div><div class="comment-box"><h3>Commentaires</h3><p>Les commentaires sont moderes par l administration EPIM.</p><textarea class="form-control" placeholder="Votre commentaire"></textarea><button class="btn btn-primary mt-3">Publier</button></div></div></section>
@endsection
