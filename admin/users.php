<?php
require_once '../config/database.php';
require_once '../config/config.php';

session_start();

// Verificar se está logado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$message = '';
$error = '';

// Processar ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            $username = sanitizeInput($_POST['username'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'author';
            $status = $_POST['status'] ?? 'active';
            
            if ($username && $email && $password) {
                try {
                    // Verificar se username ou email já existem
                    $stmt = $db->prepare("SELECT id FROM oc_blog_users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    
                    if ($stmt->fetch()) {
                        $error = 'Username ou email já existem!';
                    } else {
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("INSERT INTO oc_blog_users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $hashed_password, $role, $status]);
                        $message = 'Utilizador criado com sucesso!';
                    }
                } catch (PDOException $e) {
                    $error = 'Erro ao criar utilizador: ' . $e->getMessage();
                }
            } else {
                $error = 'Todos os campos são obrigatórios!';
            }
            break;
            
        case 'update':
            $id = (int)($_POST['id'] ?? 0);
            $username = sanitizeInput($_POST['username'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $role = $_POST['role'] ?? 'author';
            $status = $_POST['status'] ?? 'active';
            $password = $_POST['password'] ?? '';
            
            if ($id && $username && $email) {
                try {
                    // Verificar se username ou email já existem (exceto o próprio usuário)
                    $stmt = $db->prepare("SELECT id FROM oc_blog_users WHERE (username = ? OR email = ?) AND id != ?");
                    $stmt->execute([$username, $email, $id]);
                    
                    if ($stmt->fetch()) {
                        $error = 'Username ou email já existem!';
                    } else {
                        if ($password) {
                            // Atualizar com nova senha
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                            $stmt = $db->prepare("UPDATE oc_blog_users SET username = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
                            $stmt->execute([$username, $email, $hashed_password, $role, $status, $id]);
                        } else {
                            // Atualizar sem alterar senha
                            $stmt = $db->prepare("UPDATE oc_blog_users SET username = ?, email = ?, role = ?, status = ? WHERE id = ?");
                            $stmt->execute([$username, $email, $role, $status, $id]);
                        }
                        $message = 'Utilizador atualizado com sucesso!';
                    }
                } catch (PDOException $e) {
                    $error = 'Erro ao atualizar utilizador: ' . $e->getMessage();
                }
            } else {
                $error = 'Campos obrigatórios não preenchidos!';
            }
            break;
            
        case 'delete':
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id && $id != $_SESSION['admin_user_id']) {
                try {
                    $stmt = $db->prepare("DELETE FROM oc_blog_users WHERE id = ?");
                    $stmt->execute([$id]);
                    $message = 'Utilizador eliminado com sucesso!';
                } catch (PDOException $e) {
                    $error = 'Erro ao eliminar utilizador: ' . $e->getMessage();
                }
            } else {
                $error = 'Não é possível eliminar o próprio utilizador!';
            }
            break;
    }
}

// Buscar todos os utilizadores
$users = $db->query("SELECT * FROM oc_blog_users ORDER BY created_at DESC")->fetchAll();

include 'includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestão de Utilizadores</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                    <i class="fas fa-plus"></i> Novo Utilizador
                </button>
            </div>
            
            <?php if ($message): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Tabela de Utilizadores -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Lista de Utilizadores</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Função</th>
                                    <th>Status</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($user['username']) ?></strong>
                                        <?php if ($user['id'] == $_SESSION['admin_user_id']): ?>
                                            <span class="badge bg-info ms-1">Você</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $user['role'] == 'admin' ? 'danger' : ($user['role'] == 'editor' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $user['status'] == 'active' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($user['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                onclick="editUser(<?= htmlspecialchars(json_encode($user)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($user['id'] != $_SESSION['admin_user_id']): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteUser(<?= $user['id'] ?>, '<?= htmlspecialchars($user['username']) ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
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

<!-- Modal Criar Utilizador -->
<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Criar Novo Utilizador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Palavra-passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="role" class="form-label">Função</label>
                        <select class="form-select" id="role" name="role">
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Criar Utilizador</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Utilizador -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Utilizador</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Nova Palavra-passe</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                        <div class="form-text">Deixe em branco para manter a palavra-passe atual</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_role" class="form-label">Função</label>
                        <select class="form-select" id="edit_role" name="role">
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status">
                            <option value="active">Ativo</option>
                            <option value="inactive">Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Utilizador</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmar Eliminação -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o utilizador <strong id="delete_username"></strong>?</p>
                <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Esta ação não pode ser desfeita!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" id="delete_id">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function editUser(user) {
    document.getElementById('edit_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role').value = user.role;
    document.getElementById('edit_status').value = user.status;
    
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}

function deleteUser(id, username) {
    document.getElementById('delete_id').value = id;
    document.getElementById('delete_username').textContent = username;
    
    new bootstrap.Modal(document.getElementById('deleteUserModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>