@extends('layouts.admin')
@section('title', $title)
@section('content')
@if(($applicationModule ?? null) === 'index')
@php
    $query = request()->query();
    $statusLabel = fn ($value) => $statusOptions[$value] ?? ($value ?: '-');
    $priorityLabel = fn ($value) => $priorityOptions[$value ?: 'normale'] ?? ($value ?: '-');
    $sourceLabel = fn ($item) => $sourceOptions[$item->source ?: ($item->status === 'preinscription_rapide' ? 'preinscription_rapide' : 'formulaire_complet')] ?? ($item->source ?: '-');
@endphp
<section class="application-stats">
    <article><span>Total dossiers</span><strong>{{ $stats['total'] }}</strong></article>
    <article><span>Nouveaux</span><strong>{{ $stats['new'] }}</strong></article>
    <article><span>En suivi</span><strong>{{ $stats['processing'] }}</strong></article>
    <article><span>Validés / inscrits</span><strong>{{ $stats['validated'] }}</strong></article>
    <article><span>Relances dues</span><strong>{{ $stats['followups'] }}</strong></article>
</section>
<section class="admin-panel admin-filters mb-4">
    <div class="admin-panel-head">
        <div><span class="admin-kicker">Admissions</span><h2>Recherche et filtres</h2></div>
        <div class="application-export-actions">
            <a class="btn btn-outline-primary" href="{{ route('admin.applications.export.excel', $query) }}"><i class="bi bi-file-earmark-spreadsheet"></i> Excel</a>
            <a class="btn btn-outline-primary" href="{{ route('admin.applications.export.pdf', $query) }}" target="_blank"><i class="bi bi-file-earmark-pdf"></i> PDF / impression</a>
        </div>
    </div>
    <form method="get" class="application-filter-form">
        <label class="field wide"><span>Recherche</span><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Nom, téléphone, email, ville, numéro dossier"></label>
        <label class="field"><span>Statut</span><select class="form-select" name="status"><option value="">Tous</option>@foreach($statusOptions as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label class="field"><span>Priorité</span><select class="form-select" name="priority"><option value="">Toutes</option>@foreach($priorityOptions as $value => $label)<option value="{{ $value }}" @selected(request('priority') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label class="field"><span>Source</span><select class="form-select" name="source"><option value="">Toutes</option>@foreach($sourceOptions as $value => $label)<option value="{{ $value }}" @selected(request('source') === $value)>{{ $label }}</option>@endforeach</select></label>
        <label class="field"><span>Formation</span><select class="form-select" name="formation_id"><option value="">Toutes</option>@foreach($formations as $formation)<option value="{{ $formation->id }}" @selected((string) request('formation_id') === (string) $formation->id)>{{ $formation->title }}</option>@endforeach</select></label>
        <label class="field"><span>Responsable</span><select class="form-select" name="assigned_to"><option value="">Tous</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((string) request('assigned_to') === (string) $user->id)>{{ $user->name }}</option>@endforeach</select></label>
        <label class="field"><span>Relance</span><select class="form-select" name="follow_up"><option value="">Toutes</option><option value="today" @selected(request('follow_up') === 'today')>Aujourd'hui</option><option value="late" @selected(request('follow_up') === 'late')>En retard</option></select></label>
        <label class="field"><span>Du</span><input class="form-control" type="date" name="date_from" value="{{ request('date_from') }}"></label>
        <label class="field"><span>Au</span><input class="form-control" type="date" name="date_to" value="{{ request('date_to') }}"></label>
        <div class="admin-filter-actions"><button class="btn btn-primary"><i class="bi bi-funnel"></i> Filtrer</button><a class="btn btn-outline-secondary" href="{{ route('admin.applications.index') }}">Réinitialiser</a></div>
    </form>
</section>
<section class="admin-panel">
    <div class="admin-panel-head"><div><span class="admin-kicker">Liste</span><h2>Dossiers candidats</h2></div><span class="admin-muted">{{ $items->total() }} résultat(s)</span></div>
    <div class="table-responsive"><table class="table align-middle admin-table applications-table"><thead><tr><th>Dossier</th><th>Candidat</th><th>Formation</th><th>Suivi</th><th>Responsable</th><th>Relance</th><th>Actions</th></tr></thead><tbody>
    @forelse($items as $item)
        <tr>
            <td><strong>{{ $item->dossier_number }}</strong><span class="application-source">{{ $sourceLabel($item) }}</span></td>
            <td><strong>{{ $item->first_name }} {{ $item->last_name }}</strong><span><a href="tel:{{ $item->phone }}">{{ $item->phone }}</a> · <a href="mailto:{{ $item->email }}">{{ $item->email }}</a></span><small>{{ $item->city ?: 'Ville non renseignée' }}{{ $item->education_level ? ' · ' . $item->education_level : '' }}</small></td>
            <td>{{ $item->formation?->title ?: 'Formation non renseignée' }}</td>
            <td><span class="status-pill status-{{ $item->status }}">{{ $statusLabel($item->status) }}</span><span class="priority-pill priority-{{ $item->priority ?: 'normale' }}">{{ $priorityLabel($item->priority) }}</span><small>{{ $item->comments_count }} commentaire(s), {{ $item->activities_count }} action(s)</small></td>
            <td><strong>{{ $item->assignedUser?->name ?: 'Non assigné' }}</strong><small>{{ $item->processedBy ? 'Dernier traitement : ' . $item->processedBy->name : 'Aucun traitement' }}</small></td>
            <td>@if($item->next_follow_up_at)<strong class="{{ $item->next_follow_up_at->isPast() ? 'text-danger' : '' }}">{{ $item->next_follow_up_at->format('d/m/Y H:i') }}</strong>@else<span>-</span>@endif<small>Créé le {{ $item->created_at?->format('d/m/Y H:i') }}</small></td>
            <td><div class="application-row-actions"><a class="btn btn-sm btn-primary" href="{{ route('admin.applications.show', $item) }}">Voir / traiter</a><a class="btn btn-sm btn-outline-primary" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->phone) }}" target="_blank">WhatsApp</a></div></td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center text-muted py-4">Aucune inscription ne correspond aux filtres.</td></tr>
    @endforelse
    </tbody></table></div>{{ $items->links() }}
</section>
@elseif(($applicationModule ?? null) === 'show')
@php
    $statusLabel = $statusOptions[$application->status] ?? $application->status;
    $priority = $application->priority ?: 'normale';
    $priorityLabel = $priorityOptions[$priority] ?? $priority;
    $source = $application->source ?: ($application->status === 'preinscription_rapide' ? 'preinscription_rapide' : 'formulaire_complet');
    $sourceLabel = $sourceOptions[$source] ?? $source;
    $candidateEmail = str_contains($application->email, '@epim.local') ? null : $application->email;
@endphp
<section class="application-detail-head">
    <div>
        <a class="admin-back-link" href="{{ route('admin.applications.index') }}"><i class="bi bi-arrow-left"></i> Retour aux inscriptions</a>
        <span class="admin-kicker">Fiche candidat</span>
        <h2>{{ $application->first_name }} {{ $application->last_name }}</h2>
        <div class="application-head-badges"><span class="status-pill status-{{ $application->status }}">{{ $statusLabel }}</span><span class="priority-pill priority-{{ $priority }}">{{ $priorityLabel }}</span><span class="application-source">{{ $sourceLabel }}</span></div>
    </div>
    <div class="application-header-actions">
        <a class="btn btn-outline-primary" href="{{ route('admin.applications.print', $application) }}" target="_blank"><i class="bi bi-printer"></i> Fiche PDF</a>
        <a class="btn btn-outline-primary" href="tel:{{ $application->phone }}"><i class="bi bi-telephone"></i> Appeler</a>
        <a class="btn btn-outline-primary" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $application->phone) }}" target="_blank"><i class="bi bi-whatsapp"></i> WhatsApp</a>
        @if($candidateEmail)<a class="btn btn-outline-primary" href="mailto:{{ $candidateEmail }}?subject=Votre dossier EPIM {{ $application->dossier_number }}"><i class="bi bi-envelope"></i> Email</a>@endif
    </div>
</section>
<section class="application-detail-grid">
    <div class="application-main-column">
        <article class="admin-panel">
            <div class="admin-panel-head"><div><span class="admin-kicker">Informations</span><h2>Coordonnées et dossier</h2></div></div>
            <dl class="application-dl">
                <dt>Numéro dossier</dt><dd>{{ $application->dossier_number }}</dd><dt>Candidat</dt><dd>{{ $application->first_name }} {{ $application->last_name }}</dd><dt>Téléphone</dt><dd><a href="tel:{{ $application->phone }}">{{ $application->phone }}</a></dd><dt>Email</dt><dd>{{ $candidateEmail ? '' : 'Non renseigné' }}@if($candidateEmail)<a href="mailto:{{ $candidateEmail }}">{{ $candidateEmail }}</a>@endif</dd><dt>Ville</dt><dd>{{ $application->city ?: '-' }}</dd><dt>Niveau</dt><dd>{{ $application->education_level ?: '-' }}</dd><dt>Formation</dt><dd>{{ $application->formation?->title ?: '-' }}</dd><dt>Source</dt><dd>{{ $sourceLabel }}</dd><dt>Création</dt><dd>{{ $application->created_at?->format('d/m/Y H:i') }}</dd><dt>Dernier traitement</dt><dd>{{ $application->processed_at?->format('d/m/Y H:i') ?: '-' }}{{ $application->processedBy ? ' par ' . $application->processedBy->name : '' }}</dd><dt>Dernier contact</dt><dd>{{ $application->last_contacted_at?->format('d/m/Y H:i') ?: '-' }}</dd><dt>Prochaine relance</dt><dd>{{ $application->next_follow_up_at?->format('d/m/Y H:i') ?: '-' }}</dd><dt>Message candidat</dt><dd class="message-body">{{ $application->message ?: '-' }}</dd>
            </dl>
            @if(!empty($application->documents))<div class="application-documents"><h3>Documents joints</h3><div class="doc-links">@foreach($application->documents as $label => $path)<a class="btn btn-sm btn-outline-primary" href="{{ asset('storage/'.$path) }}" target="_blank">{{ strtoupper($label) }}</a>@endforeach</div></div>@endif
        </article>
        <article class="admin-panel">
            <div class="admin-panel-head"><div><span class="admin-kicker">Commentaires</span><h2>Suivi interne</h2></div></div>
            <form method="post" action="{{ route('admin.applications.comments.store', $application) }}" class="application-comment-form">@csrf<textarea class="form-control" name="body" rows="4" placeholder="Ajouter un commentaire interne : appel, relance, échange avec parent, pièce manquante..." required>{{ old('body') }}</textarea><label class="check-field"><input type="checkbox" name="is_important" value="1"> Commentaire important</label><button class="btn btn-primary"><i class="bi bi-chat-left-text"></i> Ajouter au suivi</button></form>
            <div class="application-comments">@forelse($application->comments->sortByDesc('created_at') as $comment)<div class="application-comment {{ $comment->is_important ? 'important' : '' }}"><div><strong>{{ $comment->user?->name ?: 'Utilisateur supprimé' }}</strong><span>{{ $comment->created_at?->format('d/m/Y H:i') }}</span></div><p>{{ $comment->body }}</p></div>@empty<p class="text-muted mb-0">Aucun commentaire interne pour ce dossier.</p>@endforelse</div>
        </article>
        <article class="admin-panel">
            <div class="admin-panel-head"><div><span class="admin-kicker">Historique</span><h2>Activité du dossier</h2></div></div>
            <div class="application-timeline">@forelse($application->activities->sortByDesc('created_at') as $activity)<div class="timeline-entry"><span></span><div><strong>{{ $activity->description ?: $activity->action }}</strong><small>{{ $activity->created_at?->format('d/m/Y H:i') }} · {{ $activity->user?->name ?: 'Système' }}</small></div></div>@empty<p class="text-muted mb-0">Aucune action enregistrée pour le moment.</p>@endforelse</div>
        </article>
    </div>
    <aside class="application-side-column">
        <article class="admin-panel"><span class="admin-kicker">Traitement</span><h2>Statut du dossier</h2><form method="post" action="{{ route('admin.applications.status', $application) }}" class="application-side-form">@csrf<label class="field"><span>Statut</span><select class="form-select" name="status">@foreach($statusOptions as $value => $label)<option value="{{ $value }}" @selected($application->status === $value)>{{ $label }}</option>@endforeach</select></label><label class="field"><span>Priorité</span><select class="form-select" name="priority">@foreach($priorityOptions as $value => $label)<option value="{{ $value }}" @selected($priority === $value)>{{ $label }}</option>@endforeach</select></label><label class="field"><span>Source</span><select class="form-select" name="source">@foreach($sourceOptions as $value => $label)<option value="{{ $value }}" @selected($source === $value)>{{ $label }}</option>@endforeach</select></label><button class="btn btn-primary w-100">Mettre à jour</button></form></article>
        <article class="admin-panel"><span class="admin-kicker">Responsable</span><h2>Assignation</h2><form method="post" action="{{ route('admin.applications.assign', $application) }}" class="application-side-form">@csrf<label class="field"><span>Pris en charge par</span><select class="form-select" name="assigned_to"><option value="">Non assigné</option>@foreach($users as $user)<option value="{{ $user->id }}" @selected((int) $application->assigned_to === (int) $user->id)>{{ $user->name }}</option>@endforeach</select></label><button class="btn btn-primary w-100">Assigner</button></form></article>
        <article class="admin-panel"><span class="admin-kicker">Relance</span><h2>Planification</h2><form method="post" action="{{ route('admin.applications.followup', $application) }}" class="application-side-form">@csrf<label class="field"><span>Dernier contact</span><input class="form-control" type="datetime-local" name="last_contacted_at" value="{{ old('last_contacted_at', $application->last_contacted_at?->format('Y-m-d\TH:i')) }}"></label><label class="field"><span>Prochaine relance</span><input class="form-control" type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', $application->next_follow_up_at?->format('Y-m-d\TH:i')) }}"></label><button class="btn btn-primary w-100">Enregistrer la relance</button></form><form method="post" action="{{ route('admin.applications.contacted', $application) }}" class="mt-2">@csrf<button class="btn btn-outline-primary w-100">Marquer contacté maintenant</button></form></article>
    </aside>
</section>
@else
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
@endif
@endsection
