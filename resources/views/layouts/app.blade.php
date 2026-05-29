<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'MyNotes') }}</title>

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
            
            /* Transition untuk sidebar */
            .sidebar-transition {
                transition: transform 0.3s ease-in-out, margin-left 0.3s ease-in-out;
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
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 bg-slate-50 dark:bg-gray-900 min-h-screen flex transition-colors duration-200" 
          x-data="{ 
              sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' ? true : (window.innerWidth > 1024),
              createFolderModal: false, 
              renameFolderModal: false,
              deleteFolderModal: false,
              activeFolderId: null,
              activeFolderName: '',
              toggleSidebar() {
                  this.sidebarOpen = !this.sidebarOpen;
                  localStorage.setItem('sidebarOpen', this.sidebarOpen);
              },
              toggleTheme() {
                  if (document.documentElement.classList.contains('dark')) {
                      document.documentElement.classList.remove('dark');
                      localStorage.theme = 'light';
                  } else {
                      document.documentElement.classList.add('dark');
                      localStorage.theme = 'dark';
                  }
              } 
          }"
          @resize.window="if(window.innerWidth < 768) sidebarOpen = false"
          @close-folder-modal.window="createFolderModal = false"
          @open-rename-folder.window="renameFolderModal = true; activeFolderId = $event.detail.id; activeFolderName = $event.detail.name;"
          @open-delete-folder.window="deleteFolderModal = true; activeFolderId = $event.detail.id; activeFolderName = $event.detail.name;">
        
        <div class="w-full min-h-screen flex overflow-hidden">
            
            <!-- Overlay untuk mobile -->
            <div x-show="sidebarOpen && window.innerWidth < 1024" 
                 x-transition.opacity.duration.300ms
                 @click="sidebarOpen = false"
                 class="fixed inset-0 bg-black/50 z-30 lg:hidden"
                 style="display: none;"></div>

            <!-- Sidebar -->
            <aside
                class="fixed lg:sticky inset-y-0 left-0 lg:inset-auto lg:top-0 lg:h-screen z-40 lg:z-auto w-64 flex-shrink-0 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 flex flex-col sidebar-transition shadow-xl lg:shadow-none"
                :class="{
                    'translate-x-0': sidebarOpen,
                    '-translate-x-full': !sidebarOpen,
                    'lg:translate-x-0 lg:-ml-64': !sidebarOpen
                }">
                
                <!-- Sidebar Header -->
                <div class="p-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center text-white font-bold text-sm shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                              <path d="M12.613 1.258a1.535 1.535 0 0 1 2.13 2.129l-1.905 2.856a8 8 0 0 1-3.56 2.939 4.011 4.011 0 0 0-2.46-2.46 8 8 0 0 1 2.94-3.56l2.855-1.904ZM5.5 8A2.5 2.5 0 0 0 3 10.5a.5.5 0 0 1-.7.459.75.75 0 0 0-.983 1A3.5 3.5 0 0 0 8 10.5 2.5 2.5 0 0 0 5.5 8Z" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold text-gray-800 dark:text-white">MyNotes</span>

                    </div>
                    
                    <!-- Close button di sidebar (mobile) -->
                    <button @click="toggleSidebar()" 
                            class="lg:hidden text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto custom-scrollbar p-4">
                    <!-- Navigation -->
                    <nav class="space-y-1">
                        <a href="{{ route('notes.index') }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('notes.index') && !request('folder_id') ? 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            <span class="font-medium text-sm">All Notes</span>
                        </a>
                        
                        <a href="{{ route('notes.create', ['folder_id' => request('folder_id')]) }}" 
                           class="flex items-center space-x-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->routeIs('notes.create') ? 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="font-medium text-sm">New Note</span>
                        </a>
                    </nav>
                    
                    <!-- Folders Section -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between mb-3 px-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400">Folders</h3>
                            <button @click="createFolderModal = true" class="text-gray-400 hover:text-yellow-500 transition p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <ul class="space-y-1" id="sidebar-folder-list" 
                            x-data="{ 
                                folders: {{ Auth::user()->folders()->orderBy('name')->get()->toJson() }},
                                activeFolderId: {{ request('folder_id') ?: 'null' }}
                            }"
                            @add-folder.window="folders = [...folders, $event.detail].sort((a,b) => a.name.localeCompare(b.name));">
                            <template x-for="folder in folders" :key="folder.id">
                                <li>
                                    <a :href="'/notes?folder_id=' + folder.id" 
                                       class="flex items-center justify-between group px-3 py-2 rounded-lg transition-all duration-200"
                                       :class="activeFolderId == folder.id ? 'text-yellow-600 bg-yellow-50 dark:bg-yellow-900/20 dark:text-yellow-400' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                        <div class="flex items-center space-x-2 overflow-hidden flex-1">
                                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                            </svg>
                                            <span class="text-sm font-medium truncate" x-text="folder.name"></span>
                                        </div>
                                        
                                        <div x-data="{ open: false }" class="relative flex-shrink-0">
                                            <button @click.prevent.stop="open = !open" 
                                                    class="opacity-0 group-hover:opacity-100 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded transition duration-150">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                </svg>
                                            </button>
                                            <div x-show="open" @click.outside="open = false" x-transition 
                                                 class="absolute right-0 mt-1 w-28 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg py-1 text-xs text-left z-30" 
                                                 style="display: none;">
                                                <button @click.prevent.stop="open = false; $dispatch('open-rename-folder', { id: folder.id, name: folder.name })" 
                                                        class="w-full text-left px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 transition">
                                                    Rename
                                                </button>
                                                <button @click.prevent.stop="open = false; $dispatch('open-delete-folder', { id: folder.id, name: folder.name })" 
                                                        class="w-full text-left px-3 py-1.5 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-900/30 text-red-500 transition">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>

                <!-- Sidebar Footer -->
                <div class="p-4 border-t border-gray-100 dark:border-gray-800 space-y-2">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full flex items-center space-x-3 px-3 py-2.5 rounded-xl text-gray-500 hover:bg-red-50 hover:text-red-500 dark:hover:bg-red-900/30 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="font-medium text-sm">Logout</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 flex flex-col bg-slate-50 dark:bg-gray-900 overflow-hidden relative min-w-0">
                
                <!-- Top Header -->
                <header class="h-16 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4 sm:px-6 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md sticky top-0 z-20">
                    <div class="flex items-center space-x-3">
                        <!-- Tombol Toggle Sidebar (Desktop & Mobile) -->
                        <button @click="toggleSidebar()" 
                                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200 focus:outline-none"
                                aria-label="Toggle sidebar">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                        </button>
                        
                        <!-- Search Bar area -->
                        <div class="flex-1 flex items-center">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>
                    
                    <!-- User Profile & Theme Toggle -->
                    <div class="flex items-center space-x-3">
                        <!-- Theme Toggle Button -->
                        <button @click="toggleTheme()" 
                                class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200">
                            <!-- Sun Icon -->
                            <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <!-- Moon Icon -->
                            <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                            </svg>
                        </button>

                        <div class="text-right hidden sm:block">
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ Auth::user()->name }}</div>
                            <div class="text-xs text-gray-400">User</div>
                        </div>
                        
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-500 flex items-center justify-center text-white font-bold shadow-md">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 custom-scrollbar">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Create Folder Modal -->
        <div x-show="createFolderModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="createFolderModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" @click="createFolderModal = false"></div>

                <div x-show="createFolderModal" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Create New Folder</h3>
                    <form action="{{ route('folders.store') }}" method="POST" id="create-folder-form">
                        @csrf
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Folder Name</label>
                            <input type="text" name="name" id="name" required class="w-full rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:border-yellow-500 focus:ring-yellow-500 text-gray-700 dark:text-white px-4 py-2.5 transition-colors" placeholder="e.g. Work, Ideas">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="createFolderModal = false" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition shadow-md">Create Folder</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Rename Folder Modal -->
        <div x-show="renameFolderModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="renameFolderModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" @click="renameFolderModal = false"></div>

                <div x-show="renameFolderModal" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle border border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Rename Folder</h3>
                    <form :action="'/folders/' + activeFolderId" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-6">
                            <label for="rename_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Folder Name</label>
                            <input type="text" name="name" id="rename_name" x-model="activeFolderName" required class="w-full rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 focus:border-yellow-500 focus:ring-yellow-500 text-gray-700 dark:text-white px-4 py-2.5 transition-colors">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="renameFolderModal = false" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 transition shadow-md">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Delete Folder Modal -->
        <div x-show="deleteFolderModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <div x-show="deleteFolderModal" x-transition.opacity class="fixed inset-0 transition-opacity bg-gray-900/50 backdrop-blur-sm" @click="deleteFolderModal = false"></div>

                <div x-show="deleteFolderModal" x-transition class="relative inline-block w-full max-w-md p-6 overflow-hidden text-left align-bottom transition-all transform bg-white dark:bg-gray-800 rounded-2xl shadow-xl sm:my-8 sm:align-middle border border-gray-100 dark:border-gray-700">
                    <div class="text-center">
                        <div class="w-14 h-14 rounded-full bg-red-50 dark:bg-red-900/30 text-red-500 mx-auto flex items-center justify-center mb-4">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Delete Folder?</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Are you sure you want to delete <span class="font-semibold text-gray-800 dark:text-gray-200" x-text="activeFolderName"></span>?
                            Notes in this folder will not be deleted, but will become uncategorized.
                        </p>
                        
                        <form :action="'/folders/' + activeFolderId" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="flex space-x-3">
                                <button type="button" @click="deleteFolderModal = false" class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition">Cancel</button>
                                <button type="submit" class="flex-1 px-4 py-2 bg-red-500 text-white font-medium rounded-lg hover:bg-red-600 transition shadow-md">Delete Folder</button>
                            </div>
                        </form>
                    </div>
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
                                    
                                    window.dispatchEvent(new CustomEvent('add-folder', { detail: folder }));

                                    const folderSelect = document.getElementById('folder_id');
                                    if (folderSelect) {
                                        const option = document.createElement('option');
                                        option.value = folder.id;
                                        option.textContent = folder.name;
                                        option.selected = true;
                                        folderSelect.appendChild(option);
                                    }

                                    nameInput.value = '';
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