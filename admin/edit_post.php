<?php
session_start();
require_once '../config/config.php';
require_once '../includes/Blog.php';

// Verificar se o usuário está logado como admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Obter ID do post
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$post_id) {
    header('Location: posts.php?error=Post não encontrado');
    exit;
}

// Instanciar classes
$blog = new Blog();
$database = new Database();
$db = $database->getConnection();

// Buscar post
$stmt = $db->prepare("SELECT * FROM oc_blog_posts WHERE id = ?");
$stmt->execute([$post_id]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('Location: posts.php?error=Post não encontrado');
    exit;
}

// Buscar categorias
$stmt = $db->prepare("SELECT * FROM oc_blog_categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar tags
$stmt = $db->prepare("SELECT * FROM oc_blog_tags ORDER BY name");
$stmt->execute();
$tags = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar tags do post
$stmt = $db->prepare("SELECT tag_id FROM oc_blog_post_tags WHERE post_id = ?");
$stmt->execute([$post_id]);
$post_tags = $stmt->fetchAll(PDO::FETCH_COLUMN);

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
        
        // Verificar se slug já existe (exceto para o post atual)
        $stmt = $db->prepare("SELECT id FROM oc_blog_posts WHERE slug = ? AND id != ?");
        $stmt->execute([$slug, $post_id]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        // Upload de imagem
        $image = $post['image']; // Manter imagem atual por padrão
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = uploadPostImage($_FILES['image']);
            if ($upload_result['success']) {
                // Remover imagem antiga
                if ($post['image'] && file_exists('../uploads/' . $post['image'])) {
                    unlink('../uploads/' . $post['image']);
                }
                $image = $upload_result['filename'];
            } else {
                $error = $upload_result['error'];
            }
        }
        
        if (!$error) {
            try {
                $db->beginTransaction();
                
                // Atualizar post
                $stmt = $db->prepare("
                    UPDATE oc_blog_posts 
                    SET title = ?, slug = ?, content = ?, excerpt = ?, image = ?, 
                        category_id = ?, status = ?, featured = ?, meta_title = ?, 
                        meta_description = ?, updated_at = NOW() 
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $title, $slug, $content, $excerpt, $image,
                    $category_id, $status, $featured, $meta_title,
                    $meta_description, $post_id
                ]);
                
                // Remover tags antigas
                $stmt = $db->prepare("DELETE FROM oc_blog_post_tags WHERE post_id = ?");
                $stmt->execute([$post_id]);
                
                // Inserir novas tags
                if (!empty($selected_tags)) {
                    $stmt = $db->prepare("INSERT INTO oc_blog_post_tags (post_id, tag_id) VALUES (?, ?)");
                    foreach ($selected_tags as $tag_id) {
                        $stmt->execute([$post_id, $tag_id]);
                    }
                }
                
                $db->commit();
                
                // Atualizar dados do post em memória
                $post['title'] = $title;
                $post['content'] = $content;
                $post['excerpt'] = $excerpt;
                $post['image'] = $image;
                $post['category_id'] = $category_id;
                $post['status'] = $status;
                $post['featured'] = $featured;
                $post['meta_title'] = $meta_title;
                $post['meta_description'] = $meta_description;
                $post_tags = $selected_tags;
                
                $success = 'Post atualizado com sucesso!';
                
            } catch (Exception $e) {
                $db->rollBack();
                $error = 'Erro ao atualizar post: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Por favor, preencha todos os campos obrigatórios.';
    }
}

include 'includes/header.php';
?>

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="pt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="posts.php"><i class="fas fa-file-alt"></i> Posts</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Post</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-edit text-primary"></i> 
                    Editar Post: <?= htmlspecialchars($post['title']) ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="posts.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar aos Posts
                        </a>
                        <?php if ($post['status'] === 'published'): ?>
                            <a href="../post.php?slug=<?= urlencode($post['slug']) ?>" 
                               class="btn btn-outline-info" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Visualizar Post
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Mensagens -->
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" id="editPostForm">
                <div class="row">
                    <!-- Conteúdo Principal -->
                    <div class="col-lg-8">
                        <!-- Informações Básicas -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Informações do Post</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Título *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= htmlspecialchars($post['title']) ?>" 
                                           required maxlength="200" placeholder="Digite o título do post...">
                                    <div class="form-text">
                                        <span id="titleCount">0</span>/200 caracteres
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="excerpt" class="form-label">Resumo</label>
                                    <textarea class="form-control" id="excerpt" name="excerpt" 
                                              rows="3" maxlength="300" 
                                              placeholder="Breve descrição do post..."><?= htmlspecialchars($post['excerpt']) ?></textarea>
                                    <div class="form-text">
                                        <span id="excerptCount">0</span>/300 caracteres
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">Conteúdo *</label>
                                    <textarea class="form-control summernote" id="content" name="content" 
                                              required placeholder="Escreva o conteúdo do post..."><?= $post['content'] ?></textarea>
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
                                           value="<?= htmlspecialchars($post['meta_title']) ?>" 
                                           maxlength="60" placeholder="Título para motores de busca...">
                                    <div class="form-text">
                                        Deixe vazio para usar o título do post. 
                                        <span id="metaTitleCount">0</span>/60 caracteres.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="meta_description" class="form-label">Meta Descrição</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" 
                                              rows="2" maxlength="160" 
                                              placeholder="Descrição para motores de busca..."><?= htmlspecialchars($post['meta_description']) ?></textarea>
                                    <div class="form-text">
                                        Descrição que aparece nos resultados de busca. 
                                        <span id="metaDescCount">0</span>/160 caracteres.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sidebar -->
                    <div class="col-lg-4">
                        <!-- Configurações -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-cog"></i> Configurações</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Rascunho</option>
                                        <option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Publicado</option>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Categoria *</label>
                                    <select class="form-select" id="category_id" name="category_id" required>
                                        <option value="">Selecione uma categoria...</option>
                                        <?php foreach ($categories as $category): ?>
                                            <option value="<?= $category['id'] ?>" 
                                                    <?= $post['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($category['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                                           <?= $post['featured'] ? 'checked' : '' ?>>
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
                                <?php if ($post['image']): ?>
                                    <div class="mb-3">
                                        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" 
                                             alt="Imagem atual" class="img-fluid rounded">
                                        <div class="form-text mt-2">
                                            <i class="fas fa-info-circle"></i> Imagem atual
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="image" name="image" 
                                           accept="image/jpeg,image/jpg,image/png,image/webp">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle"></i> 
                                        <?= $post['image'] ? 'Selecione para alterar a imagem atual.' : 'Recomendado: 800x400px.' ?>
                                        Máximo: 10MB. Formatos: JPG, PNG, WebP
                                    </div>
                                </div>
                                
                                <!-- Preview da nova imagem -->
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
                                                       <?= in_array($tag['id'], $post_tags) ? 'checked' : '' ?>>
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
                                <button type="submit" class="btn btn-primary w-100 mb-2" id="saveBtn">
                                    <i class="fas fa-save"></i> Atualizar Post
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

<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
<script>
// Contadores de caracteres
function updateCharCount(inputId, countId, maxLength) {
    const input = document.getElementById(inputId);
    const counter = document.getElementById(countId);
    
    function update() {
        const length = input.value.length;
        counter.textContent = length;
        counter.className = length > maxLength * 0.9 ? 'text-warning' : '';
        if (length > maxLength) counter.className = 'text-danger';
    }
    
    input.addEventListener('input', update);
    update();
}

// Inicializar contadores
updateCharCount('title', 'titleCount', 200);
updateCharCount('excerpt', 'excerptCount', 300);
updateCharCount('meta_title', 'metaTitleCount', 60);
updateCharCount('meta_description', 'metaDescCount', 160);

// Preview de imagem
document.getElementById('image').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});

function removeImagePreview() {
    document.getElementById('image').value = '';
    document.getElementById('imagePreview').classList.add('d-none');
    document.getElementById('previewImg').src = '';
}

// Auto-save no localStorage
let autoSaveInterval;
function startAutoSave() {
    autoSaveInterval = setInterval(() => {
        const formData = new FormData(document.getElementById('editPostForm'));
        const data = {};
        for (let [key, value] of formData.entries()) {
            if (key !== 'image') { // Não salvar arquivo
                data[key] = value;
            }
        }
        localStorage.setItem('edit_post_backup_<?= $post_id ?>', JSON.stringify(data));
        console.log('📝 Auto-save realizado');
    }, 30000); // A cada 30 segundos
}

// Restaurar backup se existir
function restoreBackup() {
    const backup = localStorage.getItem('edit_post_backup_<?= $post_id ?>');
    if (backup) {
        const data = JSON.parse(backup);
        const shouldRestore = confirm('🔄 Encontrado backup automático. Deseja restaurar?');
        if (shouldRestore) {
            Object.keys(data).forEach(key => {
                const element = document.querySelector(`[name="${key}"]`);
                if (element) {
                    if (element.type === 'checkbox') {
                        element.checked = data[key] === 'on';
                    } else {
                        element.value = data[key];
                    }
                }
            });
        }
    }
}

// Limpar backup ao salvar
document.getElementById('editPostForm').addEventListener('submit', function() {
    localStorage.removeItem('edit_post_backup_<?= $post_id ?>');
    
    // Loading state
    const saveBtn = document.getElementById('saveBtn');
    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    saveBtn.disabled = true;
});

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    restoreBackup();
    startAutoSave();
    
    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        // Ctrl+S para salvar
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            document.getElementById('editPostForm').submit();
        }
    });
});

// Limpar interval ao sair
window.addEventListener('beforeunload', function() {
    if (autoSaveInterval) {
        clearInterval(autoSaveInterval);
    }
});
</script>

<?php include 'includes/footer.php'; ?>