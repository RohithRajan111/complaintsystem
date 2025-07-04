<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white shadow-md rounded-lg p-6">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Student Registration</h2>

        <form action="{{ route('student.register') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Name -->
            <div>
                <label for="Stud_name" class="block text-gray-700 font-medium mb-1">Name</label>
                <input type="text" name="Stud_name" id="Stud_name" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Email -->
            <div>
                <label for="Stud_email" class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="Stud_email" id="Stud_email" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Password -->
            <div>
                <label for="Password" class="block text-gray-700 font-medium mb-1">Password</label>
                <input type="password" name="Password" id="Password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="Password_confirmation" class="block text-gray-700 font-medium mb-1">Confirm Password</label>
                <input type="password" name="Password_confirmation" id="Password_confirmation" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Submit -->
            <div>
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-md transition">
                    Register
                </button>
            </div>
        </form>
    </div>

</body>
</html>
