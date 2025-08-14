</main>
    
    <!-- Footer -->
    <footer class="text-light py-4 mt-5" style="background-color: #212629;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <img src="<?php echo SITE_URL; ?>/uploads/logo-myformula-colors.png" alt="MyFormula Logo" height="40" class="me-3">
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
                        <li><a href="<?php echo SITE_URL; ?>/admin/" class="text-light text-decoration-none">Admin</a></li>
                        <li><a href="https://www.myformula.pt" target="_blank" class="text-light text-decoration-none">Site Oficial</a></li>
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p>&copy; <?php echo date('Y'); ?> MyFormula Suplementos. Todos os direitos reservados.</p>
                <?php // <small class="text-muted">Blog oficial da MyFormula - Suplementos e Nutrição Esportiva</small> ?>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>assets/js/script.js"></script>
    
    <?php
    // Verificar se há shortcodes na página e carregar os arquivos necessários
    if (isset($post) && $post) {
        if (strpos($post['content'], '[calculadora-imc]') !== false) {
            echo '<link rel="stylesheet" href="' . SITE_URL . 'assets/css/imc-calculator.css">';
            echo '<script src="' . SITE_URL . 'assets/js/imc-calculator.js"></script>';
        }
        
        if (strpos($post['content'], '[questionario-sono]') !== false) {
            echo '<link rel="stylesheet" href="' . SITE_URL . 'assets/css/sleep-quiz.css">';
            echo '<script src="' . SITE_URL . 'assets/js/sleep-quiz.js"></script>';
        }

        if (strpos($post['content'], '[teste-menopausa]') !== false) {
            echo '<link rel="stylesheet" href="' . SITE_URL . 'assets/css/menopause-quiz.css">';
            echo '<script src="' . SITE_URL . 'assets/js/menopause-quiz.js"></script>';
        }
    }
    ?>
    
    <!-- Scripts para calculadoras -->
    <script>
    // Carregamento dinâmico para calculadora IMC (fallback)
    if (document.querySelector('.calculator-container') && !document.querySelector('link[href*="imc-calculator.css"]')) {
        // Carregar CSS
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = '<?php echo SITE_URL; ?>assets/css/imc-calculator.css';
        document.head.appendChild(link);
        
        // Carregar JS
        const script = document.createElement('script');
        script.src = '<?php echo SITE_URL; ?>assets/js/imc-calculator.js';
        document.head.appendChild(script);
    }
    
    // Carregamento dinâmico para questionário de sono (fallback)
    if (document.querySelector('.sleep-quiz-container') && !document.querySelector('link[href*="sleep-quiz.css"]')) {
        // Carregar CSS
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = '<?php echo SITE_URL; ?>assets/css/sleep-quiz.css';
        document.head.appendChild(link);
        
        // Carregar JS
        const script = document.createElement('script');
        script.src = '<?php echo SITE_URL; ?>assets/js/sleep-quiz.js';
        document.head.appendChild(script);
    }
    </script>
</body>
</html>