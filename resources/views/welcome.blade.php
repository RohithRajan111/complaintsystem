<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Complaint & Resolution Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts for a nicer look -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Header / Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-indigo-600">
                <a href="/">🎓 Student Complaint & Resolution Portal</a>
            </div>
            <div class="space-x-4">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-indigo-600 font-medium transition duration-300">Login</a>
                <a href="{{ route('showstudent.register') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-5 rounded-lg shadow-md transition duration-300">
                    Register
                </a>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main>
        <!-- Hero Section -->
        <section class="container mx-auto px-6 py-20 lg:py-24">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left Column: Text Content -->
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 leading-tight mb-4">
                        Bridging Communication. <br>
                        <span class="text-indigo-600">Building Solutions.</span>
                    </h1>
                    <p class="text-lg text-gray-600 mb-8">
                        Our platform ensures your concerns are addressed efficiently, transparently, and effectively. Raise an issue, track its progress, and find a resolution.
                    </p>
                    <div class="flex justify-center lg:justify-start space-x-4">
                        <a href="{{ route('showstudent.register') }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-8 rounded-lg text-lg font-semibold shadow-lg transition duration-300 transform hover:scale-105">
                            Make an Account
                        </a>
                        <a href="{{ route('login') }}"
                           class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-3 px-8 rounded-lg text-lg font-semibold transition duration-300">
                            Member Login
                        </a>
                    </div>
                </div>
                <!-- Right Column: Image -->
                <div class="flex justify-center">
                    <!-- THIS IS THE FINAL CORRECTED LINE -->
                    <img src="{{ asset('images/new.svg') }}" alt="Illustration of people collaborating to solve a problem" class="w-full max-w-md lg:max-w-full">
                </div>
            </div>
        </section>

        <!-- "How It Works" Section -->
        <section class="bg-white py-20 lg:py-24">
            <div class="container mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold mb-3">A Simple, Transparent Process</h2>
                <p class="text-gray-600 mb-12 max-w-2xl mx-auto">Getting your issue resolved is as easy as one, two, three. We guide you every step of the way.</p>
                
                <div class="grid md:grid-cols-3 gap-10">
                    <!-- Step 1 -->
                    <div class="p-8 border border-gray-200 rounded-xl shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-300">
                        <div class="bg-indigo-100 text-indigo-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">1. Submit Your Complaint</h3>
                        <p class="text-gray-500">Clearly describe your issue using our simple form. Attach files if needed.</p>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="p-8 border border-gray-200 rounded-xl shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-300">
                         <div class="bg-indigo-100 text-indigo-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">2. Track The Progress</h3>
                        <p class="text-gray-500">Receive real-time status updates as your complaint is assigned and reviewed.</p>
                    </div>

                    <!-- Step 3 -->
                    <div class="p-8 border border-gray-200 rounded-xl shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-300">
                         <div class="bg-indigo-100 text-indigo-600 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-6">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">3. Receive a Resolution</h3>
                        <p class="text-gray-500">Get notified with the final outcome and the steps taken to address your concern.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <p>© {{ date('Y') }} ResolutionPortal. All rights reserved.</p>
            <p class="text-sm text-gray-400 mt-2">A platform for clear communication and effective solutions.</p>
        </div>
    </footer>

</body>
</html>