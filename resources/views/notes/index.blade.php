<x-app-layout>
    <x-slot name="header">
        <form action="{{ route('notes.index') }}" method="GET" class="w-full max-w-2xl flex items-center bg-gray-50 dark:bg-gray-800 rounded-full px-6 py-1 border border-gray-100 dark:border-gray-700 focus-within:ring-2 focus-within:ring-yellow-100 dark:focus-within:ring-yellow-900 transition-all">
            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search note..." class="w-full bg-transparent border-none focus:ring-0 text-sm text-gray-700 dark:text-gray-200 outline-none placeholder-gray-400">
        </form>
    </x-slot>

    <div>
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-8 bg-green-50 dark:bg-green-900/30 border border-green-100 dark:border-green-800 text-green-600 dark:text-green-400 px-6 py-4 rounded-2xl flex items-center">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight uppercase text-sm">
                {{ request('folder_id') ? $folders->find(request('folder_id'))->name ?? 'Folder' : 'All Notes' }}
            </h2>
        </div>

        @if($notes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($notes as $note)
                    <a href="{{ route('notes.edit', $note) }}" 
                       class="relative group rounded-[20px] p-6 card-shadow transition-all duration-300 hover:-translate-y-1 hover:shadow-xl bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700/80 block"
                       style="{{ \App\Models\Note::formatHexColor($note->color, '20') ? 'background-color: ' . \App\Models\Note::formatHexColor($note->color, '20') . ';' : '' }}">
                        
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 line-clamp-1 flex-1">
                                {{ $note->title }}
                            </h3>
                            @if($note->is_pinned)
                                <span class="text-pink-500 flex-shrink-0 ml-2">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2z"/>
                                    </svg>
                                </span>
                            @endif
                        </div>
                    
                        <!-- We strip tags from content since it might be HTML from Quill -->
                        <div class="text-sm text-gray-600 dark:text-gray-300 mb-6 line-clamp-4 leading-relaxed">
                            {!! Str::limit(strip_tags($note->content), 120) !!}
                        </div>
                    
                        <div class="flex justify-between items-center mt-auto pt-4 border-t border-gray-100/50 dark:border-gray-700/50">
                            @if($note->folder)
                                <span class="text-xs font-semibold text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/30 px-3 py-1 rounded-full border border-yellow-100 dark:border-yellow-800/50">
                                    {{ $note->folder->name }}
                                </span>
                            @else
                                <span></span>
                            @endif
                    
                            <div class="flex items-center gap-3">
                                <div class="text-xs text-gray-400 dark:text-gray-500">
                                    {{ __('last edited: '). $note->updated_at->format('d M Y') }}
                                </div>
                    
                                <div x-data="{ open: false }" class="relative flex-shrink-0">
                                    <button @click.stop="open = !open" 
                                            class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded transition duration-150">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                        </svg>
                                    </button>
                                    
                                    <div x-show="open" 
                                         @click.outside="open = false" 
                                         x-transition 
                                         class="absolute right-0 mt-1 w-28 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-lg py-1.5 text-xs text-left z-30" 
                                         style="display: none;">
                                        <form id="pin-form-{{ $note->id }}" method="POST" action="{{ route('notes.toggle-pin', $note) }}" class="hidden">
                                            @csrf
                                            @method('PATCH')
                                        </form>
                                        <button @click.stop="document.getElementById('pin-form-{{ $note->id }}').submit()" 
                                                class="w-full text-left px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
                                            {{ $note->is_pinned ? 'Unpin' : 'Pin' }}
                                        </button>
                                        <button @click.stop="open = false; $dispatch('confirm-delete', { id: {{ $note->id }} })" 
                                                class="w-full text-left px-3 py-1.5 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 text-red-500 transition">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>

                    <!-- Hidden forms untuk delete (opsional) -->
                    <form id="delete-form-{{ $note->id }}" method="POST" action="{{ route('notes.destroy', $note) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                    <form id="pin-form-{{ $note->id }}" action="{{ route('notes.toggle-pin', $note) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-10">
                {{ $notes->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-20 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-[2rem]">
                <div class="w-20 h-20 bg-yellow-50 dark:bg-yellow-900/20 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-10 h-10 text-yellow-400 dark:text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white">No notes yet</h3>
                <p class="mt-2 text-gray-400 dark:text-gray-500 max-w-sm mb-8">It's a little quiet here. Why don't you start by writing down your first idea?</p>
                <a href="{{ route('notes.create', ['folder_id' => request('folder_id')]) }}" class="bg-yellow-500 text-white px-8 py-3 rounded-xl hover:bg-yellow-600 transition shadow-lg shadow-yellow-200 dark:shadow-none font-medium">
                    Create New Note
                </a>
            </div>
        @endif
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="relative p-8 w-full max-w-md shadow-2xl rounded-[1rem] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 transform scale-95 transition-transform duration-300" id="deleteModalContent">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-red-50 dark:bg-red-900/30 text-red-500 mx-auto flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Delete Note?</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-8">This action cannot be undone. The note will be permanently deleted from your account.</p>
                
                <div class="flex space-x-3">
                    <button onclick="closeModal()" class="flex-1 px-4 py-3 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-red-500 text-white font-medium rounded-xl hover:bg-red-600 transition shadow-lg shadow-red-200 dark:shadow-none">
                            Delete Note
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(noteId) {
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');
            document.getElementById('deleteForm').action = `/notes/${noteId}`;
            
            // Animation
            setTimeout(() => {
                modal.classList.remove('opacity-0');
                content.classList.remove('scale-95');
            }, 10);
        }
        
        function closeModal() {
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            
            modal.classList.add('opacity-0');
            content.classList.add('scale-95');
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</x-app-layout>