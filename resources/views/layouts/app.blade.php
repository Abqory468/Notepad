<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Notepad-X') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        <!-- Quill CSS -->
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Alpine plugins (for transition if needed) -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            body { font-family: 'Inter', sans-serif; }
            .soft-shadow { box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08); }
            .card-shadow { box-shadow: 0 4px 15px -5px rgba(0,0,0,0.05); }
            
            /* Dark mode scrollbar */
            .dark .custom-scrollbar::-webkit-scrollbar-thumb {
                background-color: #4B5563; 
            }
        </style>
        
        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark')
            } else {
                document.documentElement.classList.remove('dark')
            }
        </script>
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-[#FAFBFF] dark:bg-gray-900 min-h-screen flex transition-colors duration-200" 
          x-data="{ sidebarOpen: window.innerWidth > 768, createFolderModal: false, toggleTheme() {
              if (document.documentElement.classList.contains('dark')) {
                  document.documentElement.classList.remove('dark');
                  localStorage.theme = 'light';
              } else {
                  document.documentElement.classList.add('dark');
                  localStorage.theme = 'dark';
              }
          } }"
          @resize.window="sidebarOpen = window.innerWidth > 768"
          @close-folder-modal.window="createFolderModal = false">
        
        <div class="w-full min-h-screen flex overflow-hidden">
            
            <!-- Sidebar -->
            <aside 
                class="fixed inset-y-0 left-0 z-40 w-64 border-r border-gray-100 dark:border-gray-800 flex flex-col justify-between p-6 bg-white dark:bg-gray-900 transition-transform duration-300 md:relative md:translate-x-0"
                :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen, 'hidden md:flex': !sidebarOpen}">
                
                <!-- Close Button (Mobile) -->
                <button @click="sidebarOpen = false" class="md:hidden absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div>
                    <div class="flex items-center space-x-3 mb-10 px-2">
                        <div class="w-8 h-8 rounded-full bg-yellow-400 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-yellow-200 dark:shadow-none">N</div>
                        <span class="text-lg font-bold text-gray-800 dark:text-white">Notepad-X</span>
                    </div>

                    <nav class="space-y-2">
                        <a href="{{ route('notes.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('notes.index') && !request('folder_id') ? 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            <span class="font-medium">All Notes</span>
                        </a>
                        <a href="{{ route('notes.create', ['folder_id' => request('folder_id')]) }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl {{ request()->routeIs('notes.create') ? 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }} transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            <span class="font-medium">New Note</span>
                        </a>
                    </nav>
                    
                    <!-- Folders Section -->
                    <div class="mt-8 px-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Folders</h3>
                            <button @click="createFolderModal = true" class="text-gray-400 hover:text-yellow-500 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>
                        <ul class="space-y-1" id="sidebar-folder-list">
                            @foreach(Auth::user()->folders()->orderBy('name')->get() as $folder)
                                <li>
                                    <a href="{{ route('notes.index', ['folder_id' => $folder->id]) }}" 
                                       class="flex items-center justify-between group px-3 py-2 rounded-lg {{ request('folder_id') == $folder->id ? 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }} transition">
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                            <span class="text-sm font-medium">{{ $folder->name }}</span>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="space-y-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/30 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col bg-white dark:bg-gray-900 overflow-hidden relative transition-colors duration-200">
                
                <!-- Top Header -->
                <header class="h-20 border-b border-gray-50 dark:border-gray-800 flex items-center justify-between px-4 sm:px-8 bg-white/80 dark:bg-gray-900/80 backdrop-blur-md z-10">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 focus:outline-none rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                        
                        <!-- Search Bar area (slot for pages to add search if needed) -->
                        <div class="flex-1 flex items-center">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>
                    
                    <!-- User Profile & Theme Toggle -->
                    <div class="flex items-center space-x-4">
                        <button @click="toggleTheme()" class="p-2 text-gray-400 hover:text-yellow-500 transition rounded-full hover:bg-gray-50 dark:hover:bg-gray-800">
                            <!-- Moon Icon (shows in light mode) -->
                            <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            <!-- Sun Icon (shows in dark mode) -->
                            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </button>

                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-400">User</div>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/50 flex items-center justify-center text-yellow-600 font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-8 custom-scrollbar">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Create Folder Modal -->
        <div x-show="createFolderModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="createFolderModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" @click="createFolderModal = false"></div>

                <div x-show="createFolderModal" x-transition class="relative inline-block w-full max-w-md p-8 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Create New Folder</h3>
                    <form action="{{ route('folders.store') }}" method="POST" id="create-folder-form">
                        @csrf
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Folder Name</label>
                            <input type="text" name="name" id="name" required class="w-full rounded-xl bg-gray-50 dark:bg-gray-900 border-gray-100 dark:border-gray-700 focus:bg-white focus:border-yellow-500 focus:ring-yellow-500 text-gray-700 dark:text-white px-4 py-3 transition-colors" placeholder="e.g. Work, Ideas">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="createFolderModal = false" class="px-5 py-2.5 bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-xl hover:bg-gray-100 dark:hover:bg-gray-600 transition">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-yellow-500 text-white font-medium rounded-xl hover:bg-yellow-600 transition shadow-lg shadow-yellow-200 dark:shadow-none">Create Folder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quill JS -->
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const createFolderForm = document.getElementById('create-folder-form');
                if (createFolderForm) {
                    createFolderForm.addEventListener('submit', async (e) => {
                        e.preventDefault();
                        const form = e.target;
                        const nameInput = form.querySelector('#name');
                        const name = nameInput.value;
                        const token = form.querySelector('input[name="_token"]').value;

                        try {
                            const response = await fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': token
                                },
                                body: JSON.stringify({ name })
                            });

                            if (response.ok) {
                                const data = await response.json();
                                if (data.success && data.folder) {
                                    const folder = data.folder;
                                    
                                    // 1. Add to sidebar folder list
                                    const folderList = document.getElementById('sidebar-folder-list');
                                    if (folderList) {
                                        const li = document.createElement('li');
                                        li.innerHTML = `
                                            <a href="/notes?folder_id=${folder.id}" 
                                               class="flex items-center justify-between group px-3 py-2 rounded-lg text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                                    <span class="text-sm font-medium">${folder.name}</span>
                                                </div>
                                            </a>
                                        `;
                                        folderList.appendChild(li);
                                    }

                                    // 2. Add to folder_id dropdown if it exists on the page
                                    const folderSelect = document.getElementById('folder_id');
                                    if (folderSelect) {
                                        const option = document.createElement('option');
                                        option.value = folder.id;
                                        option.textContent = folder.name;
                                        option.selected = true;
                                        folderSelect.appendChild(option);
                                    }

                                    // 3. Clear input
                                    nameInput.value = '';

                                    // 4. Close modal
                                    window.dispatchEvent(new CustomEvent('close-folder-modal'));
                                }
                            } else {
                                const errors = await response.json();
                                alert(errors.message || 'Error creating folder');
                            }
                        } catch (err) {
                            console.error(err);
                            alert('An error occurred while creating the folder.');
                        }
                    });
                }
            });
        </script>
    </body>
</html>
