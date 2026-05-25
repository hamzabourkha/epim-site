@extends('layouts.public')
@section('content')
@include('public.partials')
@php epimHero(__('site.preapply.hero_title'), __('site.preapply.hero_text')); @endphp

<style>
    .preapply-section { padding-top: 38px; }
    .preapply-layout-direct {
        display: grid !important;
        grid-template-columns: minmax(430px, .95fr) minmax(0, .9fr) !important;
        gap: 42px !important;
        align-items: start !important;
    }
    .preapply-layout-direct .preapply-card {
        grid-column: 1 !important;
        width: 100% !important;
        max-width: none !important;
        position: static !important;
        justify-self: stretch !important;
    }
    .preapply-layout-direct .preapply-intro {
        grid-column: 2 !important;
        padding-top: 10px !important;
        max-width: 650px !important;
    }
    .preapply-layout-direct .preapply-intro h2 {
        font-size: clamp(1.85rem, 2.7vw, 2.75rem) !important;
        line-height: 1.12 !important;
        margin-bottom: 16px !important;
    }
    .preapply-layout-direct .preapply-intro > p {
        color: #52657c !important;
        font-size: 1.08rem !important;
        line-height: 1.75 !important;
        margin-bottom: 22px !important;
    }
    .preapply-layout-direct .preapply-note {
        max-width: 620px !important;
    }
    @media (max-width: 991px) {
        .preapply-layout-direct {
            grid-template-columns: 1fr !important;
        }
        .preapply-layout-direct .preapply-card,
        .preapply-layout-direct .preapply-intro {
            grid-column: auto !important;
        }
    }
</style>

<section class="section preapply-section">
    <div class="container preapply-layout-direct">
        <aside class="apply-card preapply-card">
            <h3>{{ __('site.preapply.form_title') }}</h3>
            <p>{{ __('site.preapply.form_subtitle') }}</p>
            <form method="post" action="{{ route('preapply.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('site.preapply.fields.first_name') }}</label>
                        <input class="form-control" name="first_name" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('site.preapply.fields.last_name') }}</label>
                        <input class="form-control" name="last_name" value="{{ old('last_name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('site.preapply.fields.phone') }}</label>
                        <input class="form-control" name="phone" value="{{ old('phone') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('site.preapply.fields.email') }}</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('site.preapply.fields.city') }}</label>
                        <input class="form-control" name="city" value="{{ old('city') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('site.preapply.fields.level') }}</label>
                        <input class="form-control" name="education_level" value="{{ old('education_level') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">{{ __('site.preapply.fields.formation') }}</label>
                        <select class="form-select" name="formation_id">
                            <option value="">{{ __('site.preapply.fields.formation_placeholder') }}</option>
                            @foreach($formations as $formation)
                                <option value="{{ $formation->id }}" @selected(old('formation_id') == $formation->id)>{{ $formation->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($errors->any())
                    <div class="alert alert-danger mt-3 mb-0">{{ __('site.preapply.error') }}</div>
                @endif
                <button class="btn btn-gold w-100 mt-4">{{ __('site.preapply.submit') }}</button>
            </form>
        </aside>

        <article class="preapply-intro">
            <span class="eyebrow">Préinscription rapide</span>
            <h2>Vous êtes pressé ? Envoyez votre demande en moins de deux minutes.</h2>
            <p>Remplissez les informations essentielles. L’équipe EPIM vous rappelle pour confirmer la formation souhaitée, vous orienter et compléter le dossier si nécessaire.</p>
            <div class="preapply-note">
                <strong>Après l’envoi</strong>
                <p>Un conseiller EPIM vous contacte pour répondre à vos questions, vérifier votre niveau et vous expliquer les prochaines étapes d’inscription.</p>
            </div>
        </article>
    </div>
</section>
@endsection