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

// Paginação
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$total_posts = $blog->countAllPosts(); // Usar o novo método
$total_pages = ceil($total_posts / $per_page);

// Buscar posts
$posts = $blog->getAllPosts($page, $per_page); // Usar o novo método

// Eliminar post
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    $stmt = $db->prepare("DELETE FROM oc_blog_posts WHERE id = ?");
    if ($stmt->execute([$post_id])) {
        $success = "Post eliminado com sucesso!";
        header('Location: posts.php');
        exit;
    }
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestão de Posts</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="add_post.php" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-plus"></i> Novo Post
                    </a>
                </div>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lista de Posts (<?= $total_posts ?> total)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>Categoria</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Visualizações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($posts as $post): ?>
                                <tr>
                                    <td><?= $post['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($post['title']) ?></strong>
                                        <?php if ($post['featured']): ?>
                                            <span class="badge bg-warning">Destaque</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($post['category_name']) ?></td>
                                    <td><?= formatDate($post['created_at']) ?></td>
                                    <td>
                                        <?php if ($post['status'] === 'published'): ?>
                                            <span class="badge bg-success">Publicado</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Rascunho</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $post['views'] ?></td>
                                    <td>
                                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="../post.php?slug=<?= $post['slug'] ?>" class="btn btn-sm btn-info" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="posts.php?delete=<?= $post['id'] ?>" class="btn btn-sm btn-danger delete-btn">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Paginação">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="posts.php?page=<?= $page - 1 ?>">Anterior</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="posts.php?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="posts.php?page=<?= $page + 1 ?>">Próximo</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>