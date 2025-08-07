<?php
// Buscar posts populares
$popular_posts = $blog->getPopularPosts(5);

// Buscar posts recentes
$recent_posts = $blog->getRecentPosts(5);

// Buscar todas as tags
$tags = $blog->getTagsWithCount();
?>

<!-- Search Widget -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-search me-2"></i>Buscar</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo SITE_URL; ?>search.php">
            <div class="input-group">
                <input type="text" class="form-control" name="q" placeholder="Digite sua busca..." value="<?php echo isset($_GET['q']) ? sanitizeInput($_GET['q']) : ''; ?>" style="border-radius: 0;">
                <button class="btn btn-primary" type="submit" style="border-radius: 0;">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Categories Widget -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Categorias</h5>
    </div>
    <div class="card-body">
        <ul class="list-unstyled mb-0">
            <?php foreach ($blog->getCategories() as $category): ?>
            <li class="mb-2">
                <a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $category['slug']; ?>" class="text-decoration-none">
                    <i class="fas fa-angle-right me-2"></i><?php echo $category['name']; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Popular Posts Widget -->
<?php if (!empty($popular_posts)): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Posts Populares</h5>
    </div>
    <div class="card-body">
        <?php foreach ($popular_posts as $popular): ?>
        <div class="d-flex mb-3">
            <?php if ($popular['image']): ?>
            <img src="<?php echo SITE_URL . UPLOAD_DIR . $popular['image']; ?>" class="me-3" style="width: 60px; height: 60px; object-fit: cover;" alt="<?php echo $popular['title']; ?>">
            <?php else: ?>
            <div class="bg-light me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="fas fa-image text-muted"></i>
            </div>
            <?php endif; ?>
            <div class="flex-grow-1">
                <h6 class="mb-1">
                    <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $popular['slug']; ?>" class="text-decoration-none">
                        <?php echo truncateText($popular['title'], 50); ?>
                    </a>
                </h6>
                <small class="text-muted">
                    <i class="fas fa-eye me-1"></i><?php echo $popular['views']; ?> visualizações
                </small>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Recent Posts Widget -->
<?php if (!empty($recent_posts)): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Posts Recentes</h5>
    </div>
    <div class="card-body">
        <?php foreach ($recent_posts as $recent): ?>
        <div class="mb-3">
            <h6 class="mb-1">
                <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $recent['slug']; ?>" class="text-decoration-none">
                    <?php echo truncateText($recent['title'], 60); ?>
                </a>
            </h6>
            <small class="text-muted">
                <i class="fas fa-calendar me-1"></i><?php echo formatDate($recent['created_at'], 'd/m/Y'); ?>
            </small>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<!-- Tags Widget -->
<?php if (!empty($tags)): ?>
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Tags</h5>
    </div>
    <div class="card-body">
        <?php foreach ($tags as $tag): ?>
        <a href="<?php echo SITE_URL; ?>search.php?tag=<?php echo $tag['slug']; ?>" class="badge bg-secondary text-decoration-none me-1 mb-1">
            <?php echo $tag['name']; ?> (<?php echo $tag['post_count']; ?>)
        </a>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>