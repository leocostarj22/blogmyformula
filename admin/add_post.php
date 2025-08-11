<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/Blog.php';

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$blog = new Blog();
$db = Database::getInstance()->getConnection();

// Buscar categorias
$categories = $blog->getCategories();

// Buscar tags
$stmt = $db->query("SELECT * FROM oc_blog_tags ORDER BY name");
$tags = $stmt->fetchAll();

$error = '';
$success = '';

// Função para upload de imagem
function uploadPostImage($file) {
    $upload_dir = '../uploads/';
    
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    if (!is_writable($upload_dir)) {
        return ['success' => false, 'error' => 'Diretório de upload não tem permissões de escrita!'];
    }
    
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return ['success' => false, 'error' => 'Formato não permitido! Use: JPG, JPEG, PNG ou WebP'];
    }
    
    // Verificar tamanho (máximo 10MB)
    if ($file['size'] > 10 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Arquivo muito grande! Máximo: 10MB'];
    }
    
    $filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Erro ao fazer upload da imagem!'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitizeInput($_POST['title'] ?? '');
    $content = $_POST['content'] ?? '';
    $excerpt = sanitizeInput($_POST['excerpt'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $status = $_POST['status'] ?? 'draft';
    $featured = isset($_POST['featured']) ? 1 : 0;
    $meta_title = sanitizeInput($_POST['meta_title'] ?? '');
    $meta_description = sanitizeInput($_POST['meta_description'] ?? '');
    $selected_tags = $_POST['tags'] ?? [];
    
    if ($title && $content && $category_id) {
        $slug = createSlug($title);
        
        // Verificar se slug já existe
        $stmt = $db->prepare("SELECT id FROM oc_blog_posts WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        // Upload de imagem
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadPostImage($_FILES['image']);
            if ($upload_result['success']) {
                $image = $upload_result['filename'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        if (!$error) {
            try {
                $db->beginTransaction();
                
                // Inserir post
                $stmt = $db->prepare("
                    INSERT INTO oc_blog_posts (title, slug, content, excerpt, image, category_id, status, featured, meta_title, meta_description, created_at, updated_at) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->execute([$title, $slug, $content, $excerpt, $image, $category_id, $status, $featured, $meta_title, $meta_description]);
                
                $post_id = $db->lastInsertId();
                
                // Inserir tags
                if (!empty($selected_tags)) {
                    $stmt = $db->prepare("INSERT INTO oc_blog_post_tags (post_id, tag_id) VALUES (?, ?)");
                    foreach ($selected_tags as $tag_id) {
                        $stmt->execute([$post_id, $tag_id]);
                    }
                }
                
                $db->commit();
                header('Location: posts.php?success=1');
                exit;
                
            } catch (Exception $e) {
                $db->rollBack();
                $error = "Erro ao criar post: " . $e->getMessage();
                // Remover imagem se houve erro
                if ($image && file_exists('../uploads/' . $image)) {
                    unlink('../uploads/' . $image);
                }
            }
        }
    } else {
        $error = "Por favor, preencha todos os campos obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Post - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Removido: link do Summernote -->
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2"><i class="fas fa-plus-circle"></i> Adicionar Novo Post</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="posts.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                
                <!-- Mensagens -->
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" id="addPostForm">
                    <div class="row">
                        <!-- Conteúdo Principal -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-edit"></i> Conteúdo do Post</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Título *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" 
                                               required maxlength="200" placeholder="Digite o título do post...">
                                        <div class="form-text">O título será usado para gerar automaticamente o slug da URL.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="excerpt" class="form-label">Resumo</label>
                                        <textarea class="form-control" id="excerpt" name="excerpt" 
                                                  rows="3" maxlength="300" 
                                                  placeholder="Breve descrição do post (opcional)..."><?= htmlspecialchars($_POST['excerpt'] ?? '') ?></textarea>
                                        <div class="form-text">Resumo que aparecerá na listagem de posts.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="content" class="form-label">Conteúdo *</label>
                                        <textarea class="form-control" id="content" name="content" rows="10" placeholder="Digite o conteúdo do post..."></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SEO -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-search"></i> Otimização SEO</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="meta_title" class="form-label">Meta Título</label>
                                        <input type="text" class="form-control" id="meta_title" name="meta_title" 
                                               value="<?= htmlspecialchars($_POST['meta_title'] ?? '') ?>" 
                                               maxlength="60" placeholder="Título para motores de busca...">
                                        <div class="form-text">Deixe vazio para usar o título do post. Máximo: 60 caracteres.</div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="meta_description" class="form-label">Meta Descrição</label>
                                        <textarea class="form-control" id="meta_description" name="meta_description" 
                                                  rows="2" maxlength="160" 
                                                  placeholder="Descrição para motores de busca..."><?= htmlspecialchars($_POST['meta_description'] ?? '') ?></textarea>
                                        <div class="form-text">Descrição que aparece nos resultados de busca. Máximo: 160 caracteres.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sidebar -->
                        <div class="col-lg-4">
                            <!-- Publicação -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-cog"></i> Configurações</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status">
                                            <option value="draft" <?= ($_POST['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
                                            <option value="published" <?= ($_POST['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">Categoria *</label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">Selecione uma categoria...</option>
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category['id'] ?>" 
                                                        <?= ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($category['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                                               <?= isset($_POST['featured']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="featured">
                                            <i class="fas fa-star text-warning"></i> Post em Destaque
                                        </label>
                                        <div class="form-text">Posts em destaque aparecem em posição de maior visibilidade.</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Imagem -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-image"></i> Imagem Destacada</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <input type="file" class="form-control" id="image" name="image" 
                                               accept="image/jpeg,image/jpg,image/png,image/webp">
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 
                                            Recomendado: 800x400px. Máximo: 10MB. 
                                            Formatos: JPG, PNG, WebP
                                        </div>
                                    </div>
                                    
                                    <!-- Preview da imagem -->
                                    <div id="imagePreview" class="d-none">
                                        <img id="previewImg" src="" alt="Preview" class="img-fluid rounded">
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2" onclick="removeImagePreview()">
                                            <i class="fas fa-times"></i> Remover
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Tags -->
                            <?php if (!empty($tags)): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0"><i class="fas fa-tags"></i> Tags</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($tags as $tag): ?>
                                            <div class="col-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="tag_<?= $tag['id'] ?>" name="tags[]" value="<?= $tag['id'] ?>"
                                                           <?= in_array($tag['id'], $_POST['tags'] ?? []) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="tag_<?= $tag['id'] ?>">
                                                        <?= htmlspecialchars($tag['name']) ?>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Botões -->
                            <div class="card">
                                <div class="card-body">
                                    <button type="submit" id="saveBtn" class="btn btn-primary w-100 mb-2">
                                        <i class="fas fa-save"></i> Criar Post
                                    </button>
                                    <a href="posts.php" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-times"></i> Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </main>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>