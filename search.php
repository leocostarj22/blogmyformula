<?php
require_once 'includes/Blog.php';

$blog = new Blog();

$search_query = '';
$tag_slug = '';
$posts = [];
$total_posts = 0;
$total_pages = 0;

// Verificar tipo de busca
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = sanitizeInput($_GET['q']);
    $search_type = 'text';
} elseif (isset($_GET['tag']) && !empty($_GET['tag'])) {
    $tag_slug = sanitizeInput($_GET['tag']);
    $search_type = 'tag';
} else {
    $search_type = 'empty';
}

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

if ($search_type === 'text') {
    // Busca por texto
    $posts = $blog->getPosts($page, null, $search_query);
    $total_posts = $blog->getTotalPosts(null, $search_query);
    $page_title = 'Busca: ' . $search_query;
    $search_title = 'Resultados para: "' . $search_query . '"';
} elseif ($search_type === 'tag') {
    // Busca por tag
    $tag = $blog->fetch("SELECT * FROM " . DB_PREFIX . "blog_tags WHERE slug = ?", [$tag_slug]);
    if ($tag) {
        $posts = $blog->fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug 
             FROM " . DB_PREFIX . "blog_posts p 
             LEFT JOIN " . DB_PREFIX . "blog_categories c ON p.category_id = c.category_id 
             JOIN " . DB_PREFIX . "blog_post_tags pt ON p.post_id = pt.post_id 
             WHERE pt.tag_id = ? AND p.status = 1 
             ORDER BY p.date_added DESC 
             LIMIT ? OFFSET ?",
            [$tag['tag_id'], POSTS_PER_PAGE, ($page - 1) * POSTS_PER_PAGE]
        );
        
        $total_result = $blog->fetch(
            "SELECT COUNT(*) as total FROM " . DB_PREFIX . "blog_posts p 
             JOIN " . DB_PREFIX . "blog_post_tags pt ON p.post_id = pt.post_id 
             WHERE pt.tag_id = ? AND p.status = 1",
            [$tag['tag_id']]
        );
        $total_posts = $total_result['total'];
        
        $page_title = 'Tag: ' . $tag['name'];
        $search_title = 'Posts com a tag: "' . $tag['name'] . '"';
    } else {
        $search_type = 'empty';
        $search_title = 'Tag não encontrada';
    }
} else {
    $page_title = 'Busca';
    $search_title = 'Digite algo para buscar';
}

$total_pages = ceil($total_posts / POSTS_PER_PAGE);

include 'includes/header.php';
?>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Search Header -->
        <div class="card mb-4">
            <div class="card-body">
                <h1 class="card-title"><?php echo $search_title; ?></h1>
                <?php if ($total_posts > 0): ?>
                <small class="text-muted">
                    <i class="fas fa-search me-1"></i><?php echo $total_posts; ?> resultado(s) encontrado(s)
                    <?php if ($page > 1): ?>
                    - Página <?php echo $page; ?> de <?php echo $total_pages; ?>
                    <?php endif; ?>
                </small>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="<?php echo SITE_URL; ?>search.php">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Digite sua busca..." value="<?php echo $search_query; ?>">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search me-1"></i>Buscar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Results -->
        <?php if (empty($posts) && ($search_type === 'text' || $search_type === 'tag')): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Nenhum resultado encontrado. Tente usar outras palavras-chave.
        </div>
        <?php elseif (!empty($posts)): ?>
        
        <?php foreach ($posts as $post): ?>
        <article class="card mb-4">
            <div class="row g-0">
                <?php if ($post['image']): ?>
                <div class="col-md-4">
                    <img src="<?php echo SITE_URL . UPLOAD_DIR . $post['image']; ?>" class="img-fluid rounded-start h-100" alt="<?php echo $post['title']; ?>" style="object-fit: cover;">
                </div>
                <div class="col-md-8">
                <?php else: ?>
                <div class="col-12">
                <?php endif; ?>
                    <div class="card-body">
                        <h3 class="card-title">
                            <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $post['slug']; ?>" class="text-decoration-none">
                                <?php echo $post['title']; ?>
                            </a>
                        </h3>
                        
                        <div class="text-muted mb-3">
                            <small>
                                <i class="fas fa-calendar me-1"></i><?php echo formatDate($post['created_at']); ?>
                                <span class="ms-3"><i class="fas fa-folder me-1"></i>
                                    <a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $post['category_slug']; ?>" class="text-decoration-none">
                                        <?php echo $post['category_name']; ?>
                                    </a>
                                </span>
                                <span class="ms-3"><i class="fas fa-eye me-1"></i><?php echo $post['views']; ?></span>
                                <?php if ($post['author']): ?>
                                <span class="ms-3"><i class="fas fa-user me-1"></i><?php echo $post['author']; ?></span>
                                <?php endif; ?>
                            </small>
                        </div>
                        
                        <p class="card-text"><?php echo truncateText(strip_tags($post['excerpt'] ?: $post['content'])); ?></p>
                        
                        <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $post['slug']; ?>" class="btn btn-primary">
                            Ler mais <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <nav aria-label="Paginação">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo $search_type === 'text' ? 'q=' . urlencode($search_query) : 'tag=' . urlencode($tag_slug); ?>&page=<?php echo $page - 1; ?>">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?<?php echo $search_type === 'text' ? 'q=' . urlencode($search_query) : 'tag=' . urlencode($tag_slug); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?<?php echo $search_type === 'text' ? 'q=' . urlencode($search_query) : 'tag=' . urlencode($tag_slug); ?>&page=<?php echo $page + 1; ?>">
                        Próxima <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
        
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <?php include 'includes/sidebar.php'; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>