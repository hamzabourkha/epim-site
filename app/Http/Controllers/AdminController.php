<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ContactMessage;
use App\Models\Formation;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'stats' => [
                'formations' => Formation::count(),
                'articles' => Post::count(),
                'inscriptions' => Application::count(),
                'messages' => ContactMessage::where('status', 'non_lu')->count(),
                'partenaires' => Partner::count(),
                'utilisateurs' => User::count(),
                'pages' => Page::count(),
            ],
            'formationCategories' => Formation::selectRaw('category, count(*) as total')->groupBy('category')->orderBy('category')->get(),
            'applicationStatuses' => Application::selectRaw('status, count(*) as total')->groupBy('status')->get(),
            'latestPosts' => Post::latest('published_at')->take(5)->get(),
            'applications' => Application::with('formation')->latest()->take(8)->get(),
            'messages' => ContactMessage::latest()->take(6)->get(),
        ]);
    }

    public function index(string $resource)
    {
        [$model, $title] = $this->resource($resource);
        $query = $resource === 'rubriques'
            ? $model::where('key', 'like', 'rubrique.%')->orderBy('key')
            : $model::query()->latest();

        if ($resource === 'inscriptions') {
            $query->with('formation');
        }

        if (in_array($resource, ['inscriptions', 'messages'], true) && request()->filled('status')) {
            $query->where('status', request('status'));
        }

        if ($resource === 'formations' && request()->filled('category')) {
            $query->where('category', request('category'));
        }

        if (request()->filled('q')) {
            $search = '%' . request('q') . '%';
            match ($resource) {
                'inscriptions' => $query->where(function ($q) use ($search) {
                    $q->where('dossier_number', 'like', $search)
                        ->orWhere('first_name', 'like', $search)
                        ->orWhere('last_name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('phone', 'like', $search);
                }),
                'messages' => $query->where(function ($q) use ($search) {
                    $q->where('subject', 'like', $search)
                        ->orWhere('name', 'like', $search)
                        ->orWhere('email', 'like', $search)
                        ->orWhere('message', 'like', $search);
                }),
                'formations' => $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', $search)->orWhere('description', 'like', $search);
                }),
                default => null,
            };
        }

        $items = $query->paginate(15)->withQueryString();
        $editItem = request()->filled('edit') ? $model::findOrFail((int) request('edit')) : null;
        if ($editItem && $resource === 'inscriptions') {
            $editItem->load('formation');
        }

        return view('admin.resource', [
            'resource' => $resource,
            'title' => $title,
            'items' => $items,
            'editItem' => $editItem,
        ]);
    }

    public function store(Request $request, string $resource)
    {
        if (in_array($resource, ['inscriptions', 'messages'], true)) {
            return back()->with('success', 'Les inscriptions et messages se creent depuis le site public. Vous pouvez modifier leur statut depuis la liste.');
        }

        [$model] = $this->resource($resource);
        $this->validatePayload($request, $resource);
        $data = $this->payload($request, $resource);
        $model::create($data);

        return back()->with('success', 'Element cree avec succes.');
    }

    public function update(Request $request, string $resource, int $id)
    {
        [$model] = $this->resource($resource);
        $item = $model::findOrFail($id);
        $this->validatePayload($request, $resource, false);
        $item->update($this->payload($request, $resource, $item));

        return back()->with('success', 'Élément mis à jour.');
    }

    public function destroy(string $resource, int $id)
    {
        [$model] = $this->resource($resource);
        $model::findOrFail($id)->delete();

        return back()->with('success', 'Element supprime.');
    }

    private function resource(string $resource): array
    {
        return match ($resource) {
            'formations' => [Formation::class, 'Gestion des formations'],
            'actualites' => [Post::class, 'Gestion des actualites'],
            'galerie' => [GalleryItem::class, 'Gestion galerie'],
            'inscriptions' => [Application::class, 'Gestion inscriptions'],
            'messages' => [ContactMessage::class, 'Messages contact'],
            'partenaires' => [Partner::class, 'Gestion partenaires'],
            'pages' => [Page::class, 'Gestion pages et SEO'],
            'rubriques' => [SiteSetting::class, 'Gestion des rubriques'],
            'parametres' => [SiteSetting::class, 'Paramètres site'],
            'utilisateurs' => [User::class, 'Gestion utilisateurs'],
            default => abort(404),
        };
    }

    private function validatePayload(Request $request, string $resource, bool $creating = true): void
    {
        match ($resource) {
            'formations' => $request->validate([
                'title' => ['required', 'string', 'max:180'],
                'category' => ['required', 'string', 'max:120'],
                'image' => ['nullable', 'string', 'max:500'],
                'description' => ['required', 'string'],
                'duration' => ['nullable', 'string', 'max:120'],
                'level_required' => ['nullable', 'string', 'max:180'],
                'insertion_rate' => ['nullable', 'integer', 'between:0,100'],
            ]),
            'actualites' => $request->validate([
                'title' => ['required', 'string', 'max:220'],
                'category' => ['required', 'string', 'max:120'],
                'image' => ['nullable', 'string', 'max:500'],
                'excerpt' => ['required', 'string', 'max:600'],
                'body' => ['required', 'string'],
            ]),
            'galerie' => $request->validate([
                'title' => ['required', 'string', 'max:180'],
                'category' => ['required', 'string', 'max:120'],
                'type' => ['required', 'string', 'max:40'],
                'path' => ['required', 'string', 'max:500'],
                'description' => ['nullable', 'string'],
            ]),
            'partenaires' => $request->validate([
                'name' => ['required', 'string', 'max:180'],
                'type' => ['nullable', 'string', 'max:120'],
                'logo' => ['nullable', 'string', 'max:500'],
                'logo_file' => ['nullable', 'image', 'max:4096'],
                'description' => ['nullable', 'string'],
                'website' => ['nullable', 'string', 'max:500'],
            ]),
            'pages' => $request->validate([
                'title' => ['required', 'string', 'max:180'],
                'slug' => ['nullable', 'string', 'max:180'],
                'content' => ['required', 'string'],
                'seo_title' => ['nullable', 'string', 'max:220'],
                'seo_description' => ['nullable', 'string', 'max:500'],
            ]),
            'parametres' => $request->validate([
                'key' => ['required', 'string', 'max:180'],
                'value_text' => ['nullable', 'string'],
            ]),
            'rubriques' => $request->validate([
                'key' => ['required', 'string', 'max:180'],
                'label' => ['required', 'string', 'max:120'],
                'route' => ['required', 'string', 'max:120'],
                'construction_title' => ['required', 'string', 'max:180'],
                'construction_message' => ['required', 'string', 'max:800'],
                'is_under_construction' => ['nullable', 'boolean'],
            ]),
            'utilisateurs' => $request->validate([
                'name' => ['required', 'string', 'max:180'],
                'email' => ['required', 'email', 'max:180'],
                'role' => ['required', 'in:admin,directeur,assistant,formateur'],
                'phone' => ['nullable', 'string', 'max:80'],
                'password' => [$creating ? 'required' : 'nullable', 'string', 'min:6'],
            ]),
            default => null,
        };
    }

    private function payload(Request $request, string $resource, mixed $item = null): array
    {
        return match ($resource) {
            'formations' => [
                'title' => $request->string('title'),
                'slug' => $item && $item->title === $request->input('title') ? $item->slug : Str::slug($request->string('title')) . '-' . Str::lower(Str::random(3)),
                'category' => $request->input('category', 'Formation qualifiante'),
                'image' => $request->input('image', 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80'),
                'description' => $request->input('description', 'Formation professionnelle orientee employabilite.'),
                'duration' => $request->input('duration', '12 mois'),
                'level_required' => $request->input('level_required', 'Niveau bac'),
                'insertion_rate' => (int) $request->input('insertion_rate', 86),
                'objectives' => ['Maitriser les fondamentaux', 'Utiliser les outils digitaux et IA de maniere responsable', 'Realiser des projets concrets'],
                'opportunities' => ['Stage', 'Emploi junior', 'Freelance'],
                'program' => ['Fondamentaux', 'Outils professionnels', 'Initiation IA appliquee au metier', 'Ateliers pratiques', 'Projet final'],
                'skills' => ['Autonomie', 'Communication', 'Outils metier', 'Culture IA et productivite numerique'],
                'is_featured' => $request->boolean('is_featured'),
            ],
            'actualites' => [
                'title' => $request->string('title'),
                'slug' => $item && $item->title === $request->input('title') ? $item->slug : Str::slug($request->string('title')) . '-' . Str::lower(Str::random(3)),
                'category' => $request->input('category', 'Vie EPIM'),
                'image' => $request->input('image', 'https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1200&q=80'),
                'excerpt' => $request->input('excerpt', 'Actualite EPIM.'),
                'body' => $request->input('body', 'Contenu detaille de l actualite.'),
                'published_at' => $item?->published_at ?: now(),
                'seo_title' => $request->input('seo_title') ?: $request->input('title') . ' - EPIM Meknès',
                'seo_description' => $request->input('seo_description') ?: $request->input('excerpt'),
                'is_published' => $request->boolean('is_published', true),
            ],
            'galerie' => $request->only(['title', 'category', 'type', 'path', 'description']),
            'partenaires' => $this->partnerPayload($request, $item),
            'pages' => [
                'title' => $request->input('title'),
                'slug' => $request->filled('slug') ? Str::slug($request->input('slug')) : ($item?->slug ?: Str::slug($request->input('title'))),
                'content' => $request->input('content'),
                'seo_title' => $request->input('seo_title'),
                'seo_description' => $request->input('seo_description'),
            ],
            'parametres' => [
                'key' => $request->input('key'),
                'value' => ['text' => $request->input('value_text')],
            ],
            'rubriques' => [
                'key' => Str::startsWith($request->input('key'), 'rubrique.') ? $request->input('key') : 'rubrique.' . Str::slug($request->input('key')),
                'value' => [
                    'label' => $request->input('label'),
                    'route' => $request->input('route'),
                    'is_under_construction' => $request->boolean('is_under_construction'),
                    'title' => $request->input('construction_title'),
                    'message' => $request->input('construction_message'),
                ],
            ],
            'messages' => ['status' => $request->input('status', 'lu')],
            'inscriptions' => ['status' => $request->input('status', 'en_traitement')],
            'utilisateurs' => array_filter([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'role' => $request->input('role', 'assistant'),
                'phone' => $request->input('phone'),
                'password' => $request->filled('password') ? Hash::make($request->input('password')) : null,
                'is_active' => true,
            ], fn ($value) => ! is_null($value)),
            default => [],
        };
    }

    private function partnerPayload(Request $request, mixed $item = null): array
    {
        $payload = $request->only(['name', 'type', 'description', 'website']);

        if ($request->hasFile('logo_file')) {
            $payload['logo'] = 'storage/' . $request->file('logo_file')->store('partners', 'public');
        } elseif ($request->filled('logo')) {
            $payload['logo'] = $request->input('logo');
        } elseif ($item) {
            $payload['logo'] = $item->logo;
        }

        return $payload;
    }
}
