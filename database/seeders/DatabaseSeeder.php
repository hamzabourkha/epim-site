<?php

namespace Database\Seeders;

use App\Models\Formation;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Partner;
use App\Models\Post;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            ['Direction EPIM', 'admin@epim.ma', 'admin'],
            ['Directeur Pédagogique', 'directeur@epim.ma', 'directeur'],
            ['Assistante Admissions', 'admission@epim.ma', 'assistant'],
            ['Formateur Digital', 'formateur@epim.ma', 'formateur'],
        ];

        foreach ($users as [$name, $email, $role]) {
            User::updateOrCreate(['email' => $email], [
                'name' => $name,
                'role' => $role,
                'phone' => '05 35 52 09 66',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        $formations = [
            ['Développement Digital', 'Formations diplomantes', 'Parcours Technicien spécialisé en développement digital pour concevoir des applications web modernes, interfaces performantes et services digitaux utiles aux entreprises marocaines.', '2 ans', 'Baccalauréat ou niveau bac selon étude du dossier', 91, 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1400&q=80'],
            ['Développement Informatique', 'Formations diplomantes', 'Formation diplômante de Technicien spécialisé couvrant algorithmique, bases de données, programmation orientée objet, maintenance et projets logiciels.', '2 ans', 'Baccalauréat scientifique, technique ou équivalent', 88, 'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1400&q=80'],
            ['Infographie', 'Formations diplomantes', 'Parcours Technicien en infographie : création graphique, identité visuelle, PAO, prépresse, bases motion et portfolio professionnel.', '2 ans', 'Niveau bac ou plus', 84, 'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=1400&q=80'],
            ['Gestion des Entreprises', 'Formations diplomantes', 'Formation diplômante en gestion : administration, commercial, ressources humaines, communication interne et outils de pilotage pour PME.', '2 ans', 'Baccalauréat ou niveau bac', 86, 'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1400&q=80'],
            ['Comptabilité', 'Formation continue', 'Comptabilité générale, fiscalité marocaine, paie, déclarations, tableaux de bord et logiciels professionnels pour renforcer son profil.', '6 à 12 mois', 'Niveau bac recommandé', 87, 'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=1400&q=80'],
            ['Langues', 'Formation a la carte certifiante', 'Français professionnel, anglais business, communication orale, préparation aux entretiens et rédaction administrative avec attestation EPIM.', '1 à 6 mois', 'Tous niveaux après test', 82, 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=80'],
            ['Soft Skills', 'Formation a la carte certifiante', 'Leadership, communication, prise de parole, travail en équipe, CV, LinkedIn et simulation d’entretien en modules courts certifiants.', '1 à 3 mois', 'Tous publics', 90, 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1400&q=80'],
            ['Call Center', 'Formation pour les entreprises', 'Techniques de relation client, CRM, scripts, gestion des objections, français oral et mise en situation plateau pour centres d’appel et équipes support.', 'Selon cahier des charges', 'Équipes débutantes ou confirmées', 89, 'https://images.unsplash.com/photo-1556761175-5973dc0f32e7?auto=format&fit=crop&w=1400&q=80'],
            ['Bureautique', 'Formation continue', 'Word, Excel avancé, PowerPoint, organisation numérique, reporting et productivité administrative.', '1 à 3 mois', 'Tous niveaux', 83, 'https://images.unsplash.com/photo-1483058712412-4245e9b90334?auto=format&fit=crop&w=1400&q=80'],
            ['Marketing Digital', 'Formation continue', 'Community management, SEO, publicité Meta/Google, analytics, contenus et campagnes pour marques locales.', '3 à 8 mois', 'Niveau bac ou expérience professionnelle', 88, 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1400&q=80'],
        ];

        foreach ($formations as [$title, $category, $description, $duration, $level, $rate, $image]) {
            Formation::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'category' => $category,
                'image' => $image,
                'description' => $description,
                'duration' => $duration,
                'level_required' => $level,
                'insertion_rate' => $rate,
                'is_featured' => $rate >= 86,
                'objectives' => ['Acquérir une compétence directement exploitable', 'Utiliser les outils IA de manière responsable dans le métier', 'Réaliser des ateliers pratiques encadrés', 'Construire un dossier professionnel présentable aux recruteurs'],
                'opportunities' => ['Stage en entreprise', 'Poste junior', 'Freelance ou accompagnement entrepreneurial', 'Évolution vers une spécialisation'],
                'program' => ['Fondamentaux métier', 'Outils professionnels', 'Initiation à l’IA appliquée au domaine', 'Cas pratiques marocains', 'Projet de fin de formation', 'Préparation insertion et entretien'],
                'skills' => ['Rigueur professionnelle', 'Communication', 'Maîtrise des outils', 'Résolution de problèmes', 'Culture digitale et IA responsable'],
            ]);
        }

        $posts = [
            ['Formation professionnelle 2025-2026 : pourquoi le digital et l’IA deviennent incontournables', 'Formation professionnelle', 'La rentrée 2025-2026 confirme la place du digital et de l’intelligence artificielle dans les nouvelles filières de formation au Maroc.', 'La dynamique nationale de la formation professionnelle montre une évolution nette vers les compétences digitales, l’intelligence artificielle, la cybersécurité et les métiers émergents. Pour un établissement comme EPIM, cela signifie adapter les exercices, les projets et les ateliers aux outils réels utilisés par les entreprises. Les stagiaires doivent apprendre à chercher, produire, vérifier et présenter leur travail avec méthode, en utilisant l’IA comme assistant et non comme remplacement de la compétence. Source de contexte : communiqués OFPPT sur la rentrée 2025-2026.'],
            ['Plan d’action 2026 de l’OFPPT : IA, cybersécurité et gaming parmi les métiers émergents', 'Actualité Maroc', 'Le plan d’action 2026 de l’OFPPT accorde une attention particulière aux métiers émergents comme l’IA et la cybersécurité.', 'Les orientations 2026 mettent en avant les métiers émergents : intelligence artificielle, cybersécurité, économie verte et industrie du gaming. Cette tendance confirme que les centres de formation doivent préparer des profils capables de comprendre les outils numériques, les données, l’automatisation et les nouveaux usages professionnels. EPIM intègre progressivement ces dimensions dans ses parcours : veille, prompt engineering de base, productivité bureautique assistée par IA et culture de sécurité numérique. Source de contexte : Conseil d’administration OFPPT, mai 2026.'],
            ['IA au service de la cybersécurité : une thématique qui monte en formation professionnelle', 'Intelligence artificielle', 'La Journée Régionale du Digital de Fès-Meknès a mis en avant l’IA appliquée à la cybersécurité.', 'La région Fès-Meknès a accueilli en 2025 une journée digitale autour de l’intelligence artificielle au service de la cybersécurité. Pour les stagiaires en développement informatique et digital, ce sujet est concret : protection des comptes, hygiène numérique, détection des risques, sauvegarde et responsabilité dans l’usage des données. EPIM peut transformer cette actualité en ateliers : mots de passe, phishing, sauvegarde, bonnes pratiques et mini audit de sécurité. Source de contexte : actualité OFPPT Fès-Meknès, juin 2025.'],
            ['Gestion des entreprises : les PME marocaines ont besoin de profils polyvalents et digitaux', 'Gestion des entreprises', 'La gestion moderne demande des profils capables de combiner administration, outils numériques, reporting et communication.', 'Les entreprises marocaines recherchent des assistants, gestionnaires et commerciaux capables de manipuler les données, produire des tableaux de bord, communiquer clairement et utiliser les outils numériques. L’IA ajoute une nouvelle compétence : savoir préparer un compte rendu, structurer une réponse client, analyser un tableau ou générer une trame de procédure tout en vérifiant la qualité du résultat. EPIM oriente ses ateliers gestion vers ces usages pratiques.'],
            ['Marché du travail marocain : pourquoi les soft skills restent décisives', 'Insertion', 'Les compétences humaines restent essentielles même dans un contexte de digitalisation et d’IA.', 'Ponctualité, communication, autonomie, sens du service et capacité à apprendre vite restent des critères importants pour les recruteurs. L’IA peut aider à préparer un CV ou simuler un entretien, mais elle ne remplace pas la posture professionnelle. EPIM combine donc ateliers soft skills, simulations, prise de parole et méthodes de recherche d’emploi.'],
            ['Développement digital : apprendre à coder avec l’IA sans perdre les bases', 'Développement digital', 'L’IA peut accélérer l’apprentissage du code, mais les bases restent indispensables pour comprendre, corriger et sécuriser.', 'Les outils IA génèrent du code, expliquent des erreurs et proposent des pistes. Mais un stagiaire doit comprendre l’algorithmique, les bases de données, la logique web, la sécurité et la maintenance. Dans les ateliers EPIM, l’IA est utilisée comme assistant pédagogique : reformuler un problème, comparer des solutions, documenter un projet et tester des cas limites.'],
            ['Marketing digital au Maroc : contenu, données et IA changent les pratiques', 'Marketing digital', 'Le marketing digital évolue vers plus de mesure, de contenu utile et d’automatisation responsable.', 'Community management, SEO, publicité et analyse de performance demandent aujourd’hui des outils plus avancés. L’IA aide à générer des idées, préparer des calendriers éditoriaux, analyser des personas et produire des variantes de messages. La compétence importante reste la stratégie : connaître la cible, vérifier les informations, adapter le ton et mesurer les résultats.'],
            ['Bureautique avancée : Excel, reporting et IA pour gagner en productivité', 'Bureautique', 'La bureautique moderne ne se limite plus à Word et Excel : elle inclut automatisation, tableaux de bord et assistance IA.', 'Dans les services administratifs, la productivité passe par des documents propres, des tableaux fiables et des rapports lisibles. Les outils IA peuvent aider à résumer, structurer ou expliquer des données, mais la précision vient de la maîtrise des formules, de la vérification et de l’organisation des fichiers.'],
            ['Relation client et call center : l’IA assiste, l’humain rassure', 'Relation client', 'Les centres de relation client utilisent de plus en plus scripts, CRM, analyse de conversations et outils intelligents.', 'La formation call center doit préparer à la voix, à l’écoute, à la reformulation et à la gestion des objections. L’IA peut aider à améliorer un script, analyser des motifs d’appel ou proposer une réponse type, mais la qualité de service dépend toujours de l’empathie, de la langue et de la discipline.'],
            ['Infographie et création : portfolio, IA générative et responsabilité graphique', 'Infographie', 'Les outils IA transforment la création graphique, mais le regard, la composition et le brief restent au centre du métier.', 'Un infographiste doit savoir analyser un brief, construire une identité visuelle, respecter les formats et présenter ses choix. L’IA générative peut servir à explorer des pistes, créer des moodboards ou accélérer certaines recherches, mais le portfolio doit montrer une vraie intention graphique et une exécution professionnelle.'],
            ['Entreprises partenaires : former selon les besoins réels du terrain', 'Partenariats', 'Le lien avec les entreprises permet d’adapter les ateliers, les stages et les projets aux besoins actuels du marché.', 'EPIM développe des liens avec les entreprises pour comprendre les profils recherchés : assistants polyvalents, développeurs juniors, agents relation client, infographistes, profils marketing ou comptables. Ces retours permettent d’ajuster les projets, les outils et les critères d’évaluation.'],
            ['Pourquoi une préinscription bien remplie accélère l’admission', 'Admissions', 'Un dossier clair aide l’administration à orienter rapidement le candidat vers la filière adaptée.', 'La préinscription doit contenir des informations fiables : niveau scolaire, filière souhaitée, téléphone, email et pièces disponibles. Lorsque le candidat joint la CIN, l’attestation ou le diplôme et les relevés de notes, l’équipe peut vérifier l’admissibilité plus vite et proposer un entretien plus efficace.'],
        ];

        $postImages = [
            'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1556761175-b413da4baf72?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1515879218367-8466d910aaa4?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1554224155-8d04cb21cd6c?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1556745757-8d76bdb6984b?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1561070791-2526d30994b5?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1542744173-8e7e53415bb0?auto=format&fit=crop&w=1400&q=80',
            'https://images.unsplash.com/photo-1497366811353-6870744d04b2?auto=format&fit=crop&w=1400&q=80',
        ];

        foreach ($posts as $index => [$title, $category, $excerpt, $body]) {
            Post::updateOrCreate(['slug' => Str::slug($title)], [
                'title' => $title,
                'category' => $category,
                'image' => $postImages[$index % count($postImages)],
                'excerpt' => $excerpt,
                'body' => $body . "\n\nÀ EPIM, cette évolution est traduite en ateliers pratiques, projets encadrés, coaching insertion et utilisation responsable des outils numériques.",
                'seo_title' => $title . ' - EPIM Meknès',
                'seo_description' => $excerpt,
                'published_at' => now()->subDays(rand(1, 30)),
                'is_published' => true,
            ]);
        }

        Post::orderBy('published_at')->orderBy('id')->get()->values()->each(function (Post $post, int $index) use ($postImages) {
            $post->update(['image' => $postImages[$index % count($postImages)]]);
        });

        foreach (['FEDE', 'ANAPEC', 'DFP', 'Centre d’appel partenaire', 'Agence digitale Meknès', 'Cabinet comptable Atlas', 'Startup IT Saïss', 'Entreprise RH Maroc'] as $name) {
            Partner::updateOrCreate(['name' => $name], [
                'type' => in_array($name, ['FEDE', 'ANAPEC', 'DFP'], true) ? 'Institutionnel' : 'Entreprise',
                'logo' => 'https://dummyimage.com/360x180/004B9C/ffffff&text=' . urlencode($name),
                'description' => 'Partenaire mobilisé autour de la formation, des stages, de l’insertion professionnelle et de la qualité pédagogique.',
                'website' => 'https://www.epim.ma',
            ]);
        }

        foreach ([
            ['Salma Ait Lahcen', 'Lauréate Développement Digital', 'EPIM m’a aidée à transformer mes bases en vrais projets. Le suivi et les ateliers insertion ont fait la différence.'],
            ['Youssef El Amrani', 'Responsable RH partenaire', 'Nous apprécions le sérieux des stagiaires EPIM, leur ponctualité et leur capacité à apprendre vite.'],
            ['Imane Rami', 'Stagiaire Infographie', 'Les projets concrets, les critiques de portfolio et l’ambiance de travail donnent confiance.'],
            ['Nabil Bennani', 'Parent d’étudiant', 'L’équipe administrative est disponible, claire et rassurante. On sent une vraie organisation.'],
        ] as [$name, $role, $content]) {
            Testimonial::updateOrCreate(['name' => $name], compact('role', 'content') + [
                'avatar' => 'https://ui-avatars.com/api/?background=004B9C&color=fff&name=' . urlencode($name),
                'rating' => 5,
            ]);
        }

        foreach (['Campus', 'Salles', 'Événements', 'Remise diplômes', 'Workshops', 'Intégration', 'Vidéos', 'Formations'] as $category) {
            for ($i = 1; $i <= 2; $i++) {
                GalleryItem::updateOrCreate(['title' => "$category EPIM $i"], [
                    'category' => $category,
                    'type' => $category === 'Videos' ? 'video' : 'image',
                    'path' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1000&q=80',
                    'description' => 'Moment de vie pédagogique EPIM à Meknès.',
                ]);
            }
        }

        Page::updateOrCreate(['slug' => 'politique-confidentialite'], [
            'title' => 'Politique de confidentialité',
            'content' => 'EPIM protège les données transmises via les formulaires du site et les utilise uniquement pour le suivi administratif, pédagogique et commercial.',
            'seo_title' => 'Politique de confidentialité EPIM',
            'seo_description' => 'Confidentialité et traitement des données personnelles EPIM.',
        ]);

        SiteSetting::updateOrCreate(['key' => 'contact'], [
            'value' => [
                'phone' => '05 35 52 09 66',
                'email' => 'contact@epim.ma',
                'address' => 'EPIM - Meknès, Maroc',
                'whatsapp' => '0535520966',
            ],
        ]);

        SiteSetting::updateOrCreate(['key' => 'rubrique.galerie'], [
            'value' => [
                'label' => 'Galerie',
                'route' => 'gallery',
                'is_under_construction' => true,
                'title' => 'Page en cours de construction',
                'message' => 'La galerie EPIM est en cours de préparation. Les photos du campus, des ateliers, des événements et des remises de diplômes seront publiées prochainement.',
            ],
        ]);
    }
}
