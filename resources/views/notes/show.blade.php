<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center w-full">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight uppercase text-sm">
                Note Details
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('notes.index') }}" class="px-6 py-2.5 bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    Back
                </a>
                <a href="{{ route('notes.edit', $note) }}" class="px-6 py-2.5 bg-yellow-500 text-white font-medium rounded-xl hover:bg-yellow-600 transition shadow-lg shadow-yellow-200 dark:shadow-none">
                    Edit Note
                </a>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="max-w-4xl mx-auto">
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] card-shadow border border-gray-50 dark:border-gray-700 overflow-hidden transition-colors duration-300" 
                 style="background-color: {{ $note->color && $note->color !== '#ffffff' ? $note->color . '20' : '' }};">
                
                <div class="p-8 sm:p-12">
                    <div class="flex justify-between items-start mb-8">
                        <div>
                            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white mb-4 tracking-tight">{{ $note->title }}</h1>
                            <div class="flex items-center space-x-4 text-sm">
                                @if($note->folder)
                                    <span class="text-xs font-semibold text-yellow-600 dark:text-yellow-400 bg-yellow-50 dark:bg-yellow-900/30 px-3 py-1.5 rounded-full uppercase tracking-wider border border-yellow-100 dark:border-yellow-800/50">
                                        {{ $note->folder->name }}
                                    </span>
                                @endif
                                <span class="text-gray-400 dark:text-gray-500 font-medium">Last edited: {{ $note->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <form action="{{ route('notes.toggle-pin', $note) }}" method="POST">
                                @csrf
                                <button type="submit" class="p-3 bg-white dark:bg-gray-700 rounded-full card-shadow hover:shadow-md transition-shadow group">
                                    <span class="inline-block {{ $note->is_pinned ? 'text-pink-500 dark:text-pink-400' : 'text-gray-300 dark:text-gray-500 opacity-50 group-hover:opacity-100 transition-all' }}">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M5 5a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2V5zm0 8a2 2 0 012-2h6a2 2 0 012 2v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2z"/></svg>
                                    </span>
                                </button>
                            </form>
                            <button onclick="confirmDelete()" class="p-3 bg-white dark:bg-gray-700 rounded-full card-shadow hover:shadow-md transition-shadow text-red-400 dark:text-red-500 hover:text-red-500 dark:hover:text-red-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Quill content render -->
                    <div class="prose max-w-none text-gray-700 dark:text-gray-300 ql-editor px-0 min-h-[300px]" style="font-family: inherit;">
                        {!! $note->content !!}
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center opacity-0 transition-opacity duration-300">
        <div class="relative p-8 w-full max-w-md shadow-2xl rounded-[2rem] bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 transform scale-95 transition-transform duration-300" id="deleteModalContent">
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
                    <form action="{{ route('notes.destroy', $note) }}" method="POST" class="flex-1">
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

    <!-- Override Quill editor styles for dark mode in display (if user used colors, they will stick) -->
    <style>
        .dark .ql-editor.prose a { color: #facc15; }
        .dark .ql-editor.prose blockquote { border-left-color: #4b5563; color: #d1d5db; }
        .dark .ql-editor.prose code { background-color: #374151; color: #e5e7eb; }
        .dark .ql-editor.prose pre { background-color: #1f2937; color: #e5e7eb; }
    </style>

    <script>
        function confirmDelete() {
            const modal = document.getElementById('deleteModal');
            const content = document.getElementById('deleteModalContent');
            modal.classList.remove('hidden');
            
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