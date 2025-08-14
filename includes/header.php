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
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo SITE_URL; ?>assets/images/favicon.png">
    <link rel="shortcut icon" type="image/png" href="<?php echo SITE_URL; ?>assets/images/favicon.png">
    <link rel="apple-touch-icon" href="<?php echo SITE_URL; ?>assets/images/favicon.png">
    
    <!-- Google Fonts - Crimson Text (similar to Minion Pro) as fallback -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo SITE_URL; ?>assets/css/style.css" rel="stylesheet">
    <link href="<?php echo SITE_URL; ?>assets/css/dropdown-styles.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <!-- Sempre carregar os recursos da calculadora -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/imc-calculator.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/sleep-quiz.css">
    <script src="<?php echo SITE_URL; ?>assets/js/imc-calculator.js"></script>
    <script src="<?php echo SITE_URL; ?>assets/js/sleep-quiz.js"></script>
    
    <!-- Meta Pixel Code -->
    <script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '677179405379656');
    fbq('track', 'PageView');
    </script>
    <noscript><img height="1" width="1" style="display:none"
    src="https://www.facebook.com/tr?id=677179405379656&ev=PageView&noscript=1"
    /></noscript>
    <!-- End Meta Pixel Code -->
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2R8NYD78PR"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-2R8NYD78PR');
    </script>
    <!-- End Google Analytics -->
</head>
<body<?php echo (isset($body_class) && $body_class === 'post-page') ? ' data-page="post"' : ''; ?>>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light" style="background-color: #f8f9fa;">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo SITE_URL; ?>" style="color: #2B80B9;">
                <img src="<?php echo SITE_URL; ?>/uploads/logo-myformula-colors.png" alt="MyFormula Logo" height="45" class="me-2">

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
                                    'digestivo' => 'fas fa-pills', // ou 'fas fa-pills' ou 'fas fa-capsules'
                                    'geral' => 'fas fa-newspaper',
                                    'tecnologia' => 'fas fa-laptop-code',
                                    'programacao' => 'fas fa-code',
                                    'design' => 'fas fa-paint-brush',
                                    'marketing' => 'fas fa-bullhorn'
                                ];
                                
                                foreach ($categories as $cat_item):
                                    $icon = $category_icons[$cat_item['slug']] ?? 'fas fa-folder';
                                    
                                    // Buscar número de posts por categoria
                                    $post_count = $blog->countPostsByCategory($cat_item['id']);
                                ?>
                                <li>
                                    <a class="dropdown-item d-flex justify-content-between align-items-center" 
                                       href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $cat_item['slug']; ?>">
                                        <span>
                                            <i class="<?php echo $icon; ?> me-2"></i>
                                            <?php echo htmlspecialchars($cat_item['name']); ?>
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
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin" style="color: #2B80B9;" title="Painel Administrativo">
                                <i class="fas fa-cog me-1"></i>Admin
                            </a>
                        </li> -->
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
    
    <!-- Banner Principal -->
    <section class="banner-section" style="width: 100%; margin: 0; padding: 0;">
        <div class="banner-container" style="position: relative; width: 100%; height: 300px; overflow: hidden;">
            <!-- Imagem para desktop -->
            <img src="<?php echo SITE_URL; ?>uploads/bannerblog.jpg" 
                 alt="Banner MyFormula Blog" 
                 class="d-none d-md-block"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
            <!-- Imagem para mobile -->
            <img src="<?php echo SITE_URL; ?>uploads/bannermenor.jpg" 
                 alt="Banner MyFormula Blog" 
                 class="d-block d-md-none"
                 style="width: 100%; height: 100%; object-fit: cover; display: block;">
            <div class="banner-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(45deg, rgba(43, 128, 185, 0.3), rgba(43, 128, 185, 0.1)); display: flex; align-items: center; justify-content: center;">
                <div class="banner-content text-center">
                    <a href="<?php echo SITE_URL; ?>" style="text-decoration: none; color: inherit;">
                        <h1 class="display-4 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5); color: white; font-family: math; transition: opacity 0.3s ease;" onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'"><?php echo BLOG_TITLE; ?></h1>
                    </a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Main Content -->
    <main class="container my-4">
