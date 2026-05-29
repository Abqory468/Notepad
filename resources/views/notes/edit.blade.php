<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3 text-gray-800 dark:text-white text-sm">
            <a href="{{ route('notes.index') }}" class="flex items-center gap-2 hover:text-gray-600 dark:hover:text-gray-300 transition">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-6">
                    <path fill-rule="evenodd" d="M7.72 12.53a.75.75 0 0 1 0-1.06l7.5-7.5a.75.75 0 1 1 1.06 1.06L9.31 12l6.97 6.97a.75.75 0 1 1-1.06 1.06l-7.5-7.5Z" clip-rule="evenodd" />
                </svg>
                <span class="text-xl font-bold text-gray-900 dark:text-white">{{ old('title', $note->title) }}</span>
            </a>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded-[2rem] card-shadow border border-gray-50 dark:border-gray-700 overflow-hidden">
    <form action="{{ route('notes.update', $note) }}" method="POST" id="note-form" class="p-8 sm:p-12">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <input type="text" name="title" id="title" value="{{ old('title', $note->title) }}" 
                    class="w-full bg-transparent text-3xl font-bold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-600 border-none focus:ring-0 px-0 pb-2"
                    placeholder="Note Title" required maxlength="255">
                @error('title')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <!-- Folder Selection -->
                <div class="mt-4">
                    <select name="folder_id" id="folder_id" class="w-full sm:w-1/3 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:border-yellow-500 focus:ring-yellow-500 text-gray-700 dark:text-white px-4 py-2.5 transition-colors">
                        <option value="">No Folder (Uncategorized)</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}" {{ old('folder_id', $note->folder_id) == $folder->id ? 'selected' : '' }}>
                                {{ $folder->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('folder_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

            <div>
                <div class="border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden bg-gray-50/50 dark:bg-gray-900/50">
                    <div id="editor" class="bg-white dark:bg-gray-800 min-h-[300px] text-gray-700 dark:text-gray-300 text-base border-none">{!! old('content', $note->content) !!}</div>
                </div>
                <input type="hidden" name="content" id="content-input">
                @error('content')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-between items-center pt-8">
                <div class="text-xs text-gray-400 dark:text-gray-500">
                    Last updated: {{ $note->updated_at->format('M d, Y h:i A') }}
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('notes.index') }}" class="px-8 py-3 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 bg-yellow-500 text-white font-semibold rounded-xl hover:bg-yellow-600 transition shadow-lg shadow-yellow-200 dark:shadow-none">
                        Update Note
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

    <!-- Override Quill toolbar for dark mode if needed -->
    <style>
        .dark .ql-toolbar {
            background-color: #1f2937;
            border-color: #374151 !important;
        }
        .dark .ql-toolbar .ql-stroke {
            stroke: #d1d5db;
        }
        .dark .ql-toolbar .ql-fill {
            fill: #d1d5db;
        }
        .dark .ql-toolbar .ql-picker {
            color: #d1d5db;
        }
        .dark .ql-container {
            border-color: #374151 !important;
        }
        .dark .ql-editor.ql-blank::before {
            color: #9ca3af;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill('#editor', {
                theme: 'snow',
                placeholder: 'Start writing your brilliant ideas here...',
                modules: {
                    toolbar: [
                        [{ 'font': [] }, { 'size': ['small', false, 'large', 'huge'] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        ['link', 'blockquote', 'code-block'],
                        ['clean']
                    ]
                }
            });

            var form = document.getElementById('note-form');
            form.onsubmit = function() {
                var contentInput = document.getElementById('content-input');
                contentInput.value = quill.root.innerHTML;
            };
        });
    </script>
</x-app-layout>