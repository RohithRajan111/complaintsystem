<div class="overflow-x-auto mt-6 bg-white shadow rounded-lg p-4">
    <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 font-semibold">
            <tr>
                <th class="px-4 py-2">Title</th>
                <th class="px-4 py-2">Description</th>
                <th class="px-4 py-2">Status</th>
                <th class="px-4 py-2">Response</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($complaints as $complaint)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3">{{ $complaint->title }}</td>
                    <td class="px-4 py-3">{{ $complaint->description }}</td>
                    <td class="px-4 py-3 capitalize">{{ $complaint->status }}</td>
                    <td class="px-4 py-3">
                        @if (!$complaint->response)
                            <form action="{{ route('dept.respond', $complaint->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium mb-1 text-gray-700">Update Status:</label>
                                    <select name="status" id="status_{{ $complaint->id }}"
                                            class="w-full border border-gray-300 rounded-md px-3 py-2"
                                            required onchange="toggleResponse({{ $complaint->id }})">
                                        <option value="">-- Select Status --</option>
                                        <option value="checking">Checking</option>
                                        <option value="solved">Solved</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>

                                <div id="responseBox_{{ $complaint->id }}" class="hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Response Message:</label>
                                    <div class="flex items-end gap-2">
                                        <textarea name="response"
                                                  class="w-full border border-gray-300 rounded-md px-3 py-2"
                                                  rows="3"
                                                  placeholder="Write your response..."></textarea>
                                        <button type="submit"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition font-medium h-fit">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="text-sm space-y-1">
                                <p><span class="font-semibold text-gray-700">Status:</span> {{ $complaint->status }}</p>
                                <p><span class="font-semibold text-gray-700">Response:</span> {{ $complaint->response->response }}</p>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center text-gray-500 py-6">No complaints found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
