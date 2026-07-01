<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AdminApplicationController extends Controller
{
    private array $statusOptions = [
        'nouveau' => 'Nouveau',
        'preinscription_rapide' => 'Préinscription rapide',
        'a_rappeler' => 'À rappeler',
        'contacte' => 'Contacté',
        'rendez_vous' => 'Rendez-vous planifié',
        'dossier_incomplet' => 'Dossier incomplet',
        'dossier_complet' => 'Dossier complet',
        'en_traitement' => 'En traitement',
        'valide' => 'Validé',
        'inscrit' => 'Inscrit',
        'refuse' => 'Refusé',
        'archive' => 'Archivé',
    ];

    private array $priorityOptions = [
        'normale' => 'Normale',
        'haute' => 'Haute',
        'urgente' => 'Urgente',
        'relance' => 'À relancer',
    ];

    private array $sourceOptions = [
        'preinscription_rapide' => 'Préinscription rapide',
        'formulaire_complet' => 'Formulaire complet',
        'backoffice' => 'Back-office',
        'telephone' => 'Téléphone',
        'whatsapp' => 'WhatsApp',
        'salon' => 'Salon / événement',
    ];

    public function index(Request $request)
    {
        $query = $this->filteredQuery($request)
            ->with(['formation', 'assignedUser', 'processedBy'])
            ->withCount(['comments', 'activities']);

        $items = $query->paginate(15)->withQueryString();

        return view('admin.resource', [
            'applicationModule' => 'index',
            'resource' => 'inscriptions',
            'title' => 'Gestion des inscriptions',
            'items' => $items,
            'stats' => $this->stats(),
            'formations' => Formation::orderBy('title')->get(['id', 'title']),
            'users' => User::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'statusOptions' => $this->statusOptions,
            'priorityOptions' => $this->priorityOptions,
            'sourceOptions' => $this->sourceOptions,
        ]);
    }

    public function show(Application $application)
    {
        $application->load([
            'formation',
            'assignedUser',
            'processedBy',
            'comments.user',
            'activities.user',
        ]);

        return view('admin.resource', [
            'applicationModule' => 'show',
            'resource' => 'inscriptions',
            'title' => 'Dossier ' . $application->dossier_number,
            'application' => $application,
            'users' => User::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'statusOptions' => $this->statusOptions,
            'priorityOptions' => $this->priorityOptions,
            'sourceOptions' => $this->sourceOptions,
        ]);
    }

    public function updateStatus(Request $request, Application $application)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys($this->statusOptions))],
            'priority' => ['required', 'string', 'in:' . implode(',', array_keys($this->priorityOptions))],
            'source' => ['nullable', 'string', 'in:' . implode(',', array_keys($this->sourceOptions))],
        ]);

        $oldStatus = $application->status;
        $oldPriority = $application->priority ?: 'normale';

        $application->update([
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'source' => $validated['source'] ?: $application->source,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        $parts = [];
        if ($oldStatus !== $application->status) {
            $parts[] = 'statut : ' . $this->label($this->statusOptions, $oldStatus) . ' → ' . $this->label($this->statusOptions, $application->status);
        }
        if ($oldPriority !== $application->priority) {
            $parts[] = 'priorité : ' . $this->label($this->priorityOptions, $oldPriority) . ' → ' . $this->label($this->priorityOptions, $application->priority);
        }

        $this->activity($application, 'status_updated', $parts ? implode(', ', $parts) : 'Fiche mise à jour.');

        return back()->with('success', 'Statut du dossier mis à jour.');
    }

    public function assign(Request $request, Application $application)
    {
        $validated = $request->validate([
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $application->update([
            'assigned_to' => $validated['assigned_to'] ?: null,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        $assigned = $application->assignedUser()->first();
        $this->activity($application, 'assigned', $assigned ? 'Dossier assigné à ' . $assigned->name . '.' : 'Assignation retirée.');

        return back()->with('success', 'Responsable du dossier mis à jour.');
    }

    public function storeComment(Request $request, Application $application)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
            'is_important' => ['nullable', 'boolean'],
        ]);

        $comment = $application->comments()->create([
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'is_important' => $request->boolean('is_important'),
        ]);

        $application->update([
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        $this->activity($application, 'comment_added', Str::limit($comment->body, 180));

        return back()->with('success', 'Commentaire ajouté au suivi.');
    }

    public function updateFollowUp(Request $request, Application $application)
    {
        $validated = $request->validate([
            'next_follow_up_at' => ['nullable', 'date'],
            'last_contacted_at' => ['nullable', 'date'],
        ]);

        $application->update([
            'next_follow_up_at' => $validated['next_follow_up_at'] ?? null,
            'last_contacted_at' => $validated['last_contacted_at'] ?? $application->last_contacted_at,
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        $this->activity($application, 'follow_up_updated', 'Suivi et relance mis à jour.');

        return back()->with('success', 'Suivi du candidat mis à jour.');
    }

    public function markContacted(Application $application)
    {
        $application->update([
            'last_contacted_at' => now(),
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        $this->activity($application, 'contacted', 'Candidat marqué comme contacté.');

        return back()->with('success', 'Contact candidat enregistré.');
    }

    public function exportExcel(Request $request)
    {
        $fileName = 'inscriptions-epim-' . now()->format('Ymd-His') . '.csv';
        $applications = $this->filteredQuery($request)
            ->with(['formation', 'assignedUser', 'processedBy'])
            ->get();

        return response()->streamDownload(function () use ($applications) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, [
                'Numéro dossier',
                'Candidat',
                'Téléphone',
                'Email',
                'Ville',
                'Niveau',
                'Formation',
                'Source',
                'Statut',
                'Priorité',
                'Responsable',
                'Traité par',
                'Dernier contact',
                'Prochaine relance',
                'Date demande',
            ], ';');

            foreach ($applications as $application) {
                fputcsv($handle, [
                    $application->dossier_number,
                    trim($application->first_name . ' ' . $application->last_name),
                    $application->phone,
                    $application->email,
                    $application->city,
                    $application->education_level,
                    $application->formation?->title,
                    $this->sourceLabel($application),
                    $this->label($this->statusOptions, $application->status),
                    $this->label($this->priorityOptions, $application->priority ?: 'normale'),
                    $application->assignedUser?->name,
                    $application->processedBy?->name,
                    $application->last_contacted_at?->format('d/m/Y H:i'),
                    $application->next_follow_up_at?->format('d/m/Y H:i'),
                    $application->created_at?->format('d/m/Y H:i'),
                ], ';');
            }

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $applications = $this->filteredQuery($request)
            ->with(['formation', 'assignedUser', 'processedBy'])
            ->get();

        return response($this->renderPrintHtml('Export inscriptions EPIM', $applications));
    }

    public function print(Application $application)
    {
        $application->load(['formation', 'assignedUser', 'processedBy', 'comments.user', 'activities.user']);

        return response($this->renderPrintHtml('Fiche candidat ' . $application->dossier_number, collect([$application]), $application));
    }

    private function filteredQuery(Request $request): Builder
    {
        $query = Application::query()->latest();

        if ($request->filled('q')) {
            $search = '%' . $request->input('q') . '%';
            $query->where(function (Builder $q) use ($search) {
                $q->where('dossier_number', 'like', $search)
                    ->orWhere('first_name', 'like', $search)
                    ->orWhere('last_name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone', 'like', $search)
                    ->orWhere('city', 'like', $search);
            });
        }

        foreach (['status', 'priority', 'source', 'formation_id', 'assigned_to'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $request->input($field));
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        if ($request->input('follow_up') === 'today') {
            $query->whereDate('next_follow_up_at', now()->toDateString());
        }

        if ($request->input('follow_up') === 'late') {
            $query->whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<', now());
        }

        return $query;
    }

    private function stats(): array
    {
        return [
            'total' => Application::count(),
            'new' => Application::whereIn('status', ['nouveau', 'preinscription_rapide'])->count(),
            'processing' => Application::whereIn('status', ['a_rappeler', 'contacte', 'rendez_vous', 'dossier_incomplet', 'en_traitement'])->count(),
            'validated' => Application::whereIn('status', ['valide', 'inscrit'])->count(),
            'followups' => Application::whereNotNull('next_follow_up_at')->where('next_follow_up_at', '<=', now()->endOfDay())->count(),
        ];
    }

    private function activity(Application $application, string $action, ?string $description = null, array $meta = []): void
    {
        $application->activities()->create([
            'user_id' => Auth::id(),
            'action' => $action,
            'description' => $description,
            'meta' => $meta ?: null,
        ]);
    }

    private function label(array $options, ?string $value): string
    {
        return $options[$value] ?? ($value ?: '-');
    }

    private function sourceLabel(Application $application): string
    {
        $source = $application->source ?: ($application->status === 'preinscription_rapide' ? 'preinscription_rapide' : 'formulaire_complet');

        return $this->label($this->sourceOptions, $source);
    }

    private function renderPrintHtml(string $title, $applications, ?Application $application = null): string
    {
        $generatedAt = now()->format('d/m/Y H:i');
        $rows = '';

        foreach ($applications as $item) {
            $priority = $item->priority ?: 'normale';
            $email = str_contains($item->email, '@epim.local') ? '-' : $item->email;
            $rows .= '<tr>'
                . '<td>' . e($item->dossier_number) . '</td>'
                . '<td>' . e(trim($item->first_name . ' ' . $item->last_name)) . '</td>'
                . '<td>' . e($item->phone) . '</td>'
                . '<td>' . e($email) . '</td>'
                . '<td>' . e($item->formation?->title ?: '-') . '</td>'
                . '<td>' . e($this->label($this->statusOptions, $item->status)) . '</td>'
                . '<td>' . e($this->label($this->priorityOptions, $priority)) . '</td>'
                . '<td>' . e($item->assignedUser?->name ?: '-') . '</td>'
                . '<td>' . e($item->next_follow_up_at?->format('d/m/Y H:i') ?: '-') . '</td>'
                . '</tr>';
        }

        $details = '';
        if ($application) {
            $comments = $application->comments->sortByDesc('created_at')->map(function ($comment) {
                return '<div class="comment"><strong>' . e($comment->user?->name ?: 'Utilisateur') . '</strong> - '
                    . e($comment->created_at?->format('d/m/Y H:i') ?: '')
                    . '<br>' . e($comment->body) . '</div>';
            })->implode('');

            $details = '<h2>Commentaires internes</h2>' . ($comments ?: '<p>Aucun commentaire.</p>');
        }

        return <<<HTML
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<title>{$title}</title>
<style>
body{margin:0;padding:28px;color:#1b2f49;font-family:Arial,sans-serif}header{display:flex;justify-content:space-between;gap:20px;border-bottom:3px solid #004B9C;padding-bottom:14px;margin-bottom:24px}h1{margin:0;color:#004B9C;font-size:26px}h2{color:#004B9C;font-size:18px;margin:24px 0 10px}.meta{color:#607089;font-size:12px}table{width:100%;border-collapse:collapse;margin-top:12px}th,td{border:1px solid #d9e2ef;padding:8px;text-align:left;vertical-align:top;font-size:12px}th{background:#f1f6fd;color:#004B9C}.comment{border-left:3px solid #F39C12;padding:8px 12px;background:#fffaf0;margin-bottom:8px}.actions{margin-bottom:18px}.actions button{border:1px solid #004B9C;background:#004B9C;color:white;border-radius:6px;padding:8px 14px;cursor:pointer}@media print{body{padding:0}.actions{display:none}a{color:inherit;text-decoration:none}}
</style>
</head>
<body>
<div class="actions"><button onclick="window.print()">Imprimer / enregistrer en PDF</button></div>
<header><div><h1>{$title}</h1><div class="meta">EPIM - École Professionnelle d'Informatique et de Management</div></div><div class="meta">Généré le {$generatedAt}</div></header>
<table><thead><tr><th>Dossier</th><th>Candidat</th><th>Téléphone</th><th>Email</th><th>Formation</th><th>Statut</th><th>Priorité</th><th>Responsable</th><th>Relance</th></tr></thead><tbody>{$rows}</tbody></table>
{$details}
</body>
</html>
HTML;
    }
}
