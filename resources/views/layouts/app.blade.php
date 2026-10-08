<!DOCTYPE html>
<html lang="id">
<head>
    <title>@yield('title', 'Simple POS')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-slate-800 min-h-screen">
    <x-nav />
    <main class="max-w-6xl mx-auto px-4 sm:px-6 py-6">
        @yield('content')
    </main>
</body>
</html>