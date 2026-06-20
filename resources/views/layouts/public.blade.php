<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-SGPD1K7TPB"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-SGPD1K7TPB');
        gtag('event', 'formulaire_page');
    </script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $seo['title'] ?? __('site.seo.default_title') }}</title>
    <meta name="description" content="{{ $seo['description'] ?? __('site.seo.default_description') }}">
    <meta property="og:title" content="{{ $seo['title'] ?? 'EPIM Meknès' }}">
    <meta property="og:description" content="{{ $seo['description'] ?? __('site.seo.default_og') }}">
    <meta property="og:type" content="website">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <script type="application/ld+json">{"@context":"https://schema.org","@type":"EducationalOrganization","name":"EPIM","email":"contact@epim.ma","telephone":"+212535520966","address":{"@type":"PostalAddress","addressLocality":"Meknès","addressCountry":"MA"}}</script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="{{ asset('css/site.css') }}" rel="stylesheet">
    <link href="{{ asset('css/brand.css') }}" rel="stylesheet">
    <link href="{{ asset('css/director.css') }}" rel="stylesheet">
    <link href="{{ asset('css/formations.css') }}" rel="stylesheet">
    <link href="{{ asset('css/forms.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admission.css') }}" rel="stylesheet">
    <link href="{{ asset('css/media.css') }}" rel="stylesheet">
    <link href="{{ asset('css/hero-fix.css') }}" rel="stylesheet">
    <link href="{{ asset('css/construction.css') }}" rel="stylesheet">
</head>
<body>
<div class="loader" id="loader"></div>
<nav class="navbar navbar-expand-xl fixed-top epim-nav">
    <div class="container-fluid px-3 px-xl-4">
        <div class="brand-cluster">
            <a class="navbar-brand brand-logo" href="{{ route('home') }}" aria-label="EPIM - {{ __('site.nav.home') }}">
                <img src="{{ asset('images/epim-logo.png') }}" alt="EPIM">
            </a>
            <div class="brand-name">
                <span>{!! __('site.brand_name_html') !!}</span>
            </div>
            <img class="nav-accreditation" src="{{ asset('images/epim-accreditation.webp') }}" alt="{{ __('site.accredited') }}">
        </div>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">{{ __('site.nav.home') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('about') }}">{{ __('site.nav.about') }}</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="{{ route('formations') }}" data-bs-toggle="dropdown">{{ __('site.nav.formations') }}</a>
                    <div class="dropdown-menu mega-menu p-3">
                        <div class="row g-3">
                            @foreach(\App\Models\Formation::take(6)->get() as $navFormation)
                                <div class="col-md-6"><a class="dropdown-item" href="{{ route('formations.show', $navFormation) }}"><i class="bi bi-mortarboard"></i> {{ $navFormation->title }}</a></div>
                            @endforeach
                        </div>
                    </div>
                </li>
                <li class="nav-item"><a class="nav-link" href="{{ route('admission') }}">{{ __('site.nav.admission') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('blog') }}">{{ __('site.nav.news') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('gallery') }}">{{ __('site.nav.gallery') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('partnerships') }}">{{ __('site.nav.companies') }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown lang-dropdown">
                    <button class="lang-select dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">{{ strtoupper(app()->getLocale()) }}</button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('locale', 'fr') }}">{{ __('site.lang.fr') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('locale', 'ar') }}">{{ __('site.lang.ar') }}</a></li>
                        <li><a class="dropdown-item" href="{{ route('locale', 'en') }}">{{ __('site.lang.en') }}</a></li>
                    </ul>
                </div>
                <button class="icon-btn" id="darkToggle" aria-label="Mode sombre"><i class="bi bi-moon"></i></button>
                <a class="btn btn-gold" href="{{ route('preapply') }}">{{ __('site.actions.register') }}</a>
            </div>
        </div>
    </div>
</nav>

@if(session('success'))
    <div class="toast-wrap"><div class="alert alert-success shadow">{{ session('success') }}</div></div>
@endif

<main>
    @yield('content')
</main>

<footer class="footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4"><div class="footer-brand-line"><h3>{{ __('site.footer.title') }}</h3><img src="{{ asset('images/epim-accreditation.webp') }}" alt="{{ __('site.accredited') }}"></div><p>{{ __('site.footer.text') }}</p><div class="social"><a href="https://www.instagram.com/epim.efpp/" target="_blank" rel="noopener" aria-label="Instagram EPIM"><i class="bi bi-instagram"></i></a><a href="https://www.linkedin.com/company/ecole-professionnelle-d-informatique-et-de-management/" target="_blank" rel="noopener" aria-label="LinkedIn EPIM"><i class="bi bi-linkedin"></i></a><a href="https://youtu.be/7DmLRoZG11M?si=7PI6LSQcV2Z0h-A4" target="_blank" rel="noopener" aria-label="YouTube EPIM"><i class="bi bi-youtube"></i></a></div></div>
            <div class="col-lg-2"><h4>{{ __('site.footer.navigation') }}</h4><a href="{{ route('formations') }}">{{ __('site.nav.formations') }}</a><a href="{{ route('admission') }}">{{ __('site.nav.admission') }}</a><a href="{{ route('blog') }}">{{ __('site.nav.news') }}</a><a href="{{ route('contact') }}">{{ __('site.nav.contact') }}</a></div>
            <div class="col-lg-3"><h4>{{ __('site.nav.contact') }}</h4><p>EPIM - Meknès<br>05 35 52 09 66<br>contact@epim.ma<br>administration@epim.ma</p></div>
            <div class="col-lg-3"><h4>{{ __('site.footer.newsletter') }}</h4><form class="newsletter"><input type="email" placeholder="{{ __('site.footer.email_placeholder') }}"><button class="btn btn-gold">OK</button></form></div>
        </div>
        <div class="footer-bottom">© {{ date('Y') }} EPIM. {{ __('site.footer.rights') }} <a href="{{ route('login') }}">{{ __('site.footer.admin') }}</a></div>
    </div>
</footer>

<div class="cookie" id="cookie"><span>{{ __('site.footer.cookies') }}</span><button class="btn btn-sm btn-gold">{{ __('site.footer.accept') }}</button></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/site.js') }}"></script>
</body>
</html>
