<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ContactMessage;
use App\Models\Formation;
use App\Models\GalleryItem;
use App\Models\Partner;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PublicController extends Controller
{
    public function setLocale(Request $request, string $locale)
    {
        abort_unless(in_array($locale, ['fr', 'ar', 'en'], true), 404);

        session(['locale' => $locale]);
        App::setLocale($locale);
        Cookie::queue('locale', $locale, 60 * 24 * 365, null, null, false, false, false, 'Lax');

        $target = url()->previous() ?: route('home');

        if (str_contains($target, '/changer-langue/')) {
            $target = route('home');
        }

        return redirect()->to($target);
    }

    public function home()
    {
        return view('public.home', [
            'formations' => Formation::where('is_featured', true)->take(6)->get(),
            'posts' => Post::where('is_published', true)->latest('published_at')->take(3)->get(),
            'partners' => Partner::take(8)->get(),
            'testimonials' => Testimonial::take(4)->get(),
            'seo' => ['title' => 'EPIM Meknès - École Professionnelle d’Informatique et de Management', 'description' => 'Formations professionnelles en informatique, management, langues, design, IA appliquée et digital à Meknès.'],
        ]);
    }

    public function about()
    {
        return view('public.about', [
            'partners' => Partner::all(),
            'gallery' => GalleryItem::take(8)->get(),
            'seo' => ['title' => 'À propos de EPIM Meknès', 'description' => 'Histoire, vision, mission, valeurs, accréditations et équipe pédagogique EPIM.'],
        ]);
    }

    public function formations()
    {
        $categories = [
            'Formations diplômantes' => [
                'db_key' => 'Formations diplomantes',
                'title' => __('site.formations.families.Formations diplômantes.title'),
                'subtitle' => __('site.formations.families.Formations diplômantes.subtitle'),
                'description' => __('site.formations.families.Formations diplômantes.description'),
                'badge' => __('site.formations.families.Formations diplômantes.badge'),
                'icon' => 'bi-award',
            ],
            'Formation continue' => [
                'db_key' => 'Formation continue',
                'title' => __('site.formations.families.Formation continue.title'),
                'subtitle' => __('site.formations.families.Formation continue.subtitle'),
                'description' => __('site.formations.families.Formation continue.description'),
                'badge' => __('site.formations.families.Formation continue.badge'),
                'icon' => 'bi-arrow-repeat',
            ],
            'Formation à la carte certifiante' => [
                'db_key' => 'Formation a la carte certifiante',
                'title' => __('site.formations.families.Formation à la carte certifiante.title'),
                'subtitle' => __('site.formations.families.Formation à la carte certifiante.subtitle'),
                'description' => __('site.formations.families.Formation à la carte certifiante.description'),
                'badge' => __('site.formations.families.Formation à la carte certifiante.badge'),
                'icon' => 'bi-patch-check',
            ],
            'Formation pour les entreprises' => [
                'db_key' => 'Formation pour les entreprises',
                'title' => __('site.formations.families.Formation pour les entreprises.title'),
                'subtitle' => __('site.formations.families.Formation pour les entreprises.subtitle'),
                'description' => __('site.formations.families.Formation pour les entreprises.description'),
                'badge' => __('site.formations.families.Formation pour les entreprises.badge'),
                'icon' => 'bi-buildings',
            ],
        ];

        $formations = Formation::orderBy('category')->orderBy('title')->get()->groupBy('category');

        return view('public.formations', [
            'formations' => $formations,
            'categories' => $categories,
            'seo' => ['title' => 'Filières et formations EPIM', 'description' => 'Développement digital, informatique, infographie, gestion, comptabilité, langues, IA appliquée et soft skills.'],
        ]);
    }

    public function formation(Formation $formation)
    {
        return view('public.formation-show', [
            'formation' => $formation,
            'related' => Formation::where('id', '!=', $formation->id)->take(3)->get(),
            'seo' => ['title' => $formation->title . ' - EPIM', 'description' => Str::limit($formation->description, 155)],
        ]);
    }

    public function admission()
    {
        return view('public.admission', [
            'formations' => Formation::all(),
            'seo' => ['title' => 'Admission et inscription EPIM', 'description' => 'Procédure d’inscription, conditions, documents, tarifs et FAQ.'],
        ]);
    }

    public function apply(Request $request)
    {
        $validated = $request->validate([
            'formation_id' => ['nullable', 'exists:formations,id'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['required', 'string', 'max:40'],
            'city' => ['nullable', 'string', 'max:120'],
            'education_level' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:2000'],
            'documents.cin' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'documents.diplome' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'documents.releves' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:4096'],
            'documents.photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:4096'],
        ]);

        $files = [];
        foreach ($request->file('documents', []) as $type => $file) {
            $files[$type] = $file->store('applications/' . $type, 'public');
        }

        $validated['documents'] = $files;
        $validated['dossier_number'] = 'EPIM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        Application::create($validated);

        return back()->with('success', 'Votre dossier a été enregistré sous le numéro ' . $validated['dossier_number']);
    }

    public function quickPreapply()
    {
        return view('public.preapply', [
            'formations' => Formation::where('category', 'Formations diplomantes')->orderBy('title')->get(),
            'seo' => ['title' => 'Préinscription rapide EPIM', 'description' => 'Formulaire court de préinscription EPIM pour être rappelé rapidement par l’administration.'],
        ]);
    }

    public function storeQuickPreapply(Request $request)
    {
        $validated = $request->validate([
            'formation_id' => ['nullable', 'exists:formations,id'],
            'first_name' => ['required', 'string', 'max:120'],
            'last_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:180'],
            'city' => ['nullable', 'string', 'max:120'],
            'education_level' => ['nullable', 'string', 'max:120'],
        ]);

        $contactEmail = $validated['email'] ?? null;
        $formationTitle = Formation::whereKey($validated['formation_id'] ?? null)->value('title');

        $validated['email'] = $validated['email'] ?: 'preinscription-' . Str::lower(Str::random(8)) . '@epim.local';
        $validated['status'] = 'preinscription_rapide';
        $validated['documents'] = [];
        $validated['dossier_number'] = 'PR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));

        $application = Application::create($validated);
        $this->sendQuickPreapplyEmail($application, $formationTitle, $contactEmail);

        return back()->with('success', 'Votre préinscription rapide a été envoyée. EPIM vous contactera prochainement. Numéro : ' . $validated['dossier_number']);
    }

    private function sendQuickPreapplyEmail(Application $application, ?string $formationTitle, ?string $contactEmail): void
    {
        $body = implode("\n", [
            'Nouvelle préinscription rapide EPIM',
            '',
            'Numéro : ' . $application->dossier_number,
            'Nom : ' . $application->last_name,
            'Prénom : ' . $application->first_name,
            'Téléphone : ' . $application->phone,
            'Email : ' . ($contactEmail ?: 'Non renseigné'),
            'Ville : ' . ($application->city ?: 'Non renseignée'),
            'Niveau scolaire : ' . ($application->education_level ?: 'Non renseigné'),
            'Formation souhaitée : ' . ($formationTitle ?: 'Non renseignée'),
            'Date : ' . $application->created_at?->format('d/m/Y H:i'),
            '',
            'Cette demande est également disponible dans le back-office EPIM.',
        ]);

        try {
            Mail::raw($body, function ($message) use ($application, $contactEmail) {
                $message->to('contact@epim.ma')
                    ->subject('Nouvelle préinscription rapide - ' . $application->dossier_number);

                if ($contactEmail) {
                    $message->replyTo($contactEmail, trim($application->first_name . ' ' . $application->last_name));
                }
            });
        } catch (\Throwable $exception) {
            Log::warning('Email préinscription rapide non envoyé', [
                'application_id' => $application->id,
                'dossier_number' => $application->dossier_number,
                'error' => $exception->getMessage(),
            ]);
        }
    }
    public function blog(Request $request)
    {
        $query = Post::where('is_published', true);
        if ($request->filled('q')) {
            $query->where(fn ($q) => $q->where('title', 'like', '%' . $request->q . '%')->orWhere('excerpt', 'like', '%' . $request->q . '%'));
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return view('public.blog', [
            'posts' => $query->latest('published_at')->paginate(6)->withQueryString(),
            'categories' => Post::select('category')->distinct()->pluck('category'),
            'seo' => ['title' => 'Actualités EPIM', 'description' => 'Actualités, événements, conseils carrières et vie pédagogique EPIM.'],
        ]);
    }

    public function post(Post $post)
    {
        abort_unless($post->is_published, 404);

        return view('public.post-show', [
            'post' => $post,
            'related' => Post::where('id', '!=', $post->id)->where('category', $post->category)->take(3)->get(),
            'seo' => ['title' => $post->seo_title ?: $post->title, 'description' => $post->seo_description ?: $post->excerpt],
        ]);
    }

    public function gallery(Request $request)
    {
        if ($construction = $this->constructionPage('galerie')) {
            return $construction;
        }

        $query = GalleryItem::query();
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return view('public.gallery', [
            'items' => $query->latest()->paginate(12)->withQueryString(),
            'categories' => GalleryItem::select('category')->distinct()->pluck('category'),
            'seo' => ['title' => 'Galerie EPIM', 'description' => 'Campus, salles, workshops, événements et remises de diplômes EPIM.'],
        ]);
    }

    public function partnerships()
    {
        return view('public.partnerships', [
            'partners' => Partner::all(),
            'seo' => ['title' => 'Entreprises et partenariats EPIM', 'description' => 'Stages, recrutement, insertion professionnelle et partenariats institutionnels EPIM.'],
        ]);
    }

    public function contact()
    {
        return view('public.contact', [
            'seo' => ['title' => 'Contact EPIM Meknès', 'description' => 'Contact, horaires, téléphone, WhatsApp, email et localisation EPIM Meknès.'],
        ]);
    }

    public function sendContact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180'],
            'phone' => ['nullable', 'string', 'max:40'],
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:3000'],
        ]);
        ContactMessage::create($validated);

        return $request->expectsJson()
            ? response()->json(['message' => 'Message envoyé avec succès.'])
            : back()->with('success', 'Votre message a été envoyé. Notre équipe vous répondra rapidement.');
    }

    private function constructionPage(string $section)
    {
        $setting = SiteSetting::where('key', 'rubrique.' . $section)->first();
        $value = $setting?->value ?? [];

        if (! ($value['is_under_construction'] ?? false)) {
            return null;
        }

        return response()->view('public.construction', [
            'section' => $value['label'] ?? Str::headline($section),
            'title' => $value['title'] ?? 'Page en cours de construction',
            'message' => $value['message'] ?? 'Cette rubrique sera disponible prochainement.',
            'seo' => ['title' => ($value['label'] ?? Str::headline($section)) . ' - En cours de construction', 'description' => 'Cette rubrique EPIM est en cours de construction.'],
        ]);
    }
}
