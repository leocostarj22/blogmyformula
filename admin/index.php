<?php
require_once '../config/database.php';
require_once '../config/config.php';
require_once '../includes/Blog.php';

session_start();

// Verificar se está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$blog = new Blog();
$db = Database::getInstance()->getConnection();

// Estatísticas do dashboard - CORRIGIDO: mostrar todos os posts no admin
$stats = [
    'total_posts' => $blog->countAllPosts(), // Mudança aqui
    'total_categories' => $db->query("SELECT COUNT(*) FROM oc_blog_categories")->fetchColumn(),
    'total_comments' => $db->query("SELECT COUNT(*) FROM oc_blog_comments WHERE status = 'approved'")->fetchColumn(),
    'total_views' => $db->query("SELECT SUM(views) FROM oc_blog_posts")->fetchColumn() ?: 0
];

// Posts recentes - CORRIGIDO: usar getAllPosts para mostrar todos os posts no admin
$recent_posts = $blog->getAllPosts(1, 5);

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>
            
            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total de Posts</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_posts'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Categorias</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_categories'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-folder fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Comentários</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_comments'] ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-comments fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Visualizações</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($stats['total_views']) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-eye fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Posts Recentes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Posts Recentes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Categoria</th>
                                    <th>Data</th>
                                    <th>Visualizações</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_posts as $post): ?>
                                <tr>
                                    <td><?= htmlspecialchars($post['title']) ?></td>
                                    <td><?= htmlspecialchars($post['category_name']) ?></td>
                                    <td><?= formatDate($post['created_at']) ?></td>
                                    <td><?= $post['views'] ?></td>
                                    <td>
                                        <a href="edit_post.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
                                        <a href="../post.php?slug=<?= $post['slug'] ?>" class="btn btn-sm btn-info" target="_blank">Ver</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>