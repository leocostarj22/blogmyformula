<?php
require_once 'includes/Blog.php';

$blog = new Blog();

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);

// Buscar posts
$posts = $blog->getPosts($page);
$total_posts = $blog->getTotalPosts();
$total_pages = ceil($total_posts / POSTS_PER_PAGE);

// Posts em destaque
$featured_posts = $blog->getFeaturedPosts(3);

$page_title = 'Início';
$page_description = BLOG_DESCRIPTION;

include 'includes/header.php';
?>

<div class="row">
    <!-- Main Content -->
    <div class="col-lg-8">
        <?php if ($page == 1 && !empty($featured_posts)): ?>
        <!-- Featured Posts -->
        <section class="mb-5">
            <?php 
            // Pegar apenas o primeiro post em destaque
            $featured = $featured_posts[0]; 
            ?>
            <div class="card mb-4 shadow-sm featured-post">
                <?php if (isset($featured['image']) && !empty($featured['image'])): ?>
                <img src="<?php echo SITE_URL . UPLOAD_DIR . $featured['image']; ?>" 
                     class="card-img-top" 
                     alt="<?php echo $featured['title']; ?>" 
                     style="height: 100%; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title">
                        <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $featured['slug']; ?>">
                            <?php echo $featured['title']; ?>
                        </a>
                    </h5>
                    
                    <p class="card-text text-muted mb-3 fs-6">
                        <i class="fas fa-calendar me-2"></i><?php echo formatDate($featured['created_at']); ?>
                        <span class="ms-4"><i class="fas fa-eye me-2"></i><?php echo $featured['views']; ?> visualizações</span>
                        <?php if (isset($featured['author'])): ?>
                        <span class="ms-4"><i class="fas fa-user me-2"></i><?php echo $featured['author']; ?></span>
                        <?php endif; ?>
                    </p>
                    
                    <p class="card-text mb-4">
                        <?php echo truncateText(strip_tags($featured['excerpt'] ?: $featured['content']), 250); ?>
                    </p>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $featured['slug']; ?>" 
                           class="btn btn-primary btn-sm px-3">
                            Ler Mais
                        </a>
                        
                        <div class="text-muted">
                            <small>
                                <i class="fas fa-clock me-1"></i>
                                <?php 
                                $reading_time = ceil(str_word_count(strip_tags($featured['content'])) / 200);
                                echo $reading_time . ' min de leitura';
                                ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Recent Posts -->
        <section>
            <h2 class="mb-4"><?php echo $page > 1 ? 'Posts - Página ' . $page : 'Posts Recentes'; ?></h2>
            
            <?php if (empty($posts)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Nenhum post encontrado.
            </div>
            <?php else: ?>
            
            <?php foreach ($posts as $post): ?>
            <article class="card mb-4">
                <div class="row g-0">
                    <?php if (isset($post['image']) && !empty($post['image'])): ?>
                    <div class="col-md-4">
                        <img src="<?php echo SITE_URL . UPLOAD_DIR . $post['image']; ?>" class="img-fluid h-100" alt="<?php echo $post['title']; ?>" style="object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                    <?php else: ?>
                    <div class="col-12">
                    <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                            <p class="card-text flex-grow-1"><?= truncateText(strip_tags($post['content']), 150) ?></p>
                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                <small class="text-muted">
                                    <?php if (isset($post['author']) && !empty($post['author'])): ?>
                                        Por <?= htmlspecialchars($post['author']) ?> • 
                                    <?php endif; ?>
                                    <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                </small>
                                <a href="post.php?slug=<?= urlencode($post['slug']) ?>" class="btn btn-primary btn-sm">Ler Mais</a>
                            </div>
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
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i> Anterior
                        </a>
                    </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>">
                            Próxima <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
            
            <?php endif; ?>
        </section>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <?php include 'includes/sidebar.php'; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>