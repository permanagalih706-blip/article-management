/**
 * GitHub Pages Client-side Simulator for Article Management
 * Manages dynamic rendering, search, sort, and CRUD utilizing localStorage
 */

(function () {
    // 1. Mock Data Setup
    const DEFAULT_USERS = [
        { id: 1, name: 'Galih Admin', email: 'galih@example.com', role: 'admin', profile_photo: null },
        { id: 2, name: 'Budi Penulis', email: 'budi@example.com', role: 'user', profile_photo: null }
    ];

    const DEFAULT_ARTICLES = [
        {
            id: 1,
            title: 'Membangun Karir sebagai Web Developer',
            content: "Menjadi web developer di era digital ini merupakan salah satu pilihan karir yang sangat menjanjikan. Dengan perkembangan teknologi yang sangat pesat, permintaan akan website dan aplikasi web terus meningkat setiap harinya.\n\nUntuk memulai karir ini, Anda perlu menguasai beberapa dasar penting:\n1. HTML untuk struktur halaman web\n2. CSS untuk memperindah tampilan (styling)\n3. JavaScript untuk logika dan interaksi dinamis\n\nSetelah menguasai dasar-dasar tersebut, Anda bisa mulai mempelajari framework modern seperti Laravel untuk backend, serta Tailwind CSS untuk styling cepat.",
            status: 'published',
            user_id: 2,
            created_at: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString(),
            updated_at: new Date(Date.now() - 2 * 24 * 60 * 60 * 1000).toISOString()
        },
        {
            id: 2,
            title: 'Tips Produktivitas Bekerja dari Rumah (WFH)',
            content: "Bekerja dari rumah (Work From Home) memberikan fleksibilitas tinggi, tetapi juga menyimpan tantangan produktivitas tersendiri. Banyak gangguan yang dapat memecah fokus Anda.\n\nBerikut beberapa tips agar tetap produktif:\n- Buat ruang kerja khusus yang nyaman dan tenang.\n- Tetapkan jadwal kerja yang jelas dan patuhi jam tersebut.\n- Buat daftar tugas (to-do list) setiap pagi.\n- Ambil istirahat pendek secara berkala untuk menyegarkan pikiran.\n\nDengan disiplin diri yang baik, WFH bisa menjadi sangat efisien dan menyenangkan.",
            status: 'published',
            user_id: 1,
            created_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString(),
            updated_at: new Date(Date.now() - 24 * 60 * 60 * 1000).toISOString()
        },
        {
            id: 3,
            title: 'Konsep Draft Artikel Baru',
            content: "Ini adalah konten dari artikel draft yang belum dipublikasikan ke publik. Konsep ini hanya dapat dilihat oleh penulis di menu Drafts.",
            status: 'draft',
            user_id: 1,
            created_at: new Date().toISOString(),
            updated_at: new Date().toISOString()
        }
    ];

    // Local Storage Helpers
    function getDB(key, defaults) {
        const val = localStorage.getItem(key);
        if (!val) {
            localStorage.setItem(key, JSON.stringify(defaults));
            return defaults;
        }
        return JSON.parse(val);
    }

    function saveDB(key, data) {
        localStorage.setItem(key, JSON.stringify(data));
    }

    // Initialize DB tables
    let users = getDB('static_users', DEFAULT_USERS);
    let articles = getDB('static_articles', DEFAULT_ARTICLES);
    let currentUser = JSON.parse(localStorage.getItem('static_auth_user')) || DEFAULT_USERS[0]; // Default logged in as Galih Admin

    // 2. Identify Current Page
    const pageName = (() => {
        const href = window.location.href.toLowerCase();
        if (href.includes('/articles/create.html')) return 'articles-create';
        if (href.includes('/articles/edit.html')) return 'articles-edit';
        if (href.includes('/articles/show.html')) return 'articles-show';
        if (href.includes('/users/index.html')) return 'users-index';
        if (href.includes('/users/create.html')) return 'users-create';
        if (href.includes('/users/edit.html')) return 'users-edit';
        if (href.includes('/dashboard.html')) return 'dashboard';
        if (href.includes('/drafts.html')) return 'drafts';
        if (href.includes('/profile.html')) return 'profile';
        if (href.includes('/login.html')) return 'login';
        if (href.includes('/register.html')) return 'register';
        if (href.endsWith('/index.html') || href.endsWith('/article-management/') || href.endsWith('/')) return 'welcome';
        
        // Path fallback
        const path = window.location.pathname.toLowerCase();
        if (path.endsWith('create.html') && path.includes('/articles/')) return 'articles-create';
        if (path.endsWith('edit.html') && path.includes('/articles/')) return 'articles-edit';
        if (path.endsWith('show.html') && path.includes('/articles/')) return 'articles-show';
        if (path.endsWith('index.html') && path.includes('/users/')) return 'users-index';
        if (path.endsWith('create.html') && path.includes('/users/')) return 'users-create';
        if (path.endsWith('edit.html') && path.includes('/users/')) return 'users-edit';
        if (path.endsWith('dashboard.html')) return 'dashboard';
        if (path.endsWith('drafts.html')) return 'drafts';
        if (path.endsWith('about.html')) return 'about';
        if (path.endsWith('contact.html')) return 'contact';
        if (path.endsWith('profile.html')) return 'profile';
        if (path.endsWith('login.html')) return 'login';
        if (path.endsWith('register.html')) return 'register';
        
        return 'welcome';
    })();

    const isSubdir = ['articles-create', 'articles-edit', 'articles-show', 'users-index', 'users-create', 'users-edit'].includes(pageName);
    const prefix = isSubdir ? '../' : './';

    // 3. Auth Check and Redirects
    const authRequired = ['dashboard', 'drafts', 'profile', 'articles-create', 'articles-edit', 'users-index', 'users-create', 'users-edit'];
    if (authRequired.includes(pageName) && !currentUser) {
        window.location.href = prefix + 'login.html';
        return;
    }

    // 4. Update Header/Navbar globally
    document.addEventListener('DOMContentLoaded', function () {
        updateNavbar();
        runPageLogic();
    });

    function updateNavbar() {
        const nav = document.getElementById('static-navbar');
        if (!nav) return;

        // Find Desktop Menu items container
        const desktopMenu = nav.querySelector('.hidden.md\\:block .flex-baseline');
        if (desktopMenu) {
            // Re-render navigation based on login status & role
            let menuHTML = `<a href="${prefix}dashboard.html" class="px-4 py-2 rounded-md text-sm font-medium ${['dashboard','welcome'].includes(pageName) ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white'} transition-colors">Blog</a>`;
            
            if (currentUser) {
                menuHTML += `<a href="${prefix}drafts.html" class="px-4 py-2 rounded-md text-sm font-medium ${pageName === 'drafts' ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white'} transition-colors">Drafts</a>`;
                
                if (currentUser.role === 'admin') {
                    menuHTML += `<a href="${prefix}users/index.html" class="px-4 py-2 rounded-md text-sm font-medium ${pageName.startsWith('users') ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white'} transition-colors">Users</a>`;
                }
            }
            
            menuHTML += `<a href="${prefix}about.html" class="px-4 py-2 rounded-md text-sm font-medium ${pageName === 'about' ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white'} transition-colors">About</a>`;
            menuHTML += `<a href="${prefix}contact.html" class="px-4 py-2 rounded-md text-sm font-medium ${pageName === 'contact' ? 'bg-slate-800 text-white' : 'text-slate-300 hover:bg-slate-700 hover:text-white'} transition-colors">Contact</a>`;
            
            desktopMenu.innerHTML = menuHTML;
        }

        // Find Profile section in Nav
        const profileSection = nav.querySelector('.hidden.md\\:flex.items-center');
        if (profileSection) {
            if (currentUser) {
                const avatarUrl = currentUser.profile_photo 
                    ? currentUser.profile_photo 
                    : `https://ui-avatars.com/api/?name=${encodeURIComponent(currentUser.name)}&background=6366f1&color=fff&size=36`;
                
                profileSection.innerHTML = `
                    <div class="flex items-center gap-3">
                        <a href="${prefix}profile.html" class="text-sm text-slate-300 hover:text-white transition-colors">${currentUser.name}</a>
                        <img class="h-9 w-9 rounded-full object-cover ring-2 ring-slate-700" src="${avatarUrl}" alt="User Avatar">
                    </div>
                    <button id="static-logout-btn" class="px-3 py-1.5 text-sm font-medium text-white bg-red-500/20 hover:bg-red-500/40 rounded border border-red-500/30 transition-colors ml-4">
                        Logout
                    </button>
                `;

                document.getElementById('static-logout-btn').addEventListener('click', function () {
                    localStorage.removeItem('static_auth_user');
                    window.location.href = prefix + 'index.html';
                });
            } else {
                profileSection.innerHTML = `
                    <a href="${prefix}login.html" class="px-4 py-2 text-sm font-medium text-white hover:text-slate-300 transition-colors">Login</a>
                `;
            }
        }
    }

    // 5. Page-Specific Logic
    function runPageLogic() {
        switch (pageName) {
            case 'welcome':
                handleWelcomePage();
                break;
            case 'login':
                handleLoginPage();
                break;
            case 'register':
                handleRegisterPage();
                break;
            case 'dashboard':
                handleDashboardPage(false);
                break;
            case 'drafts':
                handleDashboardPage(true);
                break;
            case 'profile':
                handleProfilePage();
                break;
            case 'articles-show':
                handleArticleShowPage();
                break;
            case 'articles-create':
                handleArticleCreatePage();
                break;
            case 'articles-edit':
                handleArticleEditPage();
                break;
            case 'users-index':
                handleUsersIndexPage();
                break;
            case 'users-create':
                handleUserCreatePage();
                break;
            case 'users-edit':
                handleUserEditPage();
                break;
        }
    }

    // Welcome Page
    function handleWelcomePage() {
        // Welcome auth header (Log in / Register vs Dashboard links)
        const welcomeAuth = document.getElementById('static-welcome-auth') || document.querySelector('.sm\\:fixed.sm\\:top-0.sm\\:right-0');
        if (welcomeAuth) {
            if (currentUser) {
                welcomeAuth.innerHTML = `
                    <a href="${prefix}dashboard.html" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Dashboard</a>
                `;
            } else {
                welcomeAuth.innerHTML = `
                    <a href="${prefix}login.html" class="font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Log in</a>
                    <a href="${prefix}register.html" class="ml-4 font-semibold text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white focus:outline focus:outline-2 focus:rounded-sm focus:outline-red-500">Register</a>
                `;
            }
        }
    }

    // Login Page
    function handleLoginPage() {
        const form = document.querySelector('form');
        if (!form) return;

        form.removeAttribute('action');
        form.removeAttribute('method');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;

            const user = users.find(u => u.email.toLowerCase() === email.toLowerCase());
            if (user) {
                // Mock login (accepting any password for simplicity in static demo)
                localStorage.setItem('static_auth_user', JSON.stringify(user));
                window.location.href = prefix + 'dashboard.html';
            } else {
                alert('Email atau password salah! (Gunakan email user yang terdaftar, contoh: galih@example.com)');
            }
        });
    }

    // Register Page
    function handleRegisterPage() {
        const form = document.querySelector('form');
        if (!form) return;

        form.removeAttribute('action');
        form.removeAttribute('method');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;

            if (password !== passwordConfirm) {
                alert('Konfirmasi password tidak cocok!');
                return;
            }

            const exists = users.find(u => u.email.toLowerCase() === email.toLowerCase());
            if (exists) {
                alert('Email sudah digunakan!');
                return;
            }

            const newUser = {
                id: users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1,
                name: name,
                email: email,
                role: 'user',
                profile_photo: null
            };

            users.push(newUser);
            saveDB('static_users', users);

            localStorage.setItem('static_auth_user', JSON.stringify(newUser));
            window.location.href = prefix + 'dashboard.html';
        });
    }

    // Dashboard & Drafts Pages
    function handleDashboardPage(isDraftsOnly) {
        // Intercept search form
        const searchForm = document.querySelector('form');
        if (searchForm) {
            searchForm.removeAttribute('action');
            searchForm.removeAttribute('method');
            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const search = searchForm.querySelector('[name="search"]').value.trim();
                const authorSelect = searchForm.querySelector('[name="author"]');
                const author = authorSelect ? authorSelect.value : '';
                const sortSelect = searchForm.querySelector('[name="sort"]');
                const sort = sortSelect ? sortSelect.value : 'latest_created';

                const url = new URL(window.location.href);
                url.searchParams.set('search', search);
                if (author) url.searchParams.set('author', author);
                else url.searchParams.delete('author');
                url.searchParams.set('sort', sort);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            });
        }

        // Parse search, author, sort, and page parameters
        const urlParams = new URLSearchParams(window.location.search);
        const searchVal = urlParams.get('search') || '';
        const authorVal = urlParams.get('author') || '';
        const sortVal = urlParams.get('sort') || 'latest_created';
        const pageVal = parseInt(urlParams.get('page')) || 1;

        // Render search fields in inputs to match current state
        if (searchForm) {
            const inputSearch = searchForm.querySelector('[name="search"]');
            if (inputSearch) inputSearch.value = searchVal;

            const selectAuthor = searchForm.querySelector('[name="author"]');
            if (selectAuthor) {
                // Dynamically build option list of authors
                let options = '<option value="">All Author</option>';
                users.forEach(u => {
                    options += `<option value="${u.id}" ${authorVal == u.id ? 'selected' : ''}>${u.name}</option>`;
                });
                selectAuthor.innerHTML = options;
            }

            const selectSort = searchForm.querySelector('[name="sort"]');
            if (selectSort) selectSort.value = sortVal;
        }

        // Filter articles
        let filtered = articles.filter(art => {
            if (isDraftsOnly) {
                // Drafts page: only show current logged in user's drafts
                return art.status === 'draft' && art.user_id === currentUser.id;
            } else {
                // Blog/Dashboard: show all published articles, plus current user's drafts
                return art.status === 'published' || art.user_id === currentUser.id;
            }
        });

        // Apply filters
        if (searchVal) {
            const searchLower = searchVal.toLowerCase();
            filtered = filtered.filter(art => 
                art.title.toLowerCase().includes(searchLower) || 
                art.content.toLowerCase().includes(searchLower)
            );
        }

        if (authorVal) {
            filtered = filtered.filter(art => art.user_id == authorVal);
        }

        // Apply sorting
        filtered.sort((a, b) => {
            if (sortVal === 'latest_created') {
                return new Date(b.created_at) - new Date(a.created_at);
            } else if (sortVal === 'latest_updated') {
                return new Date(b.updated_at) - new Date(a.updated_at);
            } else if (sortVal === 'title_asc') {
                return a.title.localeCompare(b.title);
            } else if (sortVal === 'title_desc') {
                return b.title.localeCompare(a.title);
            }
            return new Date(b.created_at) - new Date(a.created_at);
        });

        // Paginate (6 per page)
        const itemsPerPage = 6;
        const totalItems = filtered.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
        const currentPage = Math.min(pageVal, totalPages);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginated = filtered.slice(startIndex, startIndex + itemsPerPage);

        // Update pagination counter info
        const infoDiv = document.querySelector('.text-slate-500.mb-4');
        if (infoDiv) {
            if (totalItems > 0) {
                const start = startIndex + 1;
                const end = Math.min(startIndex + itemsPerPage, totalItems);
                infoDiv.innerHTML = `Showing <strong>${start}</strong> to <strong>${end}</strong> of <strong>${totalItems}</strong> results`;
            } else {
                infoDiv.innerHTML = 'Showing 0 results';
            }
        }

        // Render articles grid
        const grid = document.getElementById('static-articles-grid') || document.querySelector('.grid.grid-cols-1');
        if (grid) {
            if (paginated.length > 0) {
                let gridHTML = '';
                paginated.forEach(art => {
                    const author = users.find(u => u.id === art.user_id) || { name: 'Unknown' };
                    const isOwnerOrAdmin = currentUser && (currentUser.role === 'admin' || currentUser.id === art.user_id);
                    const excerpt = art.content.replace(/<[^>]*>/g, '').substring(0, 120) + (art.content.length > 120 ? '...' : '');
                    
                    const avatarColor = ['#6366f1','#0ea5e9','#22c55e','#f59e0b','#ef4444'][art.user_id % 5];
                    const initials = author.name.charAt(0).toUpperCase();

                    gridHTML += `
                        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-lg transition-shadow duration-300 flex flex-col h-full relative group">
                            
                            ${isOwnerOrAdmin ? `
                            <div class="absolute top-4 right-4 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity z-20">
                                <a href="${prefix}articles/edit.html?id=${art.id}" class="p-1.5 bg-yellow-50 text-yellow-500 rounded-md hover:bg-yellow-500 hover:text-white transition-colors" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                </a>
                                <button data-delete-id="${art.id}" class="p-1.5 bg-red-50 text-red-500 rounded-md hover:bg-red-500 hover:text-white transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            ` : ''}

                            <div class="flex items-center justify-between mb-4">
                                <div class="flex gap-2">
                                    ${art.status === 'published' 
                                        ? `<span class="bg-emerald-50 text-emerald-600 text-xs font-semibold px-3 py-1 rounded-full border border-emerald-200">Published</span>`
                                        : `<span class="bg-amber-50 text-amber-600 text-xs font-semibold px-3 py-1 rounded-full border border-amber-200">Draft</span>`
                                    }
                                </div>
                                <span class="text-sm text-slate-500">${timeAgo(art.updated_at)}</span>
                            </div>

                            <h2 class="text-xl font-bold text-slate-900 mb-3 line-clamp-2 leading-snug">${escapeHtml(art.title)}</h2>

                            <p class="text-slate-500 mb-6 line-clamp-3 text-sm leading-relaxed flex-1">${escapeHtml(excerpt)}</p>

                            <div class="flex items-center justify-between mt-auto pt-4 border-t border-slate-50 gap-2">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="flex items-center justify-center w-8 h-8 rounded-full text-xs font-bold text-white shadow-sm shrink-0" style="background: ${avatarColor};">
                                        ${initials}
                                    </div>
                                    <span class="text-sm font-medium text-slate-700 truncate">${escapeHtml(author.name)}</span>
                                </div>
                                
                                <div class="flex items-center gap-3 shrink-0">
                                    ${art.status === 'draft' && currentUser && currentUser.id === art.user_id ? `
                                        <button data-publish-id="${art.id}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                            Publish
                                        </button>
                                    ` : ''}
                                    <a href="${prefix}articles/show.html?id=${art.id}" class="text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1 group/link">
                                        Read
                                        <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                });
                grid.innerHTML = gridHTML;

                // Setup delete and publish click listeners
                grid.querySelectorAll('[data-delete-id]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = parseInt(this.getAttribute('data-delete-id'));
                        if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
                            articles = articles.filter(art => art.id !== id);
                            saveDB('static_articles', articles);
                            window.location.reload();
                        }
                    });
                });

                grid.querySelectorAll('[data-publish-id]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = parseInt(this.getAttribute('data-publish-id'));
                        const art = articles.find(a => a.id === id);
                        if (art) {
                            art.status = 'published';
                            art.updated_at = new Date().toISOString();
                            saveDB('static_articles', articles);
                            window.location.reload();
                        }
                    });
                });

            } else {
                grid.className = "col-span-full py-12 text-center bg-white rounded-2xl shadow-sm border border-slate-100";
                grid.innerHTML = `
                    <div class="flex flex-col items-center justify-center">
                        <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                        <p class="text-slate-500">Tidak ada artikel yang ditemukan.</p>
                        ${currentUser ? `
                            <a href="${prefix}articles/create.html" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">Buat Artikel Pertama</a>
                        ` : ''}
                    </div>
                `;
            }
        }

        // Render Pagination
        const paginationContainers = document.querySelectorAll('nav[role="navigation"]');
        paginationContainers.forEach(container => {
            if (totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let pagHTML = '<div class="flex gap-1">';
            for (let i = 1; i <= totalPages; i++) {
                const active = i === currentPage;
                const url = new URL(window.location.href);
                url.searchParams.set('page', i.toString());

                if (active) {
                    pagHTML += `<span class="px-3 py-1.5 text-sm font-bold text-white bg-blue-600 rounded-lg shadow-sm">${i}</span>`;
                } else {
                    pagHTML += `<a href="${url.toString()}" class="px-3 py-1.5 text-sm font-medium text-slate-700 hover:bg-slate-100 bg-white border border-slate-300 rounded-lg transition-colors">${i}</a>`;
                }
            }
            pagHTML += '</div>';
            container.innerHTML = pagHTML;
        });
    }

    // Article Show Page
    function handleArticleShowPage() {
        const urlParams = new URLSearchParams(window.location.search);
        const artId = parseInt(urlParams.get('id'));
        if (!artId) {
            window.location.href = prefix + 'dashboard.html';
            return;
        }

        const article = articles.find(a => a.id === artId);
        if (!article) {
            window.location.href = prefix + 'dashboard.html';
            return;
        }

        const author = users.find(u => u.id === article.user_id) || { name: 'Unknown' };

        // Check drafts authorization
        if (article.status === 'draft' && (!currentUser || currentUser.id !== article.user_id)) {
            alert('Akses ditolak: Artikel ini berupa draft.');
            window.location.href = prefix + 'dashboard.html';
            return;
        }

        // Update Title in Browser
        document.title = `${article.title} - Article App`;

        // Update Title & Header meta
        const titleSpan = document.querySelector('.flex.items-center.gap-2.text-slate-500.text-sm span');
        if (titleSpan) titleSpan.textContent = article.title;

        // Update Article container DOM
        const statusContainer = document.querySelector('.h-48.sm\\:h-64.bg-gradient-to-r');
        if (statusContainer) {
            const statusBadge = article.status === 'published'
                ? `<span class="bg-emerald-500/20 text-emerald-300 text-xs font-semibold px-3 py-1 rounded-full border border-emerald-500/30 uppercase tracking-wider">Published</span>`
                : `<span class="bg-amber-500/20 text-amber-300 text-xs font-semibold px-3 py-1 rounded-full border border-amber-500/30 uppercase tracking-wider">Draft</span>`;
            
            statusContainer.innerHTML = `
                <div class="absolute inset-0 bg-slate-950/20"></div>
                <div class="relative text-center">
                    ${statusBadge}
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white mt-4 tracking-tight leading-tight max-w-2xl mx-auto drop-shadow-sm">
                        ${escapeHtml(article.title)}
                    </h1>
                </div>
            `;
        }

        // Author metadata row
        const authorMeta = document.querySelector('.flex.items-center.justify-between.pb-6');
        if (authorMeta) {
            const avatarColor = ['#6366f1','#0ea5e9','#22c55e','#f59e0b','#ef4444'][article.user_id % 5];
            const initials = author.name.charAt(0).toUpperCase();
            const dateStr = new Date(article.updated_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });

            authorMeta.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full text-base font-bold text-white shadow-sm shrink-0" style="background: ${avatarColor};">
                        ${initials}
                    </div>
                    <div>
                        <span class="block text-sm font-semibold text-slate-900 leading-none">${escapeHtml(author.name)}</span>
                        <span class="text-xs text-slate-500 mt-1 block">Penulis Artikel</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="block text-sm text-slate-600 font-medium">${dateStr}</span>
                    <span class="text-xs text-slate-400 block mt-1">Terakhir Diupdate: ${timeAgo(article.updated_at)}</span>
                </div>
            `;
        }

        // Content
        const prose = document.querySelector('.prose');
        if (prose) {
            prose.innerHTML = escapeHtml(article.content).replace(/\n/g, '<br>');
        }

        // Render actions footer
        const isOwnerOrAdmin = currentUser && (currentUser.role === 'admin' || currentUser.id === article.user_id);
        const footerActions = document.querySelector('.mt-12.pt-6.border-t');
        if (footerActions) {
            if (isOwnerOrAdmin) {
                let actionsHTML = '';
                if (article.status === 'draft' && currentUser.id === article.user_id) {
                    actionsHTML += `
                        <button id="static-publish-btn" class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg transition-colors text-sm shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Terbitkan Artikel
                        </button>
                    `;
                }

                actionsHTML += `
                    <a href="${prefix}articles/edit.html?id=${article.id}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-yellow-50 hover:bg-yellow-100 text-yellow-700 font-semibold rounded-lg transition-colors border border-yellow-200 text-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Artikel
                    </a>
                    <button id="static-delete-btn" class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 hover:bg-red-100 text-red-700 font-semibold rounded-lg transition-colors border border-red-200 text-sm shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Hapus Artikel
                    </button>
                `;
                footerActions.innerHTML = actionsHTML;

                // Setup Action Listeners
                const pubBtn = document.getElementById('static-publish-btn');
                if (pubBtn) {
                    pubBtn.addEventListener('click', function () {
                        article.status = 'published';
                        article.updated_at = new Date().toISOString();
                        saveDB('static_articles', articles);
                        window.location.reload();
                    });
                }

                document.getElementById('static-delete-btn').addEventListener('click', function () {
                    if (confirm('Apakah Anda yakin ingin menghapus artikel ini?')) {
                        articles = articles.filter(a => a.id !== article.id);
                        saveDB('static_articles', articles);
                        window.location.href = prefix + 'dashboard.html';
                    }
                });
            } else {
                footerActions.remove();
            }
        }
    }

    // Article Create Page
    function handleArticleCreatePage() {
        const form = document.querySelector('form');
        if (!form) return;

        form.removeAttribute('action');
        form.removeAttribute('method');

        // Populate Author Select Option
        const userSelect = document.getElementById('user_id');
        if (userSelect) {
            let options = '';
            users.forEach(u => {
                options += `<option value="${u.id}" ${currentUser.id === u.id ? 'selected' : ''}>${u.name}</option>`;
            });
            userSelect.innerHTML = options;
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const title = document.getElementById('title').value.trim();
            const authorId = parseInt(document.getElementById('user_id').value);
            const status = document.getElementById('status').value;
            const content = document.getElementById('content').value.trim();

            const newArt = {
                id: articles.length > 0 ? Math.max(...articles.map(a => a.id)) + 1 : 1,
                title: title,
                content: content,
                status: status,
                user_id: authorId,
                created_at: new Date().toISOString(),
                updated_at: new Date().toISOString()
            };

            articles.push(newArt);
            saveDB('static_articles', articles);

            window.location.href = prefix + 'dashboard.html';
        });
    }

    // Article Edit Page
    function handleArticleEditPage() {
        const urlParams = new URLSearchParams(window.location.search);
        const artId = parseInt(urlParams.get('id'));
        if (!artId) {
            window.location.href = prefix + 'dashboard.html';
            return;
        }

        const article = articles.find(a => a.id === artId);
        if (!article) {
            window.location.href = prefix + 'dashboard.html';
            return;
        }

        // Fill form fields
        const titleInput = document.getElementById('title');
        if (titleInput) titleInput.value = article.title;

        const contentInput = document.getElementById('content');
        if (contentInput) contentInput.value = article.content;

        const userSelect = document.getElementById('user_id');
        if (userSelect) {
            let options = '';
            users.forEach(u => {
                options += `<option value="${u.id}" ${article.user_id === u.id ? 'selected' : ''}>${u.name}</option>`;
            });
            userSelect.innerHTML = options;
        }

        // Status field handle (Lock if already published in edit.blade)
        const statusSelect = document.getElementById('status');
        if (statusSelect) {
            if (article.status === 'published') {
                // If it is locked or disabled, let's keep it as is
            } else {
                statusSelect.value = article.status;
            }
        }

        const form = document.querySelector('form');
        if (!form) return;

        form.removeAttribute('action');
        form.removeAttribute('method');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            
            article.title = titleInput.value.trim();
            article.content = contentInput.value.trim();
            if (userSelect) article.user_id = parseInt(userSelect.value);
            if (statusSelect && article.status !== 'published') {
                article.status = statusSelect.value;
            }
            article.updated_at = new Date().toISOString();

            saveDB('static_articles', articles);
            window.location.href = prefix + 'dashboard.html';
        });
    }

    // Users Index Page
    function handleUsersIndexPage() {
        // Enforce Admin Access
        if (currentUser.role !== 'admin') {
            alert('Akses Ditolak: Halaman ini hanya untuk Administrator.');
            window.location.href = prefix + 'dashboard.html';
            return;
        }

        // Parse search and paginate parameters
        const urlParams = new URLSearchParams(window.location.search);
        const searchVal = urlParams.get('search') || '';
        const pageVal = parseInt(urlParams.get('page')) || 1;

        // Set search input
        const searchForm = document.querySelector('form');
        if (searchForm) {
            searchForm.removeAttribute('action');
            searchForm.removeAttribute('method');
            const searchInput = searchForm.querySelector('[name="search"]');
            if (searchInput) searchInput.value = searchVal;

            searchForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const search = searchInput.value.trim();
                const url = new URL(window.location.href);
                url.searchParams.set('search', search);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            });
        }

        // Filter and Paginate
        let filteredUsers = users;
        if (searchVal) {
            const searchLower = searchVal.toLowerCase();
            filteredUsers = users.filter(u => 
                u.name.toLowerCase().includes(searchLower) || 
                u.email.toLowerCase().includes(searchLower)
            );
        }

        // Paginate users (12 per page)
        const itemsPerPage = 12;
        const totalItems = filteredUsers.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage) || 1;
        const currentPage = Math.min(pageVal, totalPages);
        const startIndex = (currentPage - 1) * itemsPerPage;
        const paginatedUsers = filteredUsers.slice(startIndex, startIndex + itemsPerPage);

        // Render Table Rows
        const tbody = document.getElementById('static-users-tbody') || document.querySelector('tbody');
        if (tbody) {
            if (paginatedUsers.length > 0) {
                let tbodyHTML = '';
                paginatedUsers.forEach(u => {
                    const avatarUrl = u.profile_photo 
                        ? u.profile_photo 
                        : `https://ui-avatars.com/api/?name=${encodeURIComponent(u.name)}&background=6366f1&color=fff&size=32`;
                    
                    const roleBadge = u.role === 'admin'
                        ? `<span class="bg-indigo-100 text-indigo-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-indigo-200">Admin</span>`
                        : `<span class="bg-slate-100 text-slate-700 text-xs font-semibold px-2.5 py-1 rounded-full border border-slate-200">User</span>`;

                    tbodyHTML += `
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-slate-900">${u.id}</td>
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="${avatarUrl}" alt="${escapeHtml(u.name)}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
                                <span class="font-medium text-slate-900">${escapeHtml(u.name)}</span>
                            </td>
                            <td class="px-6 py-4">${escapeHtml(u.email)}</td>
                            <td class="px-6 py-4">${roleBadge}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="${prefix}users/edit.html?id=${u.id}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit User">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    ${u.id !== currentUser.id ? `
                                    <button data-delete-user-id="${u.id}" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus User">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                    ` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = tbodyHTML;

                // Add delete listener
                tbody.querySelectorAll('[data-delete-user-id]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        const id = parseInt(this.getAttribute('data-delete-user-id'));
                        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                            users = users.filter(u => u.id !== id);
                            // Clean up articles belonging to this user
                            articles = articles.filter(art => art.user_id !== id);
                            saveDB('static_users', users);
                            saveDB('static_articles', articles);
                            window.location.reload();
                        }
                    });
                });
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                            Tidak ada data user yang ditemukan.
                        </td>
                    </tr>
                `;
            }
        }
    }

    // User Create Page
    function handleUserCreatePage() {
        // Enforce Admin Access
        if (currentUser.role !== 'admin') {
            alert('Akses Ditolak: Halaman ini hanya untuk Administrator.');
            window.location.href = prefix + 'dashboard.html';
            return;
        }

        const form = document.querySelector('form');
        if (!form) return;

        form.removeAttribute('action');
        form.removeAttribute('method');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;

            const exists = users.find(u => u.email.toLowerCase() === email.toLowerCase());
            if (exists) {
                alert('Email sudah digunakan!');
                return;
            }

            const newUser = {
                id: users.length > 0 ? Math.max(...users.map(u => u.id)) + 1 : 1,
                name: name,
                email: email,
                role: role,
                profile_photo: null
            };

            users.push(newUser);
            saveDB('static_users', users);

            window.location.href = prefix + 'users/index.html';
        });
    }

    // User Edit Page
    function handleUserEditPage() {
        // Enforce Admin Access
        if (currentUser.role !== 'admin') {
            alert('Akses Ditolak: Halaman ini hanya untuk Administrator.');
            window.location.href = prefix + 'dashboard.html';
            return;
        }

        const urlParams = new URLSearchParams(window.location.search);
        const userId = parseInt(urlParams.get('id'));
        if (!userId) {
            window.location.href = prefix + 'users/index.html';
            return;
        }

        const user = users.find(u => u.id === userId);
        if (!user) {
            window.location.href = prefix + 'users/index.html';
            return;
        }

        // Fill form fields
        const nameInput = document.getElementById('name');
        if (nameInput) nameInput.value = user.name;

        const emailInput = document.getElementById('email');
        if (emailInput) emailInput.value = user.email;

        const roleSelect = document.getElementById('role');
        if (roleSelect) roleSelect.value = user.role;

        const form = document.querySelector('form');
        if (!form) return;

        form.removeAttribute('action');
        form.removeAttribute('method');

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            user.name = nameInput.value.trim();
            user.email = emailInput.value.trim();
            user.role = roleSelect.value;

            // If we updated our own account, sync the session
            if (user.id === currentUser.id) {
                localStorage.setItem('static_auth_user', JSON.stringify(user));
            }

            saveDB('static_users', users);
            window.location.href = prefix + 'users/index.html';
        });
    }

    // Profile Page
    function handleProfilePage() {
        const nameInput = document.getElementById('name');
        if (nameInput) nameInput.value = currentUser.name;

        const emailInput = document.getElementById('email');
        if (emailInput) emailInput.value = currentUser.email;

        // Render current photo display
        const avatarDisplay = document.querySelector('img.rounded-full');
        if (avatarDisplay) {
            avatarDisplay.src = currentUser.profile_photo 
                ? currentUser.profile_photo 
                : `https://ui-avatars.com/api/?name=${encodeURIComponent(currentUser.name)}&background=6366f1&color=fff&size=128`;
        }

        const form = document.querySelector('form');
        if (!form) return;

        form.removeAttribute('action');
        form.removeAttribute('method');

        // File profile photo handler (mocking via base64 upload)
        const photoInput = document.getElementById('profile_photo');
        let uploadPhotoBase64 = null;

        if (photoInput) {
            photoInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        uploadPhotoBase64 = e.target.result;
                        if (avatarDisplay) avatarDisplay.src = uploadPhotoBase64;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            
            // Find in users database
            const userInDb = users.find(u => u.id === currentUser.id);
            if (userInDb) {
                userInDb.name = nameInput.value.trim();
                userInDb.email = emailInput.value.trim();
                if (uploadPhotoBase64) {
                    userInDb.profile_photo = uploadPhotoBase64;
                }
                
                // Save DB
                saveDB('static_users', users);
                // Update session
                localStorage.setItem('static_auth_user', JSON.stringify(userInDb));
                currentUser = userInDb;

                alert('Profil berhasil diperbarui!');
                window.location.reload();
            }
        });
    }

    // 6. Utility Helpers
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        if (seconds < 60) return 'Just now';
        
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} min ago`;
        
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} hours ago`;
        
        const days = Math.floor(hours / 24);
        if (days === 1) return 'Yesterday';
        if (days < 7) return `${days} days ago`;
        
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

})();
