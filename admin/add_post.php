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
            $upload_dir = '../uploads/';
            $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $filename;
                }
            }
        }
        
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
            $success = "Post criado com sucesso!";
            
        } catch (Exception $e) {
            $db->rollBack();
            $error = "Erro ao criar post: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, preencha todos os campos obrigatórios!";
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Adicionar Novo Post</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="posts.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Conteúdo do Post</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Título *</label>
                                    <input type="text" class="form-control" id="title" name="title" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Resumo</label>
                                    <textarea class="form-control" id="excerpt" name="excerpt" rows="3"><?= htmlspecialchars($_POST['excerpt'] ?? '') ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">Conteúdo *</label>
                                    <textarea class="form-control summernote" id="content" name="content" required><?= $_POST['content'] ?? '' ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">SEO</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="meta_title" class="form-label">Meta Título</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="<?= htmlspecialchars($_POST['meta_title'] ?? '') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Descrição</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3"><?= htmlspecialchars($_POST['meta_description'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Publicação -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Publicação</h6>
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
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="featured" name="featured" <?= isset($_POST['featured']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="featured">
                                            Post em Destaque
                                        </label>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Salvar Post
                                </button>
                            </div>
                        </div>
                        
                        <!-- Categoria -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Categoria</h6>
                            </div>
                            <div class="card-body">
                                <select class="form-select" name="category_id" required>
                                    <option value="">Selecionar categoria...</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= ($_POST['category_id'] ?? '') == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Tags -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Tags</h6>
                            </div>
                            <div class="card-body">
                                <?php foreach ($tags as $tag): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="tag_<?= $tag['id'] ?>" name="tags[]" value="<?= $tag['id'] ?>" <?= in_array($tag['id'], $_POST['tags'] ?? []) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="tag_<?= $tag['id'] ?>">
                                            <?= htmlspecialchars($tag['name']) ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- Imagem -->
                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Imagem Destacada</h6>
                            </div>
                            <div class="card-body">
                                <input type="file" class="form-control" name="image" accept="image/*">
                                <small class="form-text text-muted">Formatos aceites: JPG, PNG, GIF, WebP</small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>