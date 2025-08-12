<?php
require_once 'config/config.php';
require_once 'includes/Blog.php';

$blog = new Blog();
$categories = $blog->getCategories();

// Definir ícones para cada categoria
$category_icons = [
    'perda-de-peso' => 'fas fa-weight-hanging',
    'menopausa' => 'fas fa-female',
    'anti-aging' => 'fas fa-spa',
    'saude-sexual' => 'fas fa-heart',
    'sono' => 'fas fa-bed',
    'digestivo' => 'fas fa-pills', // ou 'fas fa-pills' ou 'fas fa-capsules'
    'geral' => 'fas fa-newspaper',
    'tecnologia' => 'fas fa-laptop-code',
    'programacao' => 'fas fa-code',
    'design' => 'fas fa-paint-brush',
    'marketing' => 'fas fa-bullhorn'
];

$page_title = "Todas as Categorias";
$page_description = "Explore todas as categorias do " . BLOG_TITLE . " e encontre conteúdo sobre saúde, bem-estar e qualidade de vida.";

include 'includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold" style="color: #2B80B9; font-family: math;">
                    <i class="fas fa-th-large me-3"></i>Categorias
                </h1>
                <p class="lead text-muted">
                    Explore nossos conteúdos organizados por categoria
                </p>
            </div>

            <div class="row g-4">
                <?php foreach ($categories as $category): 
                    $icon = $category_icons[$category['slug']] ?? 'fas fa-folder';
                    $post_count = $blog->countPostsByCategory($category['id']);
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0" style="transition: transform 0.3s ease;">
                        <div class="card-body text-center p-4">
                            <div class="mb-3">
                                <i class="<?php echo $icon; ?> fa-3x" style="color: #2B80B9;"></i>
                            </div>
                            <h5 class="card-title fw-bold" style="color: #2B80B9;">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </h5>
                            <?php if (!empty($category['description'])): ?>
                            <p class="card-text text-muted small">
                                <?php echo htmlspecialchars($category['description']); ?>
                            </p>
                            <?php endif; ?>
                            <div class="mt-3">
                                <span class="badge bg-secondary">
                                    <?php echo $post_count; ?> 
                                    <?php echo $post_count == 1 ? 'post' : 'posts'; ?>
                                </span>
                            </div>
                            <div class="mt-3">
                                <a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $category['slug']; ?>" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-arrow-right me-1"></i>
                                    Ver Posts
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if (empty($categories)): ?>
            <div class="text-center py-5">
                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                <h3 class="text-muted">Nenhuma categoria encontrada</h3>
                <p class="text-muted">As categorias aparecerão aqui quando forem criadas.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(43, 128, 185, 0.15) !important;
}

.btn-outline-primary {
    border-color: #2B80B9;
    color: #2B80B9;
}

.btn-outline-primary:hover {
    background-color: #2B80B9;
    border-color: #2B80B9;
}
</style>

<?php include 'includes/footer.php'; ?>