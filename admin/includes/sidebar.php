<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
    <div class="position-sticky pt-3">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>" href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']), ['posts.php', 'add_post.php', 'edit_post.php']) ? 'active' : '' ?>" href="posts.php">
                    <i class="fas fa-file-alt"></i> Posts
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : '' ?>" href="categories.php">
                    <i class="fas fa-folder"></i> Categorias
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'comments.php' ? 'active' : '' ?>" href="comments.php">
                    <i class="fas fa-comments"></i> Comentários
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'media.php' ? 'active' : '' ?>" href="media.php">
                    <i class="fas fa-images"></i> Média
                </a>
            </li>
        </ul>
        
        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
            <span>Links Rápidos</span>
        </h6>
        <ul class="nav flex-column mb-2">
            <li class="nav-item">
                <a class="nav-link" href="../index.php" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Ver Blog
                </a>
            </li>
        </ul>
    </div>
</nav>