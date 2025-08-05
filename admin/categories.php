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

$error = '';
$success = '';

// Adicionar categoria
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    
    if ($name) {
        $slug = createSlug($name);
        
        // Verificar se slug já existe
        $stmt = $db->prepare("SELECT id FROM oc_blog_categories WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        $stmt = $db->prepare("INSERT INTO oc_blog_categories (name, slug, description) VALUES (?, ?, ?)");
        if ($stmt->execute([$name, $slug, $description])) {
            $success = "Categoria adicionada com sucesso!";
        } else {
            $error = "Erro ao adicionar categoria!";
        }
    } else {
        $error = "Nome da categoria é obrigatório!";
    }
}

// Eliminar categoria
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    // Verificar se há posts nesta categoria
    $stmt = $db->prepare("SELECT COUNT(*) FROM oc_blog_posts WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $post_count = $stmt->fetchColumn();
    
    if ($post_count > 0) {
        $error = "Não é possível eliminar categoria com posts associados!";
    } else {
        $stmt = $db->prepare("DELETE FROM oc_blog_categories WHERE id = ?");
        if ($stmt->execute([$category_id])) {
            $success = "Categoria eliminada com sucesso!";
        }
    }
}

// Buscar categorias
$categories = $blog->getCategories();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestão de Categorias</h1>
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
            
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Adicionar Nova Categoria</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome *</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Descrição</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                
                                <button type="submit" name="add_category" class="btn btn-primary w-100">
                                    <i class="fas fa-plus"></i> Adicionar Categoria
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Lista de Categorias</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Slug</th>
                                            <th>Posts</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                        <?php
                                        // Contar posts da categoria
                                        $stmt = $db->prepare("SELECT COUNT(*) FROM oc_blog_posts WHERE category_id = ?");
                                        $stmt->execute([$category['id']]);
                                        $post_count = $stmt->fetchColumn();
                                        ?>
                                        <tr>
                                            <td><?= $category['id'] ?></td>
                                            <td>
                                                <strong><?= htmlspecialchars($category['name']) ?></strong>
                                                <?php if ($category['description']): ?>
                                                    <br><small class="text-muted"><?= htmlspecialchars($category['description']) ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td><code><?= $category['slug'] ?></code></td>
                                            <td><?= $post_count ?></td>
                                            <td>
                                                <a href="../category.php?slug=<?= $category['slug'] ?>" class="btn btn-sm btn-info" target="_blank">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($post_count === 0): ?>
                                                    <a href="categories.php?delete=<?= $category['id'] ?>" class="btn btn-sm btn-danger delete-btn">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>