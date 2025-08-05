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
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #f8f9fa;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>" style="color: #2B80B9;">
                <img src="<?php echo SITE_URL; ?>uploads/logo-myformula-colors.png" alt="MyFormula Logo" height="40">
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="border-color: #2B80B9;">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex align-items-center">
                    <ul class="navbar-nav me-3">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>" style="color: #2B80B9;">Início</a>
                        </li>
                        <?php
                        $blog = new Blog();
                        $categories = $blog->getCategories();
                        foreach ($categories as $category):
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $category['slug']; ?>" style="color: #2B80B9;">
                                <?php echo $category['name']; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <!-- Search Form -->
                    <form class="d-flex" method="GET" action="<?php echo SITE_URL; ?>search.php">
                        <input class="form-control me-2" type="search" name="q" placeholder="Buscar..." value="<?php echo isset($_GET['q']) ? sanitizeInput($_GET['q']) : ''; ?>">
                        <button class="btn" type="submit" style="background-color: #2B80B9; color: white; border-color: #2B80B9;">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="container my-4">