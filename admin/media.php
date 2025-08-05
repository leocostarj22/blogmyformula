<?php
require_once '../config/database.php';
require_once '../config/config.php';

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$upload_dir = '../uploads/';
$success = '';
$error = '';

// Upload de arquivo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = uniqid() . '_' . sanitizeInput($file['name']);
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $success = "Arquivo enviado com sucesso: " . $filename;
            } else {
                $error = "Erro ao enviar arquivo!";
            }
        } else {
            $error = "Tipo de arquivo não permitido!";
        }
    } else {
        $error = "Erro no upload: " . $file['error'];
    }
}

// Eliminar arquivo
if (isset($_GET['delete'])) {
    $filename = basename($_GET['delete']);
    $file_path = $upload_dir . $filename;
    
    if (file_exists($file_path) && unlink($file_path)) {
        $success = "Arquivo eliminado com sucesso!";
    } else {
        $error = "Erro ao eliminar arquivo!";
    }
}

// Listar arquivos
$files = [];
if (is_dir($upload_dir)) {
    $scan = scandir($upload_dir);
    foreach ($scan as $file) {
        if ($file !== '.' && $file !== '..' && is_file($upload_dir . $file)) {
            $file_path = $upload_dir . $file;
            $files[] = [
                'name' => $file,
                'size' => filesize($file_path),
                'modified' => filemtime($file_path),
                'type' => strtolower(pathinfo($file, PATHINFO_EXTENSION))
            ];
        }
    }
    
    // Ordenar por data de modificação (mais recente primeiro)
    usort($files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });
}

function formatFileSize($size) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $unit = 0;
    while ($size >= 1024 && $unit < count($units) - 1) {
        $size /= 1024;
        $unit++;
    }
    return round($size, 2) . ' ' . $units[$unit];
}

function isImage($extension) {
    return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
}

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestão de Média</h1>
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
                            <h6 class="m-0 font-weight-bold text-primary">Enviar Novo Arquivo</h6>
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="file" class="form-label">Selecionar Arquivo</label>
                                    <input type="file" class="form-control" id="file" name="file" required>
                                    <small class="form-text text-muted">
                                        Formatos aceites: JPG, PNG, GIF, WebP, PDF, DOC, DOCX, TXT
                                    </small>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-upload"></i> Enviar Arquivo
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Estatísticas</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Total de arquivos:</strong> <?= count($files) ?></p>
                            <p><strong>Espaço usado:</strong> 
                                <?php 
                                $total_size = array_sum(array_column($files, 'size'));
                                echo formatFileSize($total_size);
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Biblioteca de Média</h6>
                        </div>
                        <div class="card-body">
                            <?php if (empty($files)): ?>
                                <p class="text-center text-muted">Nenhum arquivo encontrado.</p>
                            <?php else: ?>
                                <div class="row">
                                    <?php foreach ($files as $file): ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body p-2">
                                                    <?php if (isImage($file['type'])): ?>
                                                        <img src="../uploads/<?= $file['name'] ?>" alt="<?= htmlspecialchars($file['name']) ?>" class="img-fluid rounded mb-2" style="max-height: 150px; width: 100%; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="text-center p-4 bg-light rounded mb-2">
                                                            <i class="fas fa-file fa-3x text-muted"></i>
                                                            <p class="mt-2 mb-0 text-muted">.<?= $file['type'] ?></p>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <h6 class="card-title" style="font-size: 0.9rem;" title="<?= htmlspecialchars($file['name']) ?>">
                                                        <?= htmlspecialchars(strlen($file['name']) > 20 ? substr($file['name'], 0, 20) . '...' : $file['name']) ?>
                                                    </h6>
                                                    
                                                    <p class="card-text small text-muted">
                                                        <strong>Tamanho:</strong> <?= formatFileSize($file['size']) ?><br>
                                                        <strong>Modificado:</strong> <?= date('d/m/Y H:i', $file['modified']) ?>
                                                    </p>
                                                    
                                                    <div class="btn-group w-100" role="group">
                                                        <a href="../uploads/<?= $file['name'] ?>" class="btn btn-sm btn-outline-primary" target="_blank" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('../uploads/<?= $file['name'] ?>')" title="Copiar URL">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <a href="media.php?delete=<?= urlencode($file['name']) ?>" class="btn btn-sm btn-outline-danger delete-btn" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(window.location.origin + '/' + text).then(function() {
        alert('URL copiada para a área de transferência!');
    }, function(err) {
        console.error('Erro ao copiar: ', err);
    });
}
</script>

<?php include 'includes/footer.php'; ?>