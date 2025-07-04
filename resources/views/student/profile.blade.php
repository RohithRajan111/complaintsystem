@extends('layouts.app')

@section('content')
<main class="max-w-3xl mx-auto py-8 px-4">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold">👤 My Profile</h2>
        <a href="{{ route('student.dashboard') }}"
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition text-sm">
            ← Back to Dashboard
        </a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('student.profile.update') }}" method="POST" class="bg-white p-6 rounded-lg shadow space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="Stud_name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
            <input type="text" name="Stud_name" id="Stud_name"
                   value="{{ old('Stud_name', $student->Stud_name) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
        </div>

        <!-- Email -->
        <div>
            <label for="Stud_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="Stud_email" id="Stud_email"
                   value="{{ old('Stud_email', $student->Stud_email) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                   required>
        </div>

        <!-- Optional Password Change Section -->
        <div class="pt-4 border-t">
            <h3 class="text-lg font-semibold mb-2">🔒 Change Password (optional)</h3>

            <!-- Current Password -->
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                <input type="password" name="current_password" id="current_password"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- New Password -->
            <div class="mb-4">
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="new_password" id="new_password"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition font-medium">
                Save Changes
            </button>
        </div>
    </form>
</main>
@endsection
