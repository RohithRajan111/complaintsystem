<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Complaint Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="w-full max-w-md bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">System Login</h2>

        <form method="POST" action="{{ route('login.submit') }}">
            @csrf

            <!-- Email Address -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') border-red-500 @enderror">
                
                @error('email')
                    <p class="text-red-500 text-xs italic mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input id="password" type="password" name="password" required
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror">
                
                @error('password')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Remember Me -->
            <div class="mb-6">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remember" class="form-checkbox h-5 w-5 text-blue-600">
                    <span class="ml-2 text-gray-700 text-sm">Remember Me</span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                    Log In
                </button>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('showstudent.register') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                    Don't have an account? Register as Student
                </a>
            </div>
        </form>
    </div>


    @if($errors->any())
<div class="alert-custom mb-4" style="
    background: linear-gradient(135deg, #fff5f5 0%, #ffebee 100%);
    border-left: 4px solid #f44336;
    border-radius: 4px;
    padding: 16px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
">
    <div class="d-flex align-items-center mb-2">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#d32f2f" class="bi bi-exclamation-circle-fill me-2" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
        </svg>
        <h6 class="m-0" style="color: #d32f2f; font-weight: 600">Please fix these errors:</h6>
    </div>
    <ul class="mb-0 ps-3" style="color: #5f2120">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


</body>
</html>