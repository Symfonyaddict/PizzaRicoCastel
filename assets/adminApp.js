import './bootstrap.js';
import './styles/adminApp.css';

document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la modal de suppression d'utilisateur
    const deleteUserButtons = document.querySelectorAll('.delete-user');
    
    if (deleteUserButtons.length > 0) {
        deleteUserButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                if (confirmDeleteBtn) {
                    const deleteUrl = confirmDeleteBtn.getAttribute('data-delete-url');
                    if (deleteUrl) {
                        confirmDeleteBtn.href = deleteUrl.replace('USER_ID', userId);
                    }
                }
            });
        });
    }
    
    // Gestion de la modal de suppression des sections hero
    const deleteHeroButtons = document.querySelectorAll('.delete-hero');
    const deleteHeroForm = document.getElementById('deleteHeroForm');
    
    if (deleteHeroButtons.length > 0 && deleteHeroForm) {
        const tokenInput = deleteHeroForm.querySelector('input[name="_token"]');
        
        deleteHeroButtons.forEach(button => {
            button.addEventListener('click', function() {
                const heroId = this.getAttribute('data-hero-id');
                const csrfToken = this.getAttribute('data-csrf-token');
                
                // Mise à jour de l'action du formulaire avec l'ID correct
                // Récupérer l'URL de base du formulaire et remplacer l'ID
                const baseUrl = `/admin/hero/delete/${heroId}`;
                deleteHeroForm.action = baseUrl;
                
                // Utilisation du token CSRF stocké dans l'attribut de données
                if (tokenInput && csrfToken) {
                    tokenInput.value = csrfToken;
                }
            });
        });
    }
    
    // Toggle sidebar on mobile
    const sidebarToggle = document.querySelector('#toggleSidebarBtn');
    const adminSidebar = document.querySelector('.admin-sidebar');
    const sidebarClose = document.querySelector('#closeSidebarBtn');
    
    // Créer l'overlay pour la barre latérale mobile
    const adminOverlay = document.createElement('div');
    adminOverlay.className = 'admin-overlay';
    document.body.appendChild(adminOverlay);
    
    // Function to open sidebar
    function openSidebar() {
        adminSidebar.classList.add('show');
        document.body.classList.add('sidebar-mobile-open');
        document.body.style.overflow = 'hidden'; // Empêcher le défilement du body
        setTimeout(() => {
            adminOverlay.classList.add('active');
        }, 10);
    }
    
    // Function to close sidebar
    function closeSidebar() {
        adminSidebar.classList.remove('show');
        document.body.classList.remove('sidebar-mobile-open');
        adminOverlay.classList.remove('active');
        document.body.style.overflow = ''; // Réactiver le défilement du body
    }
    
    if (sidebarToggle && adminSidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            if (adminSidebar.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }
    
    if (sidebarClose && adminSidebar) {
        sidebarClose.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            closeSidebar();
        });
    }
    
    // Close sidebar when clicking on overlay
    adminOverlay.addEventListener('click', function() {
        closeSidebar();
    });
    
    // Close sidebar when pressing Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && adminSidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
    
    // Fermer la barre latérale lors du redimensionnement de la fenêtre si on passe en mode desktop
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 992 && adminSidebar.classList.contains('show')) {
            closeSidebar();
        }
    });
    
    // Dropdown toggle functionality
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            if (dropdown) {
                dropdown.classList.toggle('show');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        // Fermer les dropdowns
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            if (!dropdown.previousElementSibling.contains(event.target)) {
                dropdown.classList.remove('show');
            }
        });
        
        // Fermer la barre latérale si on clique en dehors et qu'elle est ouverte
        if (adminSidebar && adminSidebar.classList.contains('show')) {
            const isClickInsideSidebar = adminSidebar.contains(event.target);
            const isClickOnToggle = sidebarToggle && sidebarToggle.contains(event.target);
            
            if (!isClickInsideSidebar && !isClickOnToggle) {
                closeSidebar();
            }
        }
    });
});