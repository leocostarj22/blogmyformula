</main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo SITE_URL; ?>uploads/logo-myformula-colors.png" alt="MyFormula Logo" height="40" class="me-3">
                        <div>
                            <?php // <h5 class="mb-1"><?php echo BLOG_TITLE; ?></h5> 
                            <?php // <p class="mb-0"><?php echo BLOG_DESCRIPTION; ?></p> 
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <h6>Categorias</h6>
                    <ul class="list-unstyled">
                        <?php
                        $blog = new Blog();
                        $categories = $blog->getCategories();
                        foreach ($categories as $category):
                        ?>
                        <li>
                            <a href="<?php echo SITE_URL; ?>category.php?slug=<?php echo $category['slug']; ?>" class="text-light text-decoration-none">
                                <?php echo $category['name']; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-light text-decoration-none">Início</a></li>
                        <li><a href="<?php echo SITE_URL; ?>admin/" class="text-light text-decoration-none">Admin</a></li>
                        <li><a href="https://www.myformula.pt" target="_blank" class="text-light text-decoration-none">Site Oficial</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> MyFormula Suplementos. Todos os direitos reservados.</p>
                <small class="text-muted">Blog oficial da MyFormula - Suplementos e Nutrição Esportiva</small>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>assets/js/script.js"></script>
</body>
</html>