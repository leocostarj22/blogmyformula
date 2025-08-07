<?php
session_start(); // Adicionar esta linha
require_once '../config/config.php';
require_once '../includes/Blog.php';

// Verificar se o usuário está logado como admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Inicializar variáveis
$success = '';
$error = '';

// Instanciar classes
$blog = new Blog();
$database = new Database();
$db = $database->getConnection();

// Processar eliminação de post
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    
    try {
        $db->beginTransaction();
        
        // Buscar imagem do post para eliminar
        $stmt = $db->prepare("SELECT image FROM oc_blog_posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($post) {
            // Eliminar tags do post
            $stmt = $db->prepare("DELETE FROM oc_blog_post_tags WHERE post_id = ?");
            $stmt->execute([$post_id]);
            
            // Eliminar comentários do post
            $stmt = $db->prepare("DELETE FROM oc_blog_comments WHERE post_id = ?");
            $stmt->execute([$post_id]);
            
            // Eliminar post
            $stmt = $db->prepare("DELETE FROM oc_blog_posts WHERE id = ?");
            $stmt->execute([$post_id]);
            
            // Eliminar imagem se existir
            if ($post['image'] && file_exists('../uploads/' . $post['image'])) {
                unlink('../uploads/' . $post['image']);
            }
            
            $db->commit();
            $success = 'Post eliminado com sucesso!';
        } else {
            $error = 'Post não encontrado!';
        }
    } catch (Exception $e) {
        $db->rollBack();
        $error = 'Erro ao eliminar post: ' . $e->getMessage();
    }
}

// Processar mensagens da URL
if (isset($_GET['success'])) {
    $success = $_GET['success'];
}
if (isset($_GET['error'])) {
    $error = $_GET['error'];
}

// Filtros e pesquisa
$search = $_GET['search'] ?? '';
$category_filter = $_GET['category'] ?? '';
$status_filter = $_GET['status'] ?? '';
$featured_filter = $_GET['featured'] ?? '';

// Paginação
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$posts_per_page = 10;
$offset = ($page - 1) * $posts_per_page;

// Construir query com filtros
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(p.title LIKE ? OR p.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($category_filter)) {
    $where_conditions[] = "p.category_id = ?";
    $params[] = $category_filter;
}

if (!empty($status_filter)) {
    $where_conditions[] = "p.status = ?";
    $params[] = $status_filter;
}

