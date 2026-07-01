<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Administration EPIM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body class="admin-body">
<aside class="admin-sidebar"><a class="admin-brand" href="{{ route('admin.dashboard') }}">EPIM Admin</a>@foreach(['formations'=>'Formations','actualites'=>'Actualités','galerie'=>'Galerie','inscriptions'=>'Inscriptions','messages'=>'Messages','partenaires'=>'Partenaires','pages'=>'Pages / SEO','rubriques'=>'Rubriques','parametres'=>'Paramètres','utilisateurs'=>'Utilisateurs'] as $key=>$label)<a href="{{ $key === 'inscriptions' ? route('admin.applications.index') : route('admin.resource',$key) }}"><i class="bi bi-grid"></i> {{ $label }}</a>@endforeach<form method="post" action="{{ route('logout') }}">@csrf<button><i class="bi bi-box-arrow-right"></i> Déconnexion</button></form></aside>
<main class="admin-main"><header><div><span>Connecté : {{ auth()->user()->name }}</span><h1>@yield('title','Dashboard')</h1></div><a class="btn btn-outline-primary" href="{{ route('home') }}">Voir le site</a></header>@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif @if($errors->any())<div class="alert alert-danger"><strong>Vérifiez le formulaire.</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif @yield('content')</main>
</body>
</html>

