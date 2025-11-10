<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex flex-col min-h-screen">

    {{-- Sidebar + Konten --}}
    <div class="flex flex-1">
        {{-- Sidebar --}}
        @include('admin.sidebar')

        {{-- Konten Utama --}}
        <main class="flex-1 p-6 bg-gray-100">
            @yield('content')
        </main>
    </div>

</body>
</html>