if ($featured_filter !== '') {
    $where_conditions[] = "p.featured = ?";
    $params[] = (int)$featured_filter;
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Contar total de posts
$count_query = "SELECT COUNT(*) FROM oc_blog_posts p $where_clause";
$stmt = $db->prepare($count_query);
$stmt->execute($params);
$total_posts = $stmt->fetchColumn();
$total_pages = ceil($total_posts / $posts_per_page);

// Buscar posts com paginação
$query = "
    SELECT p.*, c.name as category_name,
           (SELECT COUNT(*) FROM oc_blog_comments WHERE post_id = p.id AND status = 'approved') as comment_count
    FROM oc_blog_posts p 
    LEFT JOIN oc_blog_categories c ON p.category_id = c.id 
    $where_clause
    ORDER BY p.created_at DESC 
    LIMIT $posts_per_page OFFSET $offset
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar categorias para filtro
$stmt = $db->prepare("SELECT * FROM oc_blog_categories ORDER BY name");
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estatísticas rápidas
$stats_query = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
        SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as drafts,
        SUM(CASE WHEN featured = 1 THEN 1 ELSE 0 END) as featured
    FROM oc_blog_posts
";
$stmt = $db->prepare($stats_query);
$stmt->execute();
$stats = $stmt->fetch(PDO::FETCH_ASSOC);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-file-alt text-primary"></i> 
                    Gestão de Posts
                    <small class="text-muted">(<?= $total_posts ?> posts)</small>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_post.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Post
                    </a>
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

            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?= $stats['total'] ?></h4>
                                    <p class="mb-0">Total de Posts</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-file-alt fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?= $stats['published'] ?></h4>
                                    <p class="mb-0">Publicados</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-eye fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?= $stats['drafts'] ?></h4>
                                    <p class="mb-0">Rascunhos</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-edit fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4><?= $stats['featured'] ?></h4>
                                    <p class="mb-0">Em Destaque</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-star fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros e Pesquisa</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Pesquisar</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= htmlspecialchars($search) ?>" placeholder="Título ou conteúdo...">
                        </div>
                        <div class="col-md-2">
                            <label for="category" class="form-label">Categoria</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Todas</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="published" <?= $status_filter == 'published' ? 'selected' : '' ?>>Publicado</option>
                                <option value="draft" <?= $status_filter == 'draft' ? 'selected' : '' ?>>Rascunho</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="featured" class="form-label">Destaque</label>
                            <select class="form-select" id="featured" name="featured">
                                <option value="">Todos</option>
                                <option value="1" <?= $featured_filter === '1' ? 'selected' : '' ?>>Em Destaque</option>
                                <option value="0" <?= $featured_filter === '0' ? 'selected' : '' ?>>Normal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filtrar
                                </button>
                                <a href="posts.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Lista de Posts -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Posts</h5>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($posts)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum post encontrado</h5>
                            <p class="text-muted">Comece criando seu primeiro post!</p>
                            <a href="add_post.php" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Criar Post
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">ID</th>
                                        <th>Título</th>
                                        <th width="120">Categoria</th>
                                        <th width="100">Status</th>
                                        <th width="80">Destaque</th>
                                        <th width="100">Data</th>
                                        <th width="80">Comentários</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($posts as $post): ?>
                                        <tr>
                                            <td><?= $post['id'] ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($post['image']): ?>
                                                        <img src="../uploads/<?= htmlspecialchars($post['image']) ?>" 
                                                             alt="" class="rounded me-2" width="40" height="30" style="object-fit: cover;">
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong><?= htmlspecialchars($post['title']) ?></strong>
                                                        <?php if ($post['featured']): ?>
                                                            <i class="fas fa-star text-warning ms-1" title="Post em destaque"></i>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($post['category_name']): ?>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($post['category_name']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">Sem categoria</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($post['status'] == 'published'): ?>
                                                    <span class="badge bg-success">Publicado</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Rascunho</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($post['featured']): ?>
                                                    <i class="fas fa-star text-warning"></i>
                                                <?php else: ?>
                                                    <i class="far fa-star text-muted"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y', strtotime($post['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $post['comment_count'] ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="edit_post.php?id=<?= $post['id'] ?>" 
                                                       class="btn btn-outline-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="../post.php?slug=<?= htmlspecialchars($post['slug']) ?>" 
                                                       class="btn btn-outline-info" title="Ver" target="_blank">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-outline-danger delete-btn" 
                                                            data-post-id="<?= $post['id'] ?>" 
                                                            data-post-title="<?= htmlspecialchars($post['title']) ?>" 
                                                            title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

            <!-- Paginação -->
            <?php if ($total_pages > 1): ?>
                <nav aria-label="Paginação" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category_filter) ?>&status=<?= urlencode($status_filter) ?>&featured=<?= urlencode($featured_filter) ?>">
                                    <i class="fas fa-chevron-left"></i> Anterior
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category_filter) ?>&status=<?= urlencode($status_filter) ?>&featured=<?= urlencode($featured_filter) ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category_filter) ?>&status=<?= urlencode($status_filter) ?>&featured=<?= urlencode($featured_filter) ?>">
                                    Próxima <i class="fas fa-chevron-right"></i>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </main>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o post <strong id="postTitle"></strong>?</p>
                <p class="text-danger"><small>Esta ação não pode ser desfeita.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDelete" class="btn btn-danger">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal de eliminação
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    const deleteButtons = document.querySelectorAll('.delete-btn');
    const postTitle = document.getElementById('postTitle');
    const confirmDelete = document.getElementById('confirmDelete');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const postId = this.dataset.postId;
            const title = this.dataset.postTitle;
            
            postTitle.textContent = title;
            confirmDelete.href = `posts.php?delete=${postId}`;
            deleteModal.show();
        });
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>

<?php include 'includes/footer.php'; ?>