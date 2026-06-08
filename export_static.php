<?php
/**
 * Static Site Exporter for Laravel (GitHub Pages compatibility)
 * Boots Laravel, renders Blade views, post-processes links/assets, and saves them to docs/
 */

define('LARAVEL_START', microtime(true));

// Boot Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

// Bind a dummy HTTP Request to the container to prevent SessionGuard from failing in CLI mode
use Illuminate\Http\Request;
$request = Request::create('/', 'GET');
$app->instance('request', $request);

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ViewErrorBag;
use App\Models\User;
use App\Models\Article;

// Share empty ErrorBag globally for Blade views
View::share('errors', new ViewErrorBag());

echo "--- Starting Static Site Export ---\n";

// 1. Get or Create Mock Data
$users = collect();
$articles = collect();

try {
    $users = User::all();
    $articles = Article::with('user')->get();
    echo "Connected to database successfully. Found " . $users->count() . " users and " . $articles->count() . " articles.\n";
} catch (\Exception $e) {
    echo "Database connection failed or not configured. Using fallback mock data.\n";
}

if ($users->isEmpty()) {
    $admin = new User([
        'id' => 1,
        'name' => 'Galih Superadmin',
        'email' => 'galih@example.com',
        'role' => 'superadmin',
        'created_at' => now(),
    ]);
    $author = new User([
        'id' => 2,
        'name' => 'Budi Penulis',
        'email' => 'budi@example.com',
        'role' => 'user',
        'created_at' => now(),
    ]);
    $users = collect([$admin, $author]);
}

$adminUser = $users->firstWhere('role', 'superadmin') ?? $users->first();

if ($articles->isEmpty()) {
    $art1 = new Article([
        'title' => 'Membangun Karir sebagai Web Developer',
        'content' => "Menjadi web developer di era digital ini merupakan salah satu pilihan karir yang sangat menjanjikan. Dengan perkembangan teknologi yang sangat pesat, permintaan akan website dan aplikasi web terus meningkat setiap harinya.\n\nUntuk memulai karir ini, Anda perlu menguasai beberapa dasar penting:\n1. HTML untuk struktur halaman web\n2. CSS untuk memperindah tampilan (styling)\n3. JavaScript untuk logika dan interaksi dinamis\n\nSetelah menguasai dasar-dasar tersebut, Anda bisa mulai mempelajari framework modern seperti Laravel untuk backend, serta Tailwind CSS untuk styling cepat.",
        'status' => 'published',
        'user_id' => $users[1]->id ?? $adminUser->id,
        'created_at' => now()->subDays(2),
        'updated_at' => now()->subDays(2),
    ]);
    $art1->id = 1;

    $art2 = new Article([
        'title' => 'Tips Produktivitas Bekerja dari Rumah (WFH)',
        'content' => "Bekerja dari rumah (Work From Home) memberikan fleksibilitas tinggi, tetapi juga menyimpan tantangan produktivitas tersendiri. Banyak gangguan yang dapat memecah fokus Anda.\n\nBerikut beberapa tips agar tetap produktif:\n- Buat ruang kerja khusus yang nyaman dan tenang.\n- Tetapkan jadwal kerja yang jelas dan patuhi jam tersebut.\n- Buat daftar tugas (to-do list) setiap pagi.\n- Ambil istirahat pendek secara berkala untuk menyegarkan pikiran.\n\nDengan disiplin diri yang baik, WFH bisa menjadi sangat efisien dan menyenangkan.",
        'status' => 'published',
        'user_id' => $adminUser->id,
        'created_at' => now()->subDay(),
        'updated_at' => now()->subDay(),
    ]);
    $art2->id = 2;

    $art3 = new Article([
        'title' => 'Konsep Draft Artikel Baru',
        'content' => "Ini adalah konten dari artikel draft yang belum dipublikasikan ke publik. Konsep ini hanya dapat dilihat oleh penulis di menu Drafts.",
        'status' => 'draft',
        'user_id' => $adminUser->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $art3->id = 3;

    $articles = collect([$art1, $art2, $art3]);
    
    foreach ($articles as $art) {
        $art->setRelation('user', $users->firstWhere('id', $art->user_id) ?? $adminUser);
    }
}

// 2. Set Up Logged-in Session
Auth::login($adminUser);

// 3. Define Paginated Collections for Blade rendering
$paginatedArticles = new \Illuminate\Pagination\LengthAwarePaginator(
    $articles,
    $articles->count(),
    12,
    1,
    ['path' => '/dashboard']
);

$paginatedUsers = new \Illuminate\Pagination\LengthAwarePaginator(
    $users,
    $users->count(),
    12,
    1,
    ['path' => '/users']
);

// Define views to render: array(output_file_relative_to_docs, blade_view_name, data, depth)
$views = [
    // Root level files
    ['index.html', 'auth.login', [], 0],
    ['about.html', 'about', [], 0],
    ['contact.html', 'contact', [], 0],
    ['login.html', 'auth.login', [], 0],
    ['register.html', 'auth.register', [], 0],
    ['dashboard.html', 'dashboard', ['articles' => $paginatedArticles, 'users' => $users], 0],
    ['drafts.html', 'dashboard', ['articles' => $paginatedArticles, 'users' => $users, 'isDraftsPage' => true], 0],
    ['profile.html', 'profile.edit', ['user' => $adminUser], 0],
    
    // Articles subdirectory (depth 1)
    ['articles/create.html', 'articles.create', ['users' => $users], 1],
    ['articles/edit.html', 'articles.edit', ['article' => $articles->first(), 'users' => $users], 1],
    ['articles/show.html', 'articles.show', ['article' => $articles->first()], 1],
    
    // Users subdirectory (depth 1)
    ['users/index.html', 'users.index', ['users' => $paginatedUsers], 1],
    ['users/create.html', 'users.create', [], 1],
    ['users/edit.html', 'users.edit', ['user' => $adminUser], 1],
];

// Ensure target directories exist
$targetDirs = [
    __DIR__.'/docs',
    __DIR__.'/docs/articles',
    __DIR__.'/docs/users',
];

foreach ($targetDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: " . basename($dir) . "\n";
    }
}

// 4. Render and Post-Process Views
foreach ($views as $viewConfig) {
    list($outFile, $viewName, $data, $depth) = $viewConfig;
    
    try {
        // Render Blade template
        $html = View::make($viewName, $data)->render();
        
        // Post-process to adjust paths and links for GitHub Pages static server
        $html = postProcessHtml($html, $depth);
        
        $destPath = __DIR__.'/docs/'.$outFile;
        file_put_contents($destPath, $html);
        echo "✓ Exported: docs/{$outFile}\n";
    } catch (\Exception $e) {
        echo "✗ Failed to export {$outFile}: " . $e->getMessage() . "\n";
    }
}

echo "--- Static Site Export Completed ---\n";

/**
 * Normalizes absolute/root-relative paths and routes to fit static subfolder architecture
 */
function postProcessHtml($html, $depth) {
    $prefix = ($depth == 0) ? './' : str_repeat('../', $depth);
    
    // Retrieve base URLs to find and normalize
    $appUrl = rtrim(config('app.url'), '/');
    $baseUrls = [
        $appUrl,
        'http://localhost/article-management/public',
        'http://localhost/article-management',
        'http://localhost',
        'http://127.0.0.1:8000'
    ];
    
    // Normalize absolute domains to root-relative paths starting with '/'
    foreach ($baseUrls as $url) {
        $html = str_ireplace($url . '/build/', '/build/', $html);
        $html = str_ireplace($url . '/storage/', '/storage/', $html);
        $html = str_ireplace($url . '/articles/', '/articles/', $html);
        $html = str_ireplace($url . '/users/', '/users/', $html);
        $html = str_ireplace($url . '/dashboard', '/dashboard', $html);
        $html = str_ireplace($url . '/drafts', '/drafts', $html);
        $html = str_ireplace($url . '/about', '/about', $html);
        $html = str_ireplace($url . '/contact', '/contact', $html);
        $html = str_ireplace($url . '/profile', '/profile', $html);
        $html = str_ireplace($url . '/login', '/login', $html);
        $html = str_ireplace($url . '/register', '/register', $html);
        $html = str_ireplace($url . '/logout', '/logout', $html);
        $html = str_ireplace($url . '"', '/"', $html);
        $html = str_ireplace($url . "'", "/'", $html);
    }
    
    // Fix standard HTML links to point to relative static HTML pages
    $html = str_replace('href="/dashboard"', 'href="' . $prefix . 'dashboard.html"', $html);
    $html = str_replace('href="/drafts"', 'href="' . $prefix . 'drafts.html"', $html);
    $html = str_replace('href="/about"', 'href="' . $prefix . 'about.html"', $html);
    $html = str_replace('href="/contact"', 'href="' . $prefix . 'contact.html"', $html);
    $html = str_replace('href="/profile"', 'href="' . $prefix . 'profile.html"', $html);
    $html = str_replace('href="/login"', 'href="' . $prefix . 'login.html"', $html);
    $html = str_replace('href="/register"', 'href="' . $prefix . 'register.html"', $html);
    $html = str_replace('href="/users"', 'href="' . $prefix . 'users/index.html"', $html);
    $html = str_replace('href="/"', 'href="' . $prefix . 'index.html"', $html);
    
    // Fix action endpoints
    $html = str_replace('action="/login"', 'action="login"', $html);
    $html = str_replace('action="/register"', 'action="register"', $html);
    $html = str_replace('action="/logout"', 'action="logout"', $html);
    $html = str_replace('action="/articles"', 'action="articles"', $html);
    $html = str_replace('action="/users"', 'action="users"', $html);
    $html = str_replace('action="/profile"', 'action="profile"', $html);
    
    // Fix nested articles routes
    $html = str_replace('href="/articles/create"', 'href="' . $prefix . 'articles/create.html"', $html);
    $html = preg_replace('/href="\/articles\/(\d+)\/edit"/', 'href="' . $prefix . 'articles/edit.html?id=$1"', $html);
    $html = preg_replace('/href="\/articles\/(\d+)"/', 'href="' . $prefix . 'articles/show.html?id=$1"', $html);
    $html = preg_replace('/action="\/articles\/(\d+)"/', 'action="articles/edit?id=$1"', $html);
    
    // Fix nested users routes
    $html = str_replace('href="/users/create"', 'href="' . $prefix . 'users/create.html"', $html);
    $html = preg_replace('/href="\/users\/(\d+)\/edit"/', 'href="' . $prefix . 'users/edit.html?id=$1"', $html);
    $html = preg_replace('/action="\/users\/(\d+)"/', 'action="users/edit?id=$1"', $html);
    
    // Fix asset paths
    $html = str_replace('href="/build/', 'href="' . $prefix . 'build/', $html);
    $html = str_replace('src="/build/', 'src="' . $prefix . 'build/', $html);
    $html = str_replace('href="/storage/', 'href="' . $prefix . 'storage/', $html);
    $html = str_replace('src="/storage/', 'src="' . $prefix . 'storage/', $html);
    
    // Inject Target IDs for static-app.js DOM manipulation
    $html = str_replace('<nav class="bg-[#1e293b]', '<nav id="static-navbar" class="bg-[#1e293b]', $html);
    $html = str_replace('<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">', '<div id="static-articles-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">', $html);
    $html = str_replace('<tbody class="divide-y divide-slate-200">', '<tbody id="static-users-tbody" class="divide-y divide-slate-200">', $html);
    $html = str_replace('<div class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">', '<div id="static-welcome-auth" class="sm:fixed sm:top-0 sm:right-0 p-6 text-right z-10">', $html);
    
    // Inject static simulator javascript
    $jsInjection = '<script src="' . $prefix . 'static-app.js"></script>' . "\n";
    $html = str_replace('</body>', $jsInjection . '</body>', $html);
    
    return $html;
}
