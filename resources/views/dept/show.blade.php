<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details - #{{ $complaint->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 font-sans">

    <header class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">Department Dashboard</h1>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                    Logout
                </button>
            </form>
        </div>
    </header>

    <main class="container mx-auto p-6">
        <div class="mb-6">
            <a href="{{ route('showdept.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                ← Back to Dashboard
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 bg-white p-8 rounded-lg shadow-lg">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-2xl font-bold text-gray-900">{{ $complaint->title }}</h2>
                    @php
                        $statusClass = [ 'pending' => 'bg-blue-100 text-blue-800', 'checking' => 'bg-yellow-100 text-yellow-800', 'solved' => 'bg-green-100 text-green-800', 'rejected' => 'bg-red-100 text-red-800', ][$complaint->status] ?? 'bg-gray-100 text-gray-800';
                    @endphp
                    <span class="px-3 py-1 font-semibold leading-tight rounded-full text-sm {{ $statusClass }}">
                        {{ ucfirst($complaint->status) }}
                    </span>
                </div>

                <div class="text-sm text-gray-500 mb-6 border-b pb-4">
                    <span>Complaint ID: <strong>#{{ $complaint->id }}</strong></span> •
                    <span>Submitted by: <strong>{{ $complaint->student->Stud_fname }} {{ $complaint->student->Stud_lname }}</strong></span> •
                    <span>Date: <strong>{{ $complaint->created_at->format('d M Y, h:i A') }}</strong></span>
                </div>

                <div class="prose max-w-none text-gray-700">
                    <p>{{ $complaint->description }}</p>
                </div>

                <!-- CRITICAL: Check for the response using the 'responses' relationship -->
                @if($complaint->responses)
                <div class="mt-8 border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Your Response</h3>
                    <div class="bg-gray-50 p-4 rounded-md border">
                        <!-- CRITICAL: Display the response text from the 'responses' relationship -->
                        <p class="text-gray-700">{{ $complaint->responses->response }}</p>
                        <!-- CRITICAL: Display the date from the 'responses' relationship -->
                        <p class="text-xs text-gray-500 mt-2">Responded on: {{ $complaint->responses->created_at->format('d M Y') }}</p>
                    </div>
                </div>
                @endif
            </div>

            <div class="bg-white p-8 rounded-lg shadow-lg h-fit">
                <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-3">Take Action</h3>

                <form action="{{ route('dept.respond', $complaint->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Update Status</label>
                        <select name="status" id="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" required onchange="toggleResponse()">
                            <option value="">-- Select New Status --</option>
                            <option value="checking" @if($complaint->status == 'checking') selected @endif>Checking</option>
                            <option value="solved" @if($complaint->status == 'solved') selected @endif>Mark as Solved</option>
                            <option value="rejected" @if($complaint->status == 'rejected') selected @endif>Mark as Rejected</option>
                        </select>
                    </div>

                    <div id="responseBox" class="hidden">
                        <label for="response" class="block text-sm font-medium text-gray-700 mb-1">Response Message</label>
                        <!-- CRITICAL: Pre-fill the textarea using the 'responses' relationship -->
                        <textarea name="response" id="response" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500" rows="5" placeholder="Provide a final response for the student...">{{ optional($complaint->responses)->response }}</textarea>
                    </div>

                    <div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300">
                            Submit Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function toggleResponse() {
            const status = document.getElementById('status').value;
            const box = document.getElementById('responseBox');
            const textarea = document.getElementById('response');
            if (status === 'solved' || status === 'rejected') {
                box.classList.remove('hidden');
                textarea.setAttribute('required', 'required');
            } else {
                box.classList.add('hidden');
                textarea.removeAttribute('required');
            }
        }
        document.addEventListener('DOMContentLoaded', toggleResponse);
    </script>
</body>
</html>