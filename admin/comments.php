<?php
require_once '../config/database.php';
require_once '../config/config.php';

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();

$success = '';
$error = '';

// Aprovar/Rejeitar comentário
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $comment_id = (int)$_GET['id'];
    
    if ($action === 'approve') {
        $stmt = $db->prepare("UPDATE oc_blog_comments SET status = 'approved' WHERE id = ?");
        if ($stmt->execute([$comment_id])) {
            $success = "Comentário aprovado!";
        }
    } elseif ($action === 'reject') {
        $stmt = $db->prepare("UPDATE oc_blog_comments SET status = 'rejected' WHERE id = ?");
        if ($stmt->execute([$comment_id])) {
            $success = "Comentário rejeitado!";
        }
    } elseif ($action === 'delete') {
        $stmt = $db->prepare("DELETE FROM oc_blog_comments WHERE id = ?");
        if ($stmt->execute([$comment_id])) {
            $success = "Comentário eliminado!";
        }
    }
}

// Buscar comentários
$stmt = $db->query("
    SELECT c.*, p.title as post_title, p.slug as post_slug 
    FROM oc_blog_comments c 
    JOIN oc_blog_posts p ON c.post_id = p.id 
    ORDER BY c.created_at DESC
");
$comments = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestão de Comentários</h1>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $success ?>
                </div>
            <?php endif; ?>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Lista de Comentários</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($comments)): ?>
                        <p class="text-center text-muted">Nenhum comentário encontrado.</p>
                    <?php else: ?>
                        <?php foreach ($comments as $comment): ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= htmlspecialchars($comment['author_name']) ?></strong>
                                        <small class="text-muted">(<?= htmlspecialchars($comment['author_email']) ?>)</small>
                                        <br>
                                        <small class="text-muted">
                                            Post: <a href="../post.php?slug=<?= $comment['post_slug'] ?>" target="_blank"><?= htmlspecialchars($comment['post_title']) ?></a>
                                            | <?= formatDate($comment['created_at']) ?>
                                        </small>
                                    </div>
                                    <div>
                                        <?php if ($comment['status'] === 'pending'): ?>
                                            <span class="badge bg-warning">Pendente</span>
                                        <?php elseif ($comment['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Aprovado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Rejeitado</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p><?= nl2br(htmlspecialchars($comment['content'])) ?></p>
                                    
                                    <div class="btn-group" role="group">
                                        <?php if ($comment['status'] !== 'approved'): ?>
                                            <a href="comments.php?action=approve&id=<?= $comment['id'] ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-check"></i> Aprovar
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if ($comment['status'] !== 'rejected'): ?>
                                            <a href="comments.php?action=reject&id=<?= $comment['id'] ?>" class="btn btn-sm btn-warning">
                                                <i class="fas fa-times"></i> Rejeitar
                                            </a>
                                        <?php endif; ?>
                                        
                                        <a href="comments.php?action=delete&id=<?= $comment['id'] ?>" class="btn btn-sm btn-danger delete-btn">
                                            <i class="fas fa-trash"></i> Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/footer.php'; ?>