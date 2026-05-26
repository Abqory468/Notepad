<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight uppercase text-sm">
            Create New Note
        </h2>
    </x-slot>

    <div>
        
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] card-shadow border border-gray-50 dark:border-gray-700 overflow-hidden">
                <form action="{{ route('notes.store') }}" method="POST" class="p-8 sm:p-12" id="note-form">
                    @csrf
                    
                    <div class="space-y-6">
                        <div>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                class="w-full bg-transparent text-3xl font-bold text-gray-900 dark:text-white placeholder-gray-300 dark:placeholder-gray-600 border-none focus:ring-0 px-0 pb-2"
                                placeholder="Note Title" required maxlength="255">
                            @error('title')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="mt-6">
    <div class="border border-gray-100 dark:border-gray-700 rounded-2xl overflow-hidden bg-gray-50/50 dark:bg-gray-900/50">
        <div id="editor" class="bg-white dark:bg-gray-800 min-h-[300px] text-gray-700 dark:text-gray-300 text-base border-none">{!! old('content') !!}</div>
    </div>
    <input type="hidden" name="content" id="content-input">
</div>
                            @error('content')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-4 pt-8">
                            <a href="{{ route('notes.index') }}" class="px-8 py-3 bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="px-8 py-3 bg-yellow-500 text-white font-semibold rounded-xl hover:bg-yellow-600 transition shadow-lg shadow-yellow-200 dark:shadow-none">
                                Save Note
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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