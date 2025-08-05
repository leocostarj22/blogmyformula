<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . BLOG_TITLE : BLOG_TITLE; ?></title>
    <meta name="description" content="<?php echo isset($page_description) ? $page_description : BLOG_DESCRIPTION; ?>">
    <?php if (isset($page_keywords)): ?>
    <meta name="keywords" content="<?php echo $page_keywords; ?>">
    <?php endif; ?>
    
    <!-- Google Fonts - Crimson Text (similar to Minion Pro) as fallback -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>assets/css/dropdown-styles.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #f8f9fa;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>" style="color: #2B80B9;">
                <img src="<?php echo SITE_URL; ?>uploads/logo-myformula-colors.png" alt="MyFormula Logo" height="45" class="me-2">
                <span class="fw-bold" style="color: #2B80B9;"></span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: #2B80B9;">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex align-items-center">
                    <ul class="navbar-nav me-3">
                        <!-- Início -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>" style="color: #2B80B9;">
                                <i class="fas fa-home me-1"></i>Início
                            </a>
                        </li>
                        
                        <!-- Dropdown de Categorias -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="color: #2B80B9;">
                                <i class="fas fa-list me-1"></i>Categorias
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="categoriasDropdown">
                                <?php
                                $blog = new Blog();
                                $categories = $blog->getCategories();
                                
                                // Definir ícones para cada categoria
                                $category_icons = [
                                    'perda-de-peso' => 'fas fa-weight-hanging',
                                    'menopausa' => 'fas fa-female',
                                    'anti-aging' => 'fas fa-spa',
                                    'saude-sexual' => 'fas fa-heart',
                                    'sono' => 'fas fa-bed',
                                    'geral' => 'fas fa-newspaper',
                                    'tecnologia' => 'fas fa-laptop-code',
                                    'programacao' => 'fas fa-code',
                                    'design' => 'fas fa-paint-brush',
                                    'marketing' => 'fas fa-bullhorn'
                                ];
                                
                                foreach ($categories as $category):
                                    $icon = $category_icons[$category['slug']] ?? 'fas fa-folder';
                                    
                                    // Buscar número de posts por categoria
                                    $post_count = $blog->countPostsByCategory($category['id']);
                                ?>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" 
                                       href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $category['slug']; ?>">
                                        <span>
                                            <i class="<?php echo $icon; ?> me-2"></i>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </span>
                                        <span class="badge bg-secondary category-count"><?php echo $post_count; ?></span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                                
                                <!-- Separador -->
                                <li><hr class="dropdown-divider"></li>
                                
                                <!-- Link para ver todas as categorias -->
                                <li>
                                    <a class="dropdown-item" href="<?php echo SITE_URL; ?>categories.php">
                                        <i class="fas fa-th-large me-2"></i>
                                        <strong>Ver Todas as Categorias</strong>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Link do Painel Admin -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>admin/" style="color: #2B80B9;" title="Painel Administrativo">
                                <i class="fas fa-cog me-1"></i>Admin
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Search Form -->
                    <form class="d-flex" method="GET" action="<?php echo SITE_URL; ?>search.php">
                        <input class="form-control me-2" type="search" name="q" placeholder="Buscar..." value="<?php echo isset($_GET['q']) ? sanitizeInput($_GET['q']) : ''; ?>" style="min-width: 200px;">
                        <button class="btn" type="submit" style="background-color: #2B80B9; color: white; border-color: #2B80B9;" title="Buscar">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="container my-4">