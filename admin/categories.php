<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Incluir arquivos necessários
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/Blog.php';

// Verificar se as funções existem
if (!function_exists('sanitizeInput')) {
    die('Erro: Função sanitizeInput não encontrada!');
}
if (!function_exists('createSlug')) {
    die('Erro: Função createSlug não encontrada!');
}

$db = Database::getInstance()->getConnection();
$error = '';
$success = '';

// Verificar mensagem de sucesso da URL
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success = "Operação realizada com sucesso!";
}

// Função para fazer upload de imagem
function uploadImage($file) {
    $upload_dir = '../uploads/';
    
    // Verificar se o diretório existe
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
    
    // Verificar tamanho (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Arquivo muito grande! Máximo: 5MB'];
    }
    
    $filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Erro ao fazer upload da imagem!'];
    }
}

// ADICIONAR CATEGORIA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $background_image = null;
    
    if ($name) {
        $slug = createSlug($name);
        
        // Verificar se o slug já existe
        $stmt = $db->prepare("SELECT id FROM oc_blog_categories WHERE slug = ?");
        $stmt->execute([$slug]);
        
        if ($stmt->fetch()) {
            $error = "Já existe uma categoria com este nome!";
        } else {
            // Processar upload de imagem se fornecida
            if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
                $upload_result = uploadImage($_FILES['background_image']);
                if ($upload_result['success']) {
                    $background_image = $upload_result['filename'];
                } else {
                    $error = $upload_result['error'];
                }
            }
            
            if (!$error) {
                // Inserir categoria
                $stmt = $db->prepare("INSERT INTO oc_blog_categories (name, slug, description, background_image) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$name, $slug, $description, $background_image])) {
                    header('Location: categories.php?success=1');
                    exit;
                } else {
                    $error = "Erro ao adicionar categoria!";
                    // Remover imagem se houve erro na inserção
                    if ($background_image && file_exists('../uploads/' . $background_image)) {
                        unlink('../uploads/' . $background_image);
                    }
                }
            }
        }
    } else {
        $error = "Nome da categoria é obrigatório!";
    }
}

// EDITAR CATEGORIA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_category'])) {
    $id = (int)$_POST['id'];
    $name = sanitizeInput($_POST['name'] ?? '');
    $description = sanitizeInput($_POST['description'] ?? '');
    $remove_image = isset($_POST['remove_image']) && $_POST['remove_image'] === '1';
    
    if ($name && $id > 0) {
        // Buscar categoria atual
        $stmt = $db->prepare("SELECT background_image FROM oc_blog_categories WHERE id = ?");
        $stmt->execute([$id]);
        $current_category = $stmt->fetch();
        $old_image = $current_category['background_image'];
        $background_image = $old_image;
        
        // Se marcou para remover imagem
        if ($remove_image) {
            $background_image = null;
        }
        
        // Processar nova imagem se fornecida
        if (!$remove_image && isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadImage($_FILES['background_image']);
            if ($upload_result['success']) {
                $background_image = $upload_result['filename'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        if (!$error) {
            $stmt = $db->prepare("UPDATE oc_blog_categories SET name = ?, description = ?, background_image = ? WHERE id = ?");
            if ($stmt->execute([$name, $description, $background_image, $id])) {
                // Remover imagem antiga se necessário
                if ($old_image && ($remove_image || ($background_image && $background_image !== $old_image))) {
                    $old_image_path = '../uploads/' . $old_image;
                    if (file_exists($old_image_path)) {
                        unlink($old_image_path);
                    }
                }
                
                header('Location: categories.php?success=1');
                exit;
            } else {
                $error = "Erro ao atualizar categoria!";
                // Remover nova imagem se houve erro
                if ($background_image && $background_image !== $old_image && file_exists('../uploads/' . $background_image)) {
                    unlink('../uploads/' . $background_image);
                }
            }
        }
    } else {
        $error = "Nome da categoria é obrigatório!";
    }
}

// ELIMINAR CATEGORIA
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $category_id = (int)$_GET['delete'];
    
    // Verificar se há posts associados
    $stmt = $db->prepare("SELECT COUNT(*) FROM oc_blog_posts WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $post_count = $stmt->fetchColumn();
    
    if ($post_count > 0) {
        $error = "Não é possível eliminar esta categoria pois tem {$post_count} post(s) associado(s)!";
    } else {
        // Buscar imagem para remover
        $stmt = $db->prepare("SELECT background_image FROM oc_blog_categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $category_to_delete = $stmt->fetch();
        
        $stmt = $db->prepare("DELETE FROM oc_blog_categories WHERE id = ?");
        if ($stmt->execute([$category_id])) {
            // Remover imagem associada
            if ($category_to_delete && $category_to_delete['background_image']) {
                $image_path = '../uploads/' . $category_to_delete['background_image'];
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            header('Location: categories.php?success=1');
            exit;
        } else {
            $error = "Erro ao eliminar categoria!";
        }
    }
}

// BUSCAR CATEGORIA PARA EDIÇÃO
$edit_category = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $stmt = $db->prepare("SELECT * FROM oc_blog_categories WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_category = $stmt->fetch();
}

// BUSCAR TODAS AS CATEGORIAS
$stmt = $db->prepare("SELECT c.*, COUNT(p.id) as post_count FROM oc_blog_categories c LEFT JOIN oc_blog_posts p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestão de Categorias - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>
            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Gestão de Categorias</h1>
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
                
                <div class="row">
                    <!-- Formulário -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-<?= $edit_category ? 'edit' : 'plus' ?>"></i>
                                    <?= $edit_category ? 'Editar' : 'Adicionar' ?> Categoria
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($edit_category): ?>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Editando: <strong><?= htmlspecialchars($edit_category['name']) ?></strong>
                                        <a href="categories.php" class="btn btn-sm btn-secondary float-end">Cancelar</a>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="POST" enctype="multipart/form-data">
                                    <?php if ($edit_category): ?>
                                        <input type="hidden" name="id" value="<?= $edit_category['id'] ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Nome *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="<?= $edit_category ? htmlspecialchars($edit_category['name']) : '' ?>" 
                                               required maxlength="100">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Descrição</label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="3" maxlength="500"><?= $edit_category ? htmlspecialchars($edit_category['description']) : '' ?></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="background_image" class="form-label">Imagem de Background</label>
                                        
                                        <?php if ($edit_category && $edit_category['background_image']): ?>
                                            <div class="mb-2">
                                                <img src="../uploads/<?= htmlspecialchars($edit_category['background_image']) ?>" 
                                                     alt="Background atual" class="img-thumbnail d-block" 
                                                     style="max-width: 100%; max-height: 150px; object-fit: cover;">
                                                <div class="form-check mt-2">
                                                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                                                    <label class="form-check-label" for="remove_image">
                                                        <i class="fas fa-trash text-danger"></i> Remover imagem atual
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <input type="file" class="form-control" id="background_image" name="background_image" 
                                               accept="image/jpeg,image/jpg,image/png,image/webp">
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i> 
                                            Recomendado: 1200x400px (formato landscape). Máximo: 5MB. 
                                            Formatos: JPG, PNG, WebP
                                        </small>
                                    </div>
                                    
                                    <button type="submit" name="<?= $edit_category ? 'edit_category' : 'add_category' ?>" 
                                            class="btn btn-primary w-100">
                                        <i class="fas fa-<?= $edit_category ? 'save' : 'plus' ?>"></i>
                                        <?= $edit_category ? 'Atualizar' : 'Adicionar' ?> Categoria
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Lista de Categorias -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list"></i> Lista de Categorias (<?= count($categories) ?>)
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($categories)): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Nenhuma categoria encontrada.</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Imagem</th>
                                                    <th>Nome</th>
                                                    <th>Slug</th>
                                                    <th>Posts</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($categories as $category): ?>
                                                    <tr>
                                                        <td>
                                                            <?php if ($category['background_image']): ?>
                                                                <img src="../uploads/<?= htmlspecialchars($category['background_image']) ?>" 
                                                                     alt="<?= htmlspecialchars($category['name']) ?>" 
                                                                     class="img-thumbnail" 
                                                                     style="width: 60px; height: 40px; object-fit: cover;">
                                                            <?php else: ?>
                                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                                     style="width: 60px; height: 40px; border-radius: 4px;">
                                                                    <i class="fas fa-image text-muted"></i>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong><?= htmlspecialchars($category['name']) ?></strong>
                                                            <?php if ($category['description']): ?>
                                                                <br><small class="text-muted"><?= htmlspecialchars(substr($category['description'], 0, 50)) ?><?= strlen($category['description']) > 50 ? '...' : '' ?></small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><code><?= htmlspecialchars($category['slug']) ?></code></td>
                                                        <td>
                                                            <span class="badge bg-<?= $category['post_count'] > 0 ? 'primary' : 'secondary' ?>">
                                                                <?= $category['post_count'] ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <a href="categories.php?edit=<?= $category['id'] ?>" 
                                                                   class="btn btn-outline-primary" title="Editar">
                                                                    <i class="fas fa-edit"></i>
                                                                </a>
                                                                <a href="categories.php?delete=<?= $category['id'] ?>" 
                                                                   class="btn btn-outline-danger" 
                                                                   onclick="return confirm('Tem certeza que deseja eliminar esta categoria?')" 
                                                                   title="Eliminar">
                                                                    <i class="fas fa-trash"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>