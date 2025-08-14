<?php
// Widget de Benefícios de Saúde - Layout de duas colunas
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'includes/Blog.php';

// Parâmetros configuráveis via GET
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 4;
$theme = isset($_GET['theme']) ? $_GET['theme'] : 'health';
$show_description = isset($_GET['description']) ? $_GET['description'] === 'true' : true;

$blog = new Blog();

// Buscar posts das categorias de saúde
$health_posts = $blog->getLatestPosts($limit);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicas sobre Saúde - <?php echo BLOG_TITLE; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: diatype, "diatype Fallback", Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #FFFFFF;
            min-height: 100vh;
        }
        
        .widget-container {
            /*max-width: 1200px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 15px;*/
            margin: 0 auto;
            background: white;
            padding: 5px;
        }
        
        .main-content {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }
        
        .left-section {
            flex: 0 0 350px;
            padding-right: 20px;
        }
        
        .right-section {
            flex: 1;
        }
        
        .main-title {
                font-family: diatype, "diatype Fallback", Arial, sans-serif;
                font-size: 46px;
                font-weight: 400;
                line-height: 48px;
                text-align: start;
                letter-spacing: -1.68px;
                color: #000000;
                width: 456px;
                height: 112px;
                margin-bottom: 60px;
                display: block;
                position: static;
            }
        
        .main-description {
            font-family: diatype, "diatype Fallback", Arial, sans-serif;
            font-size: 18px;
            font-weight: 400;
            line-height: 28px;
            text-align: start;
            letter-spacing: normal;
            color: #000000;
            width: 456px;
            height: 84px;
            display: block;
            position: static;
            margin-bottom: 0;
        }
        
        .posts-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            font-family: diatype, "diatype Fallback", Arial, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 25.6px;
            text-align: start;
            letter-spacing: normal;
            color: #000000;
        }
        
        .post-card {
            display: flex;
            background: white;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
        }
        
        .post-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
        }
        
        .post-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
            margin-right: 15px;
        }
        
        .post-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .post-title {
            font-family: diatype, "diatype Fallback", Arial, sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        
        .post-description {
            font-family: diatype, "diatype Fallback", Arial, sans-serif;
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.4;
            margin: 0;
        }
        
        .no-posts {
            font-family: diatype, "diatype Fallback", Arial, sans-serif;
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        /* Responsivo */
        @media (max-width: 768px) {
            .widget-container {
                padding: 10px;
            }
            
            .main-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .left-section {
                flex: none;
                padding-right: 0;
                text-align: left;
            }
            
            .main-title {
                font-size: 28px;
                line-height: 32px;
                letter-spacing: -1px;
                width: 100%;
                height: auto;
                margin-bottom: 20px;
            }
            
            .main-description {
                font-size: 16px;
                line-height: 24px;
                width: 100%;
                height: auto;
                margin-bottom: 20px;
            }
            
            .post-card {
                padding: 12px;
            }
            
            .post-image {
                width: 60px;
                height: 60px;
                margin-right: 12px;
            }
            
            .post-title {
                font-size: 1rem;
            }
            
            .post-description {
                font-size: 0.85rem;
            }
        }
        
        /* Tablets pequenos e celulares grandes */
        @media (max-width: 576px) {
            body {
                padding: 10px;
            }
            
            .widget-container {
                padding: 5px;
            }
            
            .main-content {
                gap: 15px;
            }
            
            .main-title {
                font-size: 24px;
                line-height: 28px;
                letter-spacing: -0.8px;
                margin-bottom: 15px;
            }
            
            .main-description {
                font-size: 14px;
                line-height: 22px;
                margin-bottom: 15px;
            }
            
            .posts-container {
                gap: 15px;
            }
            
            .post-card {
                padding: 10px;
                flex-direction: column;
                text-align: center;
            }
            
            .post-image {
                width: 50px;
                height: 50px;
                margin: 0 auto 10px auto;
            }
            
            .post-title {
                font-size: 0.9rem;
                margin-bottom: 5px;
            }
            
            .post-description {
                font-size: 0.8rem;
            }
        }
        
        /* Celulares muito pequenos */
        @media (max-width: 375px) {
            .main-title {
                font-size: 20px;
                line-height: 24px;
                letter-spacing: -0.6px;
            }
            
            .main-description {
                font-size: 13px;
                line-height: 20px;
            }
            
            .post-title {
                font-size: 0.85rem;
            }
            
            .post-description {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="widget-container">
        <div class="main-content">
            <!-- Seção Esquerda: Título e Descrição -->
            <div class="left-section">
                <h1 class="main-title">MyFormula: Informação e testes para o seu bem-estar.</h1>
                <p class="main-description">
                    No Blog MyFormula, você encontra artigos claros e objetivos sobre saúde, bem-estar e qualidade de vida. 
                    Faça também testes rápidos, como IMC e sono, e descubra mais sobre você.
                </p>
            </div>
            
            <!-- Seção Direita: Posts -->
            <div class="right-section">
                <div class="posts-container">
                    <?php if (empty($health_posts)): ?>
                        <div class="no-posts">
                            <i class="fas fa-info-circle mb-2" style="font-size: 2rem;"></i>
                            <p>Nenhum artigo encontrado no momento.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($health_posts as $post): ?>
                            <a href="<?php echo SITE_URL; ?>post.php?slug=<?php echo $post['slug']; ?>" 
                               class="post-card" 
                               target="_blank">
                                <!-- Imagem do Post -->
                                <?php if (!empty($post['image'])): ?>
                                    <img src="<?php echo SITE_URL; ?>uploads/<?php echo $post['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                                         class="post-image">
                                <?php else: ?>
                                    <div class="post-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Conteúdo do Post -->
                                <div class="post-content">
                                    <h3 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <?php if ($show_description && !empty($post['excerpt'])): ?>
                                        <p class="post-description">
                                            <?php echo htmlspecialchars(substr($post['excerpt'], 0, 100)) . (strlen($post['excerpt']) > 100 ? '...' : ''); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
