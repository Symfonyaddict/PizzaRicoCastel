import './bootstrap.js';
import './styles/app.css';

document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la navbar sticky avec effet au scroll
    const navbar = document.querySelector('.navbar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('navbar-scrolled');
            } else {
                navbar.classList.remove('navbar-scrolled');
                navbar.style.backgroundColor = 'var(--dark-surface)';
            }
        });
    }
    
    // Gestion du menu burger interactif
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarCollapse = document.querySelector('.navbar-collapse');
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
    
    if (navbarToggler && navbarCollapse) {
        // Fonction pour fermer le menu
        function closeNavMenu() {
            if (navbarToggler.getAttribute('aria-expanded') === 'true') {
                navbarToggler.click();
            }
        }
        
        // Fermer le menu lorsqu'un lien est cliqué
        navLinks.forEach(link => {
            link.addEventListener('click', closeNavMenu);
        });
        
        // Fermer le menu lorsque l'utilisateur clique en dehors
        document.addEventListener('click', function(event) {
            const isClickInside = navbarToggler.contains(event.target) || navbarCollapse.contains(event.target);
            
            if (!isClickInside && navbarCollapse.classList.contains('show')) {
                closeNavMenu();
            }
        });
        
        // Fermer le menu lorsque l'utilisateur appuie sur la touche Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && navbarCollapse.classList.contains('show')) {
                closeNavMenu();
            }
        });
    }
});
