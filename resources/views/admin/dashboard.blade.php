@extends('layouts.admin')
@section('title','Dashboard')
@section('content')
<div class="admin-stats">
    @foreach($stats as $label => $value)
        <div><span>{{ ucfirst($label) }}</span><strong>{{ $value }}</strong></div>
    @endforeach
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-7">
        <div class="admin-panel">
            <div class="admin-panel-head"><h2>Inscriptions récentes</h2><a href="{{ route('admin.resource','inscriptions') }}">Voir tout</a></div>
            <table class="table">
                <thead><tr><th>Dossier</th><th>Candidat</th><th>Filiere</th><th>Statut</th></tr></thead>
                <tbody>
                @forelse($applications as $a)
                    <tr><td>{{ $a->dossier_number }}</td><td>{{ $a->first_name }} {{ $a->last_name }}</td><td>{{ $a->formation?->title ?: '-' }}</td><td><span class="badge text-bg-primary">{{ $a->status }}</span></td></tr>
                @empty
                    <tr><td colspan="4">Aucune inscription pour le moment.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="admin-panel">
            <div class="admin-panel-head"><h2>Messages contact</h2><a href="{{ route('admin.resource','messages') }}">Traiter</a></div>
            @forelse($messages as $m)
                <div class="notif"><strong>{{ $m->subject }}</strong><span>{{ $m->name }} - {{ $m->created_at->diffForHumans() }} - {{ $m->status }}</span></div>
            @empty
                <p>Aucun message récent.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-4">
        <div class="admin-panel">
            <h2>Formations par famille</h2>
            @foreach($formationCategories as $category)
                <div class="metric-row"><span>{{ $category->category }}</span><strong>{{ $category->total }}</strong></div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-panel">
            <h2>Statuts dossiers</h2>
            @forelse($applicationStatuses as $status)
                <div class="metric-row"><span>{{ $status->status }}</span><strong>{{ $status->total }}</strong></div>
            @empty
                <p>Aucun dossier.</p>
            @endforelse
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-panel">
            <h2>Articles récents</h2>
            @foreach($latestPosts as $post)
                <div class="notif"><strong>{{ $post->title }}</strong><span>{{ $post->category }} - {{ $post->published_at?->format('d/m/Y') }}</span></div>
            @endforeach
        </div>
    </div>
</div>

<div class="admin-panel mt-4">
    <h2>Actions rapides</h2>
    <div class="quick-actions">
        <a class="btn btn-primary" href="{{ route('admin.resource','actualites') }}">Publier une actualité</a>
        <a class="btn btn-primary" href="{{ route('admin.resource','formations') }}">Gérer les formations</a>
        <a class="btn btn-primary" href="{{ route('admin.resource','partenaires') }}">Ajouter un partenaire</a>
        <a class="btn btn-outline-primary" href="{{ route('home') }}">Voir le site</a>
    </div>
</div>
@endsection
