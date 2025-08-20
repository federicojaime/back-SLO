// Configuración global
window.SLO = {
    baseUrl: '/back-SLO/public',
    csrfToken: null
};

// Función para generar slugs
function generateSlug(text) {
    return text
        .toLowerCase()
        .replace(/[áàäâ]/g, 'a')
        .replace(/[éèëê]/g, 'e')
        .replace(/[íìïî]/g, 'i')
        .replace(/[óòöô]/g, 'o')
        .replace(/[úùüû]/g, 'u')
        .replace(/[ñ]/g, 'n')
        .replace(/[^\w\s-]/g, '')
        .replace(/[\s_-]+/g, '-')
        .replace(/^-+|-+$/g, '');
}

document.addEventListener('DOMContentLoaded', function() {
    
    // ==========================================
    // MENÚ MÓVIL RESPONSIVE
    // ==========================================
    
    // Crear botón de menú móvil
    const mobileToggle = document.createElement('button');
    mobileToggle.className = 'mobile-menu-toggle';
    mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
    mobileToggle.setAttribute('aria-label', 'Abrir menú');
    document.body.appendChild(mobileToggle);
    
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    document.body.appendChild(overlay);
    
    // Función para abrir/cerrar menú
    function toggleMenu() {
        const isOpen = sidebar.classList.contains('show');
        
        if (isOpen) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
            document.body.style.overflow = '';
        } else {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            mobileToggle.innerHTML = '<i class="fas fa-times"></i>';
            document.body.style.overflow = 'hidden';
        }
    }
    
    // Event listeners para el menú
    mobileToggle.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', toggleMenu);
    
    // Cerrar menú al hacer click en un enlace
    const navLinks = document.querySelectorAll('.nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1023) {
                toggleMenu();
            }
        });
    });
    
    // Cerrar menú con ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebar.classList.contains('show')) {
            toggleMenu();
        }
    });
    
    // Manejar resize de ventana
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1023) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
            mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
        }
    });
    
    // ==========================================
    // INICIALIZAR QUILL EDITOR
    // ==========================================
    
    if (document.getElementById('editor')) {
        const quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'align': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['blockquote', 'code-block'],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });
        
        // Sincronizar con textarea oculta
        const hiddenInput = document.getElementById('content');
        if (hiddenInput) {
            quill.on('text-change', function() {
                hiddenInput.value = quill.root.innerHTML;
            });
            
            // Cargar contenido inicial si existe
            if (hiddenInput.value) {
                quill.root.innerHTML = hiddenInput.value;
            }
        }
        
        // Handler para subir imágenes
        const toolbar = quill.getModule('toolbar');
        toolbar.addHandler('image', function() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();
            
            input.onchange = function() {
                const file = input.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('image', file);
                    
                    fetch('/back-SLO/public/api/upload-image', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const range = quill.getSelection();
                            quill.insertEmbed(range.index, 'image', data.url);
                        } else {
                            alert('Error al subir imagen: ' + data.error);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al subir imagen');
                    });
                }
            };
        });
    }
    
    // ==========================================
    // AUTO-GENERAR SLUG DESDE TÍTULO
    // ==========================================
    
    const titleInput = document.getElementById('title');
    if (titleInput) {
        // Crear preview del slug
        const preview = document.createElement('div');
        preview.className = 'mt-2 p-2 bg-light border rounded';
        preview.innerHTML = '<small class="text-muted"><strong>URL:</strong> <span class="text-primary">/articulo/<span id="slug-text">nuevo-articulo</span></span></small>';
        titleInput.parentNode.appendChild(preview);
        
        titleInput.addEventListener('input', function() {
            const slug = generateSlug(this.value) || 'nuevo-articulo';
            document.getElementById('slug-text').textContent = slug;
        });
    }
    
    // ==========================================
    // AUTO-HIDE ALERTS
    // ==========================================
    
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            if (alert.classList.contains('alert-success')) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }
        });
    }, 3000);
    
    // ==========================================
    // CONFIRMACIÓN PARA ELIMINACIONES
    // ==========================================
    
    document.querySelectorAll('.btn-delete').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('¿Estás seguro de que quieres eliminar este elemento?')) {
                e.preventDefault();
            }
        });
    });
    
    // ==========================================
    // PREVISUALIZACIÓN DE IMÁGENES
    // ==========================================
    
    document.querySelectorAll('input[type="file"]').forEach(function(input) {
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById(this.dataset.preview);
            
            if (file && preview) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    });
});

// ==========================================
// FUNCIONES UTILITARIAS
// ==========================================

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-AR', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}