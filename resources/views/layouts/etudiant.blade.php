<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
    @yield('styles')
    @yield('scripts')
</head>
<body>
<div class="dash">
    <aside class="sidebar">

        <nav class="menu">
            @yield('sideBar')
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="/logout">
                @csrf
                <button class="logout" type="submit">⎋ Déconnexion</button>
            </form>
        </div>
    </aside>

    <main class="content">
        @yield('content')
    </main>
</div>
</body>
</html>
