<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 min-h-screen">

    <!-- Header: Consistent navigation -->
    <nav class="bg-gray-800 text-white px-6 py-4 flex justify-between items-center shadow">
        <span class="text-xl font-bold">Department Portal</span>
        <div class="flex items-center gap-4">
            <a href="{{ route('showdept.dashboard') }}" class="text-gray-300 hover:text-white transition">Dashboard</a>
            <a href="{{ route('dept.profile.show') }}" class="text-white font-semibold">Profile</a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Content -->
    <main class="p-6">
        <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">Edit Your Profile</h2>

            <!-- Success Message -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('dept.profile.update') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Department Name -->
                <div>
                    <label for="Dept_name" class="block text-sm font-medium text-gray-700">Department Name</label>
                    <input type="text" id="Dept_name" name="Dept_name"
                           value="{{ old('Dept_name', $department->Dept_name) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('Dept_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- HOD Name -->
                <div>
                    <label for="Hod_name" class="block text-sm font-medium text-gray-700">HOD Name</label>
                    <input type="text" id="Hod_name" name="Hod_name"
                           value="{{ old('Hod_name', $department->Hod_name) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('Hod_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Department Email -->
                <div>
                    <label for="Dept_email" class="block text-sm font-medium text-gray-700">Department Email</label>
                    <input type="email" id="Dept_email" name="Dept_email"
                           value="{{ old('Dept_email', $department->Dept_email) }}"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           required>
                    @error('Dept_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <hr class="my-4">

                <p class="text-gray-600 text-sm">Leave password fields blank if you don't want to change the password.</p>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" id="password" name="password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Confirm New Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </main>

</body>
</html>