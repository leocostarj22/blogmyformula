<?php
// Widget dos Últimos Posts - Para uso como embed
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/Blog.php';

// Parâmetros configuráveis via GET
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$show_excerpt = isset($_GET['excerpt']) ? $_GET['excerpt'] === 'true' : true;
$show_date = isset($_GET['date']) ? $_GET['date'] === 'true' : true;
$show_category = isset($_GET['category']) ? $_GET['category'] === 'true' : true;
$theme = isset($_GET['theme']) ? $_GET['theme'] : 'default'; // default, minimal, card

$blog = new Blog();
$latest_posts = $blog->getLatestPosts($limit);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Últimos Posts - <?php echo BLOG_TITLE; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .widget-container {
            max-width: 100%;
            margin: 0;
        }
        
        /* Tema Default */
        .theme-default .post-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        
        .theme-default .post-item:last-child {
            border-bottom: none;
        }
        
        /* Tema Minimal */
        .theme-minimal .post-item {
            padding: 10px 0;
            border-left: 3px solid #2B80B9;
            padding-left: 15px;
            margin-bottom: 15px;
        }
        
        /* Tema Card */
        .theme-card .post-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }
        
        .theme-card .post-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .post-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .post-title a {
            color: #2B80B9;
            text-decoration: none;
        }
        
        .post-title a:hover {
            color: #1a5a8a;
            text-decoration: underline;
        }
        
        .post-meta {
            font-size: 0.85rem;
            color: #666;
            margin-bottom: 8px;
        }
        
        .post-excerpt {
            font-size: 0.9rem;
            color: #555;
            line-height: 1.4;
        }
        
        .category-badge {
            background-color: #2B80B9;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            text-decoration: none;
        }
        
        .category-badge:hover {
            background-color: #1a5a8a;
            color: white;
        }
        
        .widget-header {
            border-bottom: 2px solid #2B80B9;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .widget-title {
            color: #2B80B9;
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="widget-container theme-<?php echo $theme; ?>">
        <div class="widget-header">
            <h2 class="widget-title">
                <i class="fas fa-newspaper me-2"></i>
                Últimos Posts
            </h2>
        </div>
        
        <?php if (empty($latest_posts)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Nenhum post encontrado.
            </div>
        <?php else: ?>
            <div class="posts-list">
                <?php foreach ($latest_posts as $post): ?>
                    <article class="post-item">
                        <h3 class="post-title">
                            <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $post['slug']; ?>" target="_blank">
                                <?php echo htmlspecialchars($post['title']); ?>
                            </a>
                        </h3>
                        
                        <?php if ($show_date || $show_category): ?>
                            <div class="post-meta">
                                <?php if ($show_date): ?>
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <span class="me-3"><?php echo date('d/m/Y', strtotime($post['created_at'])); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($show_category && !empty($post['category_name'])): ?>
                                    <a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $post['category_slug']; ?>" 
                                       class="category-badge" target="_blank">
                                        <?php echo htmlspecialchars($post['category_name']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($show_excerpt && !empty($post['excerpt'])): ?>
                            <div class="post-excerpt">
                                <?php echo htmlspecialchars(substr($post['excerpt'], 0, 150)) . (strlen($post['excerpt']) > 150 ? '...' : ''); ?>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-3">
                <a href="<?php echo SITE_URL; ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                    <i class="fas fa-external-link-alt me-1"></i>
                    Ver todos os posts
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>