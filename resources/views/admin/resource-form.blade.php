@if($resource === 'formations')
    <label class="field"><span>Titre</span><input name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}" required></label>
    <label class="field"><span>Famille de formation</span><select name="category" class="form-select" required>@foreach($formationCategories as $value => $label)<option value="{{ $value }}" @selected(old('category', $item->category ?? '') === $value)>{{ $label }}</option>@endforeach</select></label>
    <label class="field check-field"><input type="checkbox" name="is_diploma" value="1" @checked(old('is_diploma', ($item->category ?? '') === 'Formations diplomantes'))><span>Formation diplômante à afficher dans le formulaire rapide</span></label>
    <label class="field wide"><span>URL de l’image</span><input name="image" class="form-control" value="{{ old('image', $item->image ?? '') }}"></label>
    <label class="field"><span>Durée</span><input name="duration" class="form-control" value="{{ old('duration', $item->duration ?? '') }}"></label>
    <label class="field"><span>Niveau requis</span><input name="level_required" class="form-control" value="{{ old('level_required', $item->level_required ?? '') }}"></label>
    <label class="field"><span>Taux d’insertion (%)</span><input name="insertion_rate" class="form-control" value="{{ old('insertion_rate', $item->insertion_rate ?? '') }}"></label>
    <label class="field wide"><span>Description</span><textarea name="description" class="form-control" required>{{ old('description', $item->description ?? '') }}</textarea></label>
    <label class="field check-field"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $item->is_featured ?? false))><span>Mettre en avant sur l’accueil</span></label>
@elseif($resource === 'actualites')
    <label class="field wide"><span>Titre de l’article</span><input name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}" required></label>
    <label class="field"><span>Catégorie</span><input name="category" class="form-control" value="{{ old('category', $item->category ?? '') }}" required></label>
    <label class="field"><span>URL de l’image</span><input name="image" class="form-control" value="{{ old('image', $item->image ?? '') }}"></label>
    <label class="field wide"><span>Extrait</span><textarea name="excerpt" class="form-control" required>{{ old('excerpt', $item->excerpt ?? '') }}</textarea></label>
    <label class="field wide"><span>Contenu</span><textarea name="body" class="form-control tall" required>{{ old('body', $item->body ?? '') }}</textarea></label>
    <label class="field"><span>Titre SEO</span><input name="seo_title" class="form-control" value="{{ old('seo_title', $item->seo_title ?? '') }}"></label>
    <label class="field"><span>Description SEO</span><input name="seo_description" class="form-control" value="{{ old('seo_description', $item->seo_description ?? '') }}"></label>
    <label class="field check-field"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $item->is_published ?? true))><span>Article publié</span></label>
@elseif($resource === 'galerie')
    <label class="field"><span>Titre</span><input name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}" required></label>
    <label class="field"><span>Catégorie</span><input name="category" class="form-control" value="{{ old('category', $item->category ?? '') }}" required></label>
    <label class="field"><span>Type</span><select name="type" class="form-select"><option value="image" @selected(old('type', $item->type ?? '') === 'image')>image</option><option value="video" @selected(old('type', $item->type ?? '') === 'video')>video</option></select></label>
    <label class="field wide"><span>URL du média</span><input name="path" class="form-control" value="{{ old('path', $item->path ?? '') }}" required></label>
    <label class="field wide"><span>Description</span><textarea name="description" class="form-control">{{ old('description', $item->description ?? '') }}</textarea></label>
@elseif($resource === 'partenaires')
    <div class="partner-form wide">
        <div class="partner-logo-preview">
            <img src="{{ $item?->logo_url ?? asset('images/epim-accreditation.webp') }}" alt="Logo partenaire">
            <span>Aperçu du logo actuel</span>
        </div>
        <div class="partner-fields">
            <label class="field"><span>Nom du partenaire</span><input name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required></label>
            <label class="field"><span>Type</span><input name="type" class="form-control" value="{{ old('type', $item->type ?? '') }}"></label>
            <label class="field"><span>Site web</span><input name="website" class="form-control" value="{{ old('website', $item->website ?? '') }}"></label>
            <label class="field"><span>URL du logo externe</span><input name="logo" class="form-control" value="{{ old('logo', $item->logo ?? '') }}"></label>
            <label class="file-field partner-upload">
                <span>Uploader un nouveau logo</span>
                <small>PNG, JPG, WEBP ou SVG conseillé. Taille max : 4 Mo. Si vous laissez ce champ vide, le logo actuel reste conservé.</small>
                <input class="form-control" name="logo_file" type="file" accept="image/*">
            </label>
            <label class="field wide"><span>Description</span><textarea name="description" class="form-control">{{ old('description', $item->description ?? '') }}</textarea></label>
        </div>
    </div>
@elseif($resource === 'utilisateurs')
    <label class="field"><span>Nom</span><input name="name" class="form-control" value="{{ old('name', $item->name ?? '') }}" required></label>
    <label class="field"><span>Email</span><input name="email" type="email" class="form-control" value="{{ old('email', $item->email ?? '') }}" required></label>
    <label class="field"><span>Rôle</span><select name="role" class="form-select">@foreach(['admin','directeur','assistant','formateur'] as $role)<option value="{{ $role }}" @selected(old('role', $item->role ?? '') === $role)>{{ $role }}</option>@endforeach</select></label>
    <label class="field"><span>Téléphone</span><input name="phone" class="form-control" value="{{ old('phone', $item->phone ?? '') }}"></label>
    <label class="field"><span>Mot de passe</span><input name="password" class="form-control" placeholder="{{ $item ? 'Laisser vide pour conserver le mot de passe actuel' : '' }}"></label>
@elseif($resource === 'pages')
    <label class="field"><span>Titre de la page</span><input name="title" class="form-control" value="{{ old('title', $item->title ?? '') }}" required></label>
    <label class="field"><span>Slug URL</span><input name="slug" class="form-control" value="{{ old('slug', $item->slug ?? '') }}"></label>
    <label class="field wide"><span>Contenu</span><textarea name="content" class="form-control tall" required>{{ old('content', $item->content ?? '') }}</textarea></label>
    <label class="field"><span>Titre SEO</span><input name="seo_title" class="form-control" value="{{ old('seo_title', $item->seo_title ?? '') }}"></label>
    <label class="field"><span>Description SEO</span><input name="seo_description" class="form-control" value="{{ old('seo_description', $item->seo_description ?? '') }}"></label>
@elseif($resource === 'parametres')
    <label class="field"><span>Clé</span><input name="key" class="form-control" placeholder="ex: contact.phone" value="{{ old('key', $item->key ?? '') }}" required></label>
    <label class="field wide"><span>Valeur</span><textarea name="value_text" class="form-control">{{ old('value_text', is_array($item->value ?? null) ? ($item->value['text'] ?? json_encode($item->value)) : '') }}</textarea></label>
@elseif($resource === 'rubriques')
    @php($value = $item->value ?? [])
    <label class="field"><span>Clé technique</span><input name="key" class="form-control" placeholder="rubrique.galerie" value="{{ old('key', $item->key ?? 'rubrique.') }}" required></label>
    <label class="field"><span>Nom de la rubrique</span><input name="label" class="form-control" value="{{ old('label', $value['label'] ?? '') }}" required></label>
    <label class="field"><span>Route Laravel</span><input name="route" class="form-control" placeholder="gallery" value="{{ old('route', $value['route'] ?? '') }}" required></label>
    <label class="field check-field"><input type="checkbox" name="is_under_construction" value="1" @checked(old('is_under_construction', $value['is_under_construction'] ?? false))><span>Remplacer cette rubrique par une page “en cours de construction”</span></label>
    <label class="field wide"><span>Titre de la page temporaire</span><input name="construction_title" class="form-control" value="{{ old('construction_title', $value['title'] ?? 'Page en cours de construction') }}" required></label>
    <label class="field wide"><span>Message affiché aux visiteurs</span><textarea name="construction_message" class="form-control" required>{{ old('construction_message', $value['message'] ?? 'Cette rubrique est en cours de préparation. Elle sera disponible prochainement.') }}</textarea></label>
@endif
