<?php
require_once 'includes/Blog.php';

$blog = new Blog();

// Verificar se o slug foi fornecido
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header('Location: ' . SITE_URL);
    exit;
}

$slug = sanitizeInput($_GET['slug']);
$category = $blog->getCategoryBySlug($slug);

if (!$category) {
    header('HTTP/1.0 404 Not Found');
    $page_title = 'Categoria não encontrada';
    include 'includes/header.php';
    echo '<div class="alert alert-danger"><h4>Categoria não encontrada</h4><p>A categoria que você está procurando não existe.</p></div>';
    include 'includes/footer.php';
    exit;
}

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

// Buscar posts da categoria
$posts = $blog->getPosts($page, $category['id']);
$total_posts = $blog->getTotalPosts($category['id']);
$total_pages = ceil($total_posts / POSTS_PER_PAGE);

// Meta tags para SEO
$page_title = isset($category['meta_title']) && $category['meta_title'] ? $category['meta_title'] : $category['name'];
$page_description = isset($category['meta_description']) && $category['meta_description'] ? $category['meta_description'] : (isset($category['description']) ? $category['description'] : '');

include 'includes/header.php';
?>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <!-- Category Header -->
        <div class="card mb-4 category-header" <?php if ($category['background_image']): ?>style="background-image: url('<?php echo SITE_URL . UPLOAD_DIR . $category['background_image']; ?>'); background-size: cover; background-position: center; color: white;"<?php endif; ?>>
            <div class="card-body" style="min-height: 200px; display: flex; flex-direction: column; justify-content: center;">
                <h1 class="card-title" <?php if ($category['background_image']): ?>style="color:  #2B80B9;font-family: math;"<?php else: ?>style="font-family: math;"<?php endif; ?>><?php echo $category['name']; ?></h1>
                <?php if ($category['description']): ?>
                <p class="card-text" <?php if ($category['background_image']): ?>style="color: #212529;font-weight: 500;"<?php endif; ?>><?php echo $category['description']; ?></p>
                <?php endif; ?>
                <small <?php if ($category['background_image']): ?>style="color: #212529;"<?php else: ?>class="text-muted"<?php endif; ?>>
                    <i class="fas fa-file-alt me-1"></i><?php echo $total_posts; ?> post(s) nesta categoria
                </small>
            </div>
        </div>
        
        <!-- Posts -->
        <?php if (empty($posts)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Nenhum post encontrado nesta categoria.
        </div>
        <?php else: ?>
        
        <?php foreach ($posts as $post): ?>
        <article class="card mb-4">
            <div class="row g-0">
                <?php if (isset($post['image']) && $post['image']): ?>
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
                                <span class="ms-3"><i class="fas fa-eye me-1"></i><?php echo $post['views']; ?></span>
                                <?php if (isset($post['author_id']) && $post['author_id']): ?>
                                <span class="ms-3"><i class="fas fa-user me-1"></i>Autor ID: <?php echo $post['author_id']; ?></span>
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
                    <a class="page-link" href="?slug=<?php echo $category['slug']; ?>&page=<?php echo $page - 1; ?>">
                        <i class="fas fa-chevron-left"></i> Anterior
                    </a>
                </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                    <a class="page-link" href="?slug=<?php echo $category['slug']; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?slug=<?php echo $category['slug']; ?>&page=<?php echo $page + 1; ?>">
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