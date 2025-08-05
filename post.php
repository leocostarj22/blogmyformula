<?php
require_once 'includes/Blog.php';

$blog = new Blog();

// Verificar se o slug foi fornecido
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header('Location: ' . SITE_URL);
    exit;
}

$slug = sanitizeInput($_GET['slug']);
$post = $blog->getPostBySlug($slug);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    $page_title = 'Post não encontrado';
    include 'includes/header.php';
    echo '<div class="alert alert-danger"><h4>Post não encontrado</h4><p>O post que você está procurando não existe ou foi removido.</p></div>';
    include 'includes/footer.php';
    exit;
}

// Buscar posts relacionados - CORRIGIDO: usar 'id' em vez de 'post_id'
$related_posts = $blog->getRelatedPosts($post['id'], $post['category_id'], 3);

// Meta tags para SEO - CORRIGIDO: verificar se a chave existe
$page_title = $post['meta_title'] ?: $post['title'];
$page_description = $post['meta_description'] ?: truncateText(strip_tags($post['content']), 160);
$page_keywords = isset($post['meta_keywords']) ? $post['meta_keywords'] : '';

include 'includes/header.php';
?>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <article class="card">
            <?php if ($post['image']): ?>
            <img src="<?php echo SITE_URL . UPLOAD_DIR . $post['image']; ?>" class="card-img-top" alt="<?php echo $post['title']; ?>" style="max-height: 400px; object-fit: cover;">
            <?php endif; ?>
            
            <div class="card-body">
                <!-- Post Header -->
                <header class="mb-4">
                    <h1 class="card-title"><?php echo $post['title']; ?></h1>
                    
                    <div class="text-muted mb-3">
                        <small>
                            <i class="fas fa-calendar me-1"></i><?php echo formatDate($post['created_at']); ?>
                            <span class="ms-3">
                                <i class="fas fa-folder me-1"></i>
                                <a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $post['category_slug']; ?>" class="text-decoration-none">
                                    <?php echo $post['category_name']; ?>
                                </a>
                            </span>
                            <span class="ms-3"><i class="fas fa-eye me-1"></i><?php echo $post['views']; ?> visualizações</span>
                            <?php // Comentado até criar tabela de usuários
                            // if (isset($post['author_id']) && $post['author_id']): ?>
                            <?php // <span class="ms-3"><i class="fas fa-user me-1"></i>Autor ID: <?php echo $post['author_id']; ?></span> ?>
                            <?php // endif; ?>
                            <?php if (isset($post['author_name']) && $post['author_name']): ?>
                            <span class="ms-3"><i class="fas fa-user me-1"></i><?php echo $post['author_name']; ?></span>
                            <?php endif; ?>
                        </small>
                    </div>
                    
                    <?php if (!empty($post['tags'])): ?>
                    <div class="mb-3">
                        <?php foreach ($post['tags'] as $tag): ?>
                        <a href="<?php echo SITE_URL; ?>search.php?tag=<?php echo $tag['slug']; ?>" class="badge bg-primary text-decoration-none me-1">
                            <i class="fas fa-tag me-1"></i><?php echo $tag['name']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </header>
                
                <!-- Post Content -->
                <div class="post-content">
                    <?php echo $post['content']; ?>
                </div>
                
                <!-- Social Share -->
                <div class="mt-4 pt-3 border-top">
                    <h6>Compartilhar:</h6>
                    <div class="d-flex gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(SITE_URL . 'post.php?slug=' . $post['slug']); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(SITE_URL . 'post.php?slug=' . $post['slug']); ?>&text=<?php echo urlencode($post['title']); ?>" target="_blank" class="btn btn-outline-info btn-sm">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($post['title'] . ' - ' . SITE_URL . 'post.php?slug=' . $post['slug']); ?>" target="_blank" class="btn btn-outline-success btn-sm">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        </article>
        
        <!-- Related Posts -->
        <?php if (!empty($related_posts)): ?>
        <section class="mt-5">
            <h3 class="mb-4">Posts Relacionados</h3>
            <div class="row">
                <?php foreach ($related_posts as $related): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <?php if ($related['image']): ?>
                        <img src="<?php echo SITE_URL . UPLOAD_DIR . $related['image']; ?>" class="card-img-top" alt="<?php echo $related['title']; ?>" style="height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title">
                                <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $related['slug']; ?>" class="text-decoration-none">
                                    <?php echo truncateText($related['title'], 50); ?>
                                </a>
                            </h6>
                            <p class="card-text small text-muted">
                                <i class="fas fa-calendar me-1"></i><?php echo formatDate($related['created_at'], 'd/m/Y'); ?>
                            </p>
                            <p class="card-text flex-grow-1"><?php echo truncateText(strip_tags($related['content']), 80); ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted">
                                    <?php if (isset($post['author']) && !empty($post['author'])): ?>
                                        Por <?= htmlspecialchars($post['author']) ?> • 
                                    <?php endif; ?>
                                    <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                </small>
                                <a href="post.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-primary btn-sm">Ler mais</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <?php include 'includes/sidebar.php'; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>