@extends('layouts.admin')
@section('title', $title)
@section('content')
@php
    $readonlyResources = ['inscriptions', 'messages'];
    $statusOptions = [
        'inscriptions' => ['nouveau' => 'Nouveau', 'preinscription_rapide' => 'Préinscription rapide', 'en_traitement' => 'En traitement', 'valide' => 'Validé', 'refuse' => 'Refusé', 'archive' => 'Archivé'],
        'messages' => ['non_lu' => 'Non lu', 'lu' => 'Lu', 'en_traitement' => 'En traitement', 'traite' => 'Traité', 'archive' => 'Archivé'],
    ];
    $formationCategories = [
        'Formations diplomantes' => 'Formations diplômantes',
        'Formation continue' => 'Formation continue',
        'Formation a la carte certifiante' => 'Formation à la carte certifiante',
        'Formation pour les entreprises' => 'Formation pour les entreprises',
    ];
    $selectedStatus = request('status');
@endphp

@if(in_array($resource, ['inscriptions', 'messages', 'formations'], true))
    <section class="admin-panel admin-filters mb-4">
        <form method="get" class="admin-filter-form">
            <label class="field"><span>Recherche</span><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Nom, téléphone, email, sujet..."></label>
            @if(in_array($resource, $readonlyResources, true))
                <label class="field"><span>Statut</span><select class="form-select" name="status"><option value="">Tous les statuts</option>@foreach($statusOptions[$resource] as $value => $label)<option value="{{ $value }}" @selected($selectedStatus === $value)>{{ $label }}</option>@endforeach</select></label>
            @endif
            @if($resource === 'formations')
                <label class="field"><span>Famille</span><select class="form-select" name="category"><option value="">Toutes les familles</option>@foreach($formationCategories as $value => $label)<option value="{{ $value }}" @selected(request('category') === $value)>{{ $label }}</option>@endforeach</select></label>
            @endif
            <div class="admin-filter-actions"><button class="btn btn-primary">Filtrer</button><a class="btn btn-outline-secondary" href="{{ route('admin.resource', $resource) }}">Réinitialiser</a></div>
        </form>
    </section>
@endif

@unless(in_array($resource, $readonlyResources, true))
    @if($editItem)
        <section class="admin-panel admin-editor mb-4" id="editor">
            <div class="admin-panel-head"><div><span class="admin-kicker">Modification</span><h2>{{ $editItem->title ?? $editItem->name ?? $editItem->value['label'] ?? $editItem->key ?? 'Élément' }}</h2></div><a class="btn btn-outline-secondary" href="{{ route('admin.resource', $resource) }}">Annuler</a></div>
            <form method="post" action="{{ route('admin.resource.update.post', [$resource, $editItem->id]) }}" class="admin-form admin-form-pro" enctype="multipart/form-data">
                @csrf
                @include('admin.resource-form', ['item' => $editItem, 'resource' => $resource, 'formationCategories' => $formationCategories])
                <div class="admin-form-actions"><button class="btn btn-primary">Mettre à jour</button><a class="btn btn-outline-secondary" href="{{ route('admin.resource', $resource) }}">Annuler</a></div>
            </form>
        </section>
    @else
        <details class="admin-panel admin-editor mb-4">
            <summary class="admin-summary">Ajouter un élément</summary>
            <form method="post" action="{{ route('admin.resource.store', $resource) }}" class="admin-form admin-form-pro mt-3" enctype="multipart/form-data">
                @csrf
                @include('admin.resource-form', ['item' => null, 'resource' => $resource, 'formationCategories' => $formationCategories])
                <div class="admin-form-actions"><button class="btn btn-primary">Enregistrer</button></div>
            </form>
        </details>
    @endif
@else
    <div class="admin-panel mb-4">
        <h2>{{ $resource === 'inscriptions' ? 'Traitement des dossiers candidats' : 'Traitement des messages contact' }}</h2>
        <p class="mb-0">Ouvrez une fiche avec “Traiter”, changez son statut et utilisez les liens rapides pour rappeler ou répondre.</p>
    </div>
@endunless

@if(in_array($resource, $readonlyResources, true) && $editItem)
    <section class="admin-panel admin-treatment mb-4" id="editor">
        <div class="admin-panel-head"><div><span class="admin-kicker">Fiche de traitement</span><h2>{{ $resource === 'inscriptions' ? $editItem->dossier_number : $editItem->subject }}</h2></div><a class="btn btn-outline-secondary" href="{{ route('admin.resource', $resource) }}">Fermer</a></div>
        <div class="treatment-grid">
            <div class="treatment-card">
                @if($resource === 'inscriptions')
                    <dl><dt>Candidat</dt><dd>{{ $editItem->first_name }} {{ $editItem->last_name }}</dd><dt>Téléphone</dt><dd><a href="tel:{{ $editItem->phone }}">{{ $editItem->phone }}</a></dd><dt>Email</dt><dd><a href="mailto:{{ $editItem->email }}">{{ $editItem->email }}</a></dd><dt>Ville</dt><dd>{{ $editItem->city ?: '-' }}</dd><dt>Niveau</dt><dd>{{ $editItem->education_level ?: '-' }}</dd><dt>Formation</dt><dd>{{ $editItem->formation?->title ?: '-' }}</dd><dt>Message</dt><dd>{{ $editItem->message ?: '-' }}</dd></dl>
                    @if(!empty($editItem->documents))<h3>Documents</h3><div class="doc-links">@foreach($editItem->documents as $label => $path)<a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/'.$path) }}" target="_blank">{{ strtoupper($label) }}</a>@endforeach</div>@endif
                @else
                    <dl><dt>Expéditeur</dt><dd>{{ $editItem->name }}</dd><dt>Téléphone</dt><dd>{{ $editItem->phone ? '' : '-' }}@if($editItem->phone)<a href="tel:{{ $editItem->phone }}">{{ $editItem->phone }}</a>@endif</dd><dt>Email</dt><dd><a href="mailto:{{ $editItem->email }}?subject=Re: {{ rawurlencode($editItem->subject) }}">{{ $editItem->email }}</a></dd><dt>Sujet</dt><dd>{{ $editItem->subject }}</dd><dt>Message</dt><dd class="message-body">{{ $editItem->message }}</dd></dl>
                @endif
            </div>
            <form method="post" action="{{ route('admin.resource.update.post', [$resource, $editItem->id]) }}" class="treatment-actions">
                @csrf
                <label class="field"><span>Nouveau statut</span><select name="status" class="form-select">@foreach($statusOptions[$resource] as $value => $label)<option value="{{ $value }}" @selected(($editItem->status ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
                <button class="btn btn-primary w-100">Mettre à jour le statut</button>
                @if($resource === 'inscriptions')<a class="btn btn-outline-primary w-100" href="mailto:{{ $editItem->email }}?subject=Votre dossier EPIM {{ $editItem->dossier_number }}">Répondre par email</a><a class="btn btn-outline-primary w-100" href="tel:{{ $editItem->phone }}">Appeler</a>@endif
                @if($resource === 'messages')<a class="btn btn-outline-primary w-100" href="mailto:{{ $editItem->email }}?subject=Re: {{ rawurlencode($editItem->subject) }}">Répondre par email</a>@if($editItem->phone)<a class="btn btn-outline-primary w-100" href="tel:{{ $editItem->phone }}">Appeler</a>@endif @endif
            </form>
        </div>
    </section>
@endif

<div class="admin-panel">
    <h2>Liste</h2>
    <div class="table-responsive"><table class="table align-middle admin-table"><thead><tr><th>ID</th><th>Élément</th><th>Statut / Type</th><th>Date</th><th>Actions</th></tr></thead><tbody>
    @foreach($items as $item)
        <tr class="{{ $editItem?->id === $item->id ? 'table-active' : '' }}"><td>{{ $item->id }}</td><td>
            @if($resource === 'inscriptions')<strong>{{ $item->dossier_number }}</strong><br><span>{{ $item->first_name }} {{ $item->last_name }} - {{ $item->email }} - {{ $item->phone }}</span><br><small>{{ $item->formation?->title ?: 'Formation non renseignée' }}</small>
            @elseif($resource === 'messages')<strong>{{ $item->subject }}</strong><br><span>{{ $item->name }} - {{ $item->email }}</span><br><small>{{ Str::limit($item->message, 90) }}</small>
            @elseif($resource === 'partenaires')<div class="admin-logo-cell"><img src="{{ $item->logo_url }}" alt="{{ $item->name }}"><span>{{ $item->name }}</span></div>
            @elseif($resource === 'parametres')<strong>{{ $item->key }}</strong><br><span>{{ is_array($item->value) ? ($item->value['text'] ?? json_encode($item->value)) : $item->value }}</span>
            @elseif($resource === 'rubriques')<strong>{{ $item->value['label'] ?? $item->key }}</strong><br><span>{{ $item->value['is_under_construction'] ?? false ? 'Page remplacée par “en cours de construction”' : 'Page publique active' }}</span>
            @else<strong>{{ $item->title ?? $item->name ?? $item->email ?? '-' }}</strong>@if($resource === 'actualites')<br><span>{{ $item->category }}</span>@endif @if($resource === 'formations')<br><span>{{ $formationCategories[$item->category] ?? $item->category }} - {{ $item->duration }}</span>@if($item->category === 'Formations diplomantes')<br><span class="badge text-bg-success">Formulaire rapide</span>@endif @endif @endif
        </td><td>@if($resource === 'rubriques')<span class="badge {{ ($item->value['is_under_construction'] ?? false) ? 'text-bg-warning' : 'text-bg-success' }}">{{ ($item->value['is_under_construction'] ?? false) ? 'En construction' : 'Active' }}</span>@elseif(in_array($resource, $readonlyResources, true))<span class="status-pill status-{{ $item->status }}">{{ $statusOptions[$resource][$item->status] ?? $item->status }}</span>@else{{ $item->role ?? $item->type ?? $item->category ?? '-' }}@endif</td><td>{{ $item->created_at?->format('d/m/Y H:i') }}</td><td>
            @if(in_array($resource, $readonlyResources, true))<a class="btn btn-sm btn-primary" href="{{ route('admin.resource', array_filter(['resource' => $resource, 'edit' => $item->id, 'status' => request('status'), 'q' => request('q')])) }}#editor">Traiter</a><form method="post" action="{{ route('admin.resource.update.post', [$resource, $item->id]) }}" class="d-inline">@csrf<input type="hidden" name="status" value="{{ $resource === 'messages' ? 'traite' : 'en_traitement' }}"><button class="btn btn-sm btn-outline-primary">{{ $resource === 'messages' ? 'Marquer traité' : 'Prendre en charge' }}</button></form>
            @else<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.resource', ['resource' => $resource, 'edit' => $item->id]) }}#editor">Modifier</a><form method="post" action="{{ route('admin.resource.destroy', [$resource, $item->id]) }}" class="mt-2">@csrf @method('delete')<button class="btn btn-sm btn-outline-danger">Supprimer</button></form>@endif
        </td></tr>
    @endforeach
    </tbody></table></div>{{ $items->links() }}
</div>
@endsection