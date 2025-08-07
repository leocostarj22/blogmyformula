// Script personalizado para o blog MyFormula

document.addEventListener('DOMContentLoaded', function() {
    // Remover JavaScript personalizado do dropdown - usar apenas Bootstrap nativo
    // O Bootstrap já gerencia o dropdown corretamente
    
    // Melhorar a busca com Enter
    const searchForm = document.querySelector('form[action*="search.php"]');
    if (searchForm) {
        const searchInput = searchForm.querySelector('input[name="q"]');
        
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchForm.submit();
            }
        });
    }
    
    // Adicionar animações suaves aos cards
    // Remover as animações dos cards (linhas 36-44)
    // const cards = document.querySelectorAll('.card');
    // cards.forEach(card => {
    //     card.addEventListener('mouseenter', function() {
    //         this.style.transform = 'translateY(-5px)';
    //     });
    //     
    //     card.addEventListener('mouseleave', function() {
    //         this.style.transform = 'translateY(0)';
    //     });
    // });
    
    // Melhorar a responsividade do menu mobile
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    
    if (navbarToggler && navbarCollapse) {
        navbarToggler.addEventListener('click', function() {
            setTimeout(() => {
                if (navbarCollapse.classList.contains('show')) {
                    document.body.style.paddingTop = navbarCollapse.offsetHeight + 'px';
                } else {
                    document.body.style.paddingTop = '0';
                }
            }, 300);
        });
    }
});

// Função para destacar o item de menu ativo
function highlightActiveMenuItem() {
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.navbar-nav .nav-link');
    
    menuItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && currentPath.includes(href.replace(window.location.origin, ''))) {
            item.classList.add('active');
            item.style.fontWeight = 'bold';
        }
    });
}

// Executar quando a página carregar
document.addEventListener('DOMContentLoaded', highlightActiveMenuItem);

// Smooth scrolling for anchor links
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for internal links
    const links = document.querySelectorAll('a[href^="#"]');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Search form enhancement
    const searchForms = document.querySelectorAll('form[action*="search.php"]');
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const input = this.querySelector('input[name="q"]');
            if (input && input.value.trim() === '') {
                e.preventDefault();
                input.focus();
                input.classList.add('is-invalid');
                setTimeout(() => {
                    input.classList.remove('is-invalid');
                }, 3000);
            }
        });
    });
    
    // Image lazy loading fallback
    const images = document.querySelectorAll('img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtc2l6ZT0iMTgiIHRleHQtYW5jaG9yPSJtaWRkbGUiIGR5PSIuM2VtIiBmaWxsPSIjOTk5Ij5JbWFnZW0gbsOjbyBlbmNvbnRyYWRhPC90ZXh0Pjwvc3ZnPg==';
            this.alt = 'Imagem não encontrada';
        });
    });
    
    // Back to top button - Versão Simples e Funcional
    const backToTop = document.createElement('button');
    backToTop.className = 'btn btn-primary position-fixed';
    backToTop.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none; align-items: center; justify-content: center; font-size: 1.2rem;';
    backToTop.title = 'Voltar ao topo';
    backToTop.innerHTML = '↑'; // Símbolo simples que sempre funciona
    
    document.body.appendChild(backToTop);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTop.style.display = 'flex';
        } else {
            backToTop.style.display = 'none';
        }
    });
    
    backToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Print functionality
    window.printPost = function() {
        window.print();
    };
    
    // Share functionality
    window.sharePost = function(url, title) {
        if (navigator.share) {
            navigator.share({
                title: title,
                url: url
            });
        } else {
            // Fallback: copy to clipboard
            navigator.clipboard.writeText(url).then(function() {
                alert('Link copiado para a área de transferência!');
            });
        }
    };
});

// Reading time calculator
function calculateReadingTime(text) {
    const wordsPerMinute = 200;
    const words = text.trim().split(/\s+/).length;
    const time = Math.ceil(words / wordsPerMinute);
    return time;
}

// Format numbers
function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}