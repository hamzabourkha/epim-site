<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Connexion EPIM</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="{{ asset('css/site.css') }}" rel="stylesheet"></head>
<body class="login-screen"><form method="post" action="{{ route('login.post') }}" class="login-card">@csrf<h1>EPIM Admin</h1><p>Connexion securisee au tableau de bord.</p>@error('email')<div class="alert alert-danger">{{ $message }}</div>@enderror<input class="form-control" type="email" name="email" value="admin@epim.ma" placeholder="Email" required><input class="form-control" type="password" name="password" value="password" placeholder="Mot de passe" required><label><input type="checkbox" name="remember"> Se souvenir</label><button class="btn btn-gold w-100">Connexion</button><a href="{{ route('home') }}">Retour au site</a></form></body>
</html>
