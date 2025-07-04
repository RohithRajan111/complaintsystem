<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
     <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style> .data-container { position: relative; min-height: 200px; } </style>
</head>
<body class="h-full">
<div class="min-h-full">
    <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
        <div class="flex-1 flex flex-col min-h-0 bg-gray-800">
            <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900 text-white font-bold text-lg">Admin Panel</div>
            <div class="flex-1 flex flex-col overflow-y-auto">
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md"><i class="bi bi-speedometer2 mr-3"></i> Dashboard</a>
                    <a href="{{ route('complaints.index') }}" class="{{ request()->routeIs('admin.complaints.index') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md"><i class="bi bi-journal-text mr-3"></i> Complaints</a>
                    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('admin.students.index') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md"><i class="bi bi-people mr-3"></i> Students</a>
                    <a href="{{ route('departments.index') }}" class="{{ request()->routeIs('admin.departments.index') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md"><i class="bi bi-building mr-3"></i> Departments</a>
                    <a href="{{ route('logs.index') }}" class="{{ request()->routeIs('admin.logs.index') ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md"><i class="bi bi-clipboard-data mr-3"></i> Action Logs</a>
                </nav>
            </div>
        </div>
    </div>
    <div class="md:pl-64 flex flex-col flex-1">
        <header class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white shadow justify-end items-center px-4">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-gray-500 hover:text-gray-700">Sign out</a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        </header>
        <main>
            <div class="py-6"><div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                @if (session('success'))<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert"><p>{{ session('success') }}</p></div>@endif
                @if (session('error'))<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert"><p>{{ session('error') }}</p></div>@endif
                @if ($errors->any())<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4"><ul>@foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul></div>@endif
                @yield('content')
            </div></div>
        </main>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@stack('scripts')
</body>
</html>