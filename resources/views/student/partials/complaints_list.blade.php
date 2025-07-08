@if($complaints->isEmpty())
    <div class="text-center text-subtle py-16 bg-surface rounded-lg shadow-sm border border-border-color">
        <i class="fa-solid fa-magnifying-glass fa-2x mb-4 text-primary"></i><br>
        No complaints found matching your criteria.
    </div>
@else
    <div class="space-y-6">
        @foreach ($complaints as $complaint)
            <div class="bg-surface rounded-lg shadow-sm border border-border-color overflow-hidden">
                <div class="p-5 flex flex-col sm:flex-row justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-1">
                             @php
                                $statusClasses = [
                                    'pending'   => 'bg-warning/10 text-yellow-800 border-warning/20', 'checking'  => 'bg-info/10 text-blue-800 border-info/20',
                                    'solved'    => 'bg-primary/10 text-green-800 border-primary/20',  'rejected'  => 'bg-danger/10 text-red-800 border-danger/20',
                                    'withdrawn' => 'bg-gray-100 text-gray-800 border-gray-200',
                                ];
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full border {{ $statusClasses[$complaint->status] ?? 'bg-gray-200' }}">
                                {{ str_replace('_', ' ', $complaint->status) }}
                            </span>
                            <h3 class="text-lg font-bold text-strong">{{ $complaint->title }}</h3>
                        </div>
                        <p class="text-sm text-subtle">
                            To: <span class="font-medium">{{ $complaint->department->name }}</span> |
                            Submitted: <span class="font-medium">{{ $complaint->created_at->format('M d, Y') }}</span> ({{ $complaint->created_at->diffForHumans() }})
                        </p>
                    </div>
                    @if(in_array($complaint->status, ['pending', 'checking']))
                        <button data-id="{{ $complaint->id }}" class="withdraw-btn bg-danger/10 hover:bg-danger/20 text-danger text-sm px-4 py-2 rounded-md transition font-semibold flex items-center gap-2 self-start">
                            <i class="fa-solid fa-trash-can"></i> Withdraw
                        </button>
                    @endif
                </div>

                <div class="px-5 py-4 border-t border-border-color space-y-4 bg-base/50">
                    <div>
                        <p class="text-xs text-subtle uppercase font-semibold mb-1">Your Description</p>
                        <p class="text-strong text-sm whitespace-pre-wrap">{{ $complaint->description }}</p>
                    </div>

                    @if ($complaint->attachment_path)
                        <div>
                             <p class="text-xs text-subtle uppercase font-semibold mb-1">Your Attachment</p>
                            <a href="{{ asset('storage/' . $complaint->attachment_path) }}" target="_blank" class="inline-flex items-center gap-2 text-info hover:underline text-sm font-medium">
                                <i class="fa-solid fa-paperclip"></i> View Attachment
                            </a>
                        </div>
                    @endif

                    @if($complaint->responses->isNotEmpty())
                        <div class="pt-3 border-t border-border-color">
                             <p class="text-xs text-subtle uppercase font-semibold mb-2">Department Response</p>
                             @foreach($complaint->responses as $response)
                                <div class="bg-white p-3 rounded-md border border-border-color text-sm text-strong">
                                    {{ $response->response }}
                                </div>
                             @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination links will automatically include the filter query strings -->
    <div class="mt-8">
        {{ $complaints->links() }}
    </div>
@endif