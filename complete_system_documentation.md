# 📰 San Luis Opina CMS - Documentación Técnica Completa

## 📋 Índice
1. [Arquitectura del Sistema](#arquitectura-del-sistema)
2. [Base de Datos](#base-de-datos)
3. [Estructura del Proyecto](#estructura-del-proyecto)
4. [Funcionalidades Implementadas](#funcionalidades-implementadas)
5. [Funcionalidades Pendientes](#funcionalidades-pendientes)
6. [APIs y Endpoints](#apis-y-endpoints)
7. [Seguridad](#seguridad)
8. [Instalación y Configuración](#instalación-y-configuración)
9. [Guía de Desarrollo](#guía-de-desarrollo)
10. [Roadmap](#roadmap)

---

## 🏗️ Arquitectura del Sistema

### Stack Tecnológico
- **Backend:** PHP 8.x (Vanilla PHP con Router personalizado)
- **Frontend:** HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.1.3
- **Base de Datos:** MySQL 8.x / MariaDB 10.x
- **Editor:** Quill.js (WYSIWYG)
- **Iconos:** Font Awesome 6.x
- **Tipografía:** Inter Font (Google Fonts)

### Patrón de Arquitectura
```
MVC Simplificado:
├── Router (index.php) - Controlador principal
├── Templates (Views) - Presentación
├── Lógica de negocio - Embebida en router
└── Modelos - Consultas SQL directas
```

### Características Técnicas
- **Responsive Design:** Mobile-first approach
- **SPA Behavior:** Navegación fluida sin recargas completas
- **Progressive Enhancement:** Funciona sin JavaScript
- **Accessibility:** WCAG 2.1 AA compliant
- **SEO Ready:** Meta tags y estructura semántica

---

## 🗄️ Base de Datos

### Esquema Completo (11 Tablas)

#### 1. **users** - Gestión de Usuarios
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,              -- bcrypt hash
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor', 'author') DEFAULT 'author',
    status ENUM('active', 'inactive') DEFAULT 'active',
    avatar VARCHAR(255) NULL,                    -- URL del avatar
    bio TEXT NULL,                               -- Biografía del usuario
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 2. **articles** - Contenido Principal
```sql
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,           -- URL amigable
    content LONGTEXT NOT NULL,                   -- HTML del editor
    excerpt TEXT NULL,                           -- Resumen para listados
    featured_image VARCHAR(255) NULL,            -- Imagen destacada
    featured_image_alt VARCHAR(255) NULL,        -- Alt text para SEO
    category_id INT NULL,
    author_id INT NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    featured BOOLEAN DEFAULT FALSE,              -- Artículo destacado
    views INT DEFAULT 0,                         -- Contador de visitas
    meta_title VARCHAR(60) NULL,                 -- SEO title tag
    meta_description VARCHAR(160) NULL,          -- SEO meta description
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE,
    
    INDEX idx_slug (slug),
    INDEX idx_status_published (status, published_at DESC),
    FULLTEXT idx_search (title, content, excerpt)
);
```

#### 3. **categories** - Organización de Contenido
```sql
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    color VARCHAR(7) DEFAULT '#6c757d',          -- Color hex para UI
    sort_order INT DEFAULT 0,                    -- Orden de visualización
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 4. **tags** - Sistema de Etiquetas
```sql
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    color VARCHAR(7) DEFAULT '#6c757d',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 5. **article_tags** - Relación Many-to-Many
```sql
CREATE TABLE article_tags (
    article_id INT,
    tag_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
```

#### 6. **comments** - Sistema de Comentarios
```sql
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    author_name VARCHAR(100) NOT NULL,
    author_email VARCHAR(100) NOT NULL,
    author_website VARCHAR(255) NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'spam') DEFAULT 'pending',
    parent_id INT NULL,                          -- Para respuestas anidadas
    ip_address VARCHAR(45) NULL,                 -- IPv4/IPv6
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);
```

#### 7. **site_config** - Configuración del Sistema
```sql
CREATE TABLE site_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT NULL,
    config_type ENUM('string', 'text', 'number', 'boolean', 'json') DEFAULT 'string',
    description VARCHAR(255) NULL,
    is_public BOOLEAN DEFAULT FALSE,             -- Si se expone al frontend
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

#### 8. **media_files** - Gestor de Multimedia
```sql
CREATE TABLE media_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,                      -- En bytes
    mime_type VARCHAR(100) NOT NULL,
    width INT NULL,                              -- Para imágenes
    height INT NULL,                             -- Para imágenes
    alt_text VARCHAR(255) NULL,
    uploaded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);
```

#### 9. **analytics** - Métricas y Estadísticas
```sql
CREATE TABLE analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    referer VARCHAR(500) NULL,
    page_url VARCHAR(500) NOT NULL,
    session_id VARCHAR(128) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
);
```

#### 10. **newsletter_subscribers** - Marketing por Email
```sql
CREATE TABLE newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(100) NULL,
    status ENUM('active', 'unsubscribed', 'bounced') DEFAULT 'active',
    verification_token VARCHAR(128) NULL,
    verified_at TIMESTAMP NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    unsubscribed_at TIMESTAMP NULL
);
```

#### 11. **menu_items** - Menús Dinámicos
```sql
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    target ENUM('_self', '_blank') DEFAULT '_self',
    icon VARCHAR(50) NULL,
    parent_id INT NULL,                          -- Para menús jerárquicos
    sort_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (parent_id) REFERENCES menu_items(id) ON DELETE CASCADE
);
```

---

## 📁 Estructura del Proyecto

```
back-SLO/
├── public/                          # Punto de entrada web
│   ├── index.php                    # Router principal y lógica
│   ├── .htaccess                    # Rewrite rules para URLs amigables
│   ├── assets/
│   │   ├── css/
│   │   │   └── style.css           # Estilos personalizados
│   │   ├── js/
│   │   │   └── app.js              # JavaScript personalizado
│   │   └── images/
│   │       └── logo.png            # Logo del sitio
│   └── uploads/                     # Archivos subidos dinámicamente
│
├── templates/                       # Plantillas PHP
│   ├── base.php                     # Layout principal con sidebar
│   ├── login.php                    # Página de autenticación
│   ├── dashboard.php                # Panel principal
│   ├── articles/
│   │   ├── index.php               # Lista de artículos
│   │   ├── create.php              # Formulario de creación
│   │   ├── edit.php                # Formulario de edición
│   │   └── view.php                # Vista individual
│   └── categories/
│       ├── index.php               # Gestión de categorías
│       └── create.php              # Crear categoría
│
└── docs/                           # Documentación (este archivo)
    └── README.md
```

---

## ✅ Funcionalidades Implementadas

### 🔐 Sistema de Autenticación
- [x] Login con usuario/contraseña
- [x] Hash de contraseñas con `password_hash()`
- [x] Sesiones PHP seguras
- [x] Middleware de autenticación
- [x] Roles: admin, editor, author
- [x] Logout con destrucción de sesión

### 📝 Gestión de Contenido
- [x] CRUD completo de artículos
- [x] Editor WYSIWYG con Quill.js
- [x] Estados: borrador, publicado, archivado
- [x] Slugs automáticos para URLs amigables
- [x] Sistema de categorías
- [x] Extractos automáticos
- [x] Fechas de publicación

### 🎨 Interfaz de Usuario
- [x] Dashboard con métricas en tiempo real
- [x] Sidebar de navegación fijo
- [x] Diseño responsive (mobile-first)
- [x] Estados de loading y feedback
- [x] Alertas de éxito/error
- [x] Paginación de listados

### 📊 Analytics Básicos
- [x] Contador de vistas por artículo
- [x] Estadísticas del dashboard
- [x] Filtros por estado de publicación
- [x] Métricas de productividad

### 🔧 Configuración
- [x] Configuración de base de datos
- [x] Variables de entorno
- [x] Personalización de rutas
- [x] Gestión de errores

---

## 🚧 Funcionalidades Pendientes (Roadmap)

### Prioridad Alta (Sprint 1)
- [ ] **Sistema de Tags completo**
  - [ ] CRUD de tags
  - [ ] Asignación múltiple a artículos
  - [ ] Filtrado por tags
  - [ ] Nube de tags en frontend

- [ ] **Gestor de Medios**
  - [ ] Subida múltiple de archivos
  - [ ] Galería de medios
  - [ ] Redimensionamiento automático
  - [ ] Optimización de imágenes
  - [ ] CDN integration ready

- [ ] **Editor Avanzado**
  - [ ] Inserción de medios desde galería
  - [ ] Autoguardado cada 30 segundos
  - [ ] Revisiones de contenido
  - [ ] Modo preview en tiempo real
  - [ ] Plantillas de contenido

### Prioridad Media (Sprint 2)
- [ ] **Sistema de Comentarios**
  - [ ] Comentarios públicos en artículos
  - [ ] Moderación automática (spam filter)
  - [ ] Respuestas anidadas
  - [ ] Notificaciones por email
  - [ ] Integración con Akismet

- [ ] **SEO Avanzado**
  - [ ] Meta títulos y descripciones personalizadas
  - [ ] Open Graph tags
  - [ ] Schema.org markup
  - [ ] Sitemap XML automático
  - [ ] Robots.txt dinámico
  - [ ] Analytics de Google integrado

- [ ] **Gestión de Usuarios**
  - [ ] CRUD completo de usuarios
  - [ ] Perfiles públicos de autores
  - [ ] Biografías y avatares
  - [ ] Permisos granulares
  - [ ] Audit trail de acciones

### Prioridad Baja (Sprint 3)
- [ ] **Frontend Público**
  - [ ] Sitio web público responsive
  - [ ] Página de inicio con últimas noticias
  - [ ] Páginas de categorías
  - [ ] Búsqueda de contenido
  - [ ] Archivo por fechas
  - [ ] RSS feeds

- [ ] **Newsletter**
  - [ ] Suscripción desde frontend
  - [ ] Campañas de email
  - [ ] Plantillas de newsletter
  - [ ] Estadísticas de apertura
  - [ ] Integración con Mailchimp/SendGrid

- [ ] **Analytics Avanzados**
  - [ ] Dashboard de métricas detalladas
  - [ ] Reportes exportables
  - [ ] Análisis de audiencia
  - [ ] Métricas de engagement
  - [ ] Integración con Google Analytics 4

### Funcionalidades Futuras
- [ ] **Multiidioma**
  - [ ] Soporte para español/inglés
  - [ ] Gestión de traducciones
  - [ ] URLs localizadas

- [ ] **API REST**
  - [ ] Endpoints para mobile app
  - [ ] Autenticación JWT
  - [ ] Rate limiting
  - [ ] Documentación Swagger

- [ ] **Performance**
  - [ ] Cache de página completa
  - [ ] Compresión de assets
  - [ ] Lazy loading de imágenes
  - [ ] Service Workers (PWA)

---

## 🔌 APIs y Endpoints

### Rutas Principales Implementadas
```php
// Autenticación
GET  /login              # Formulario de login
POST /login              # Procesar login
GET  /logout             # Cerrar sesión

// Dashboard
GET  /dashboard          # Panel principal

// Artículos
GET  /articles           # Lista paginada
GET  /articles/create    # Formulario crear
POST /articles/create    # Procesar creación
GET  /articles/edit/{id} # Formulario editar (pendiente)
POST /articles/edit/{id} # Procesar edición (pendiente)
DELETE /articles/{id}    # Eliminar artículo (pendiente)

// Categorías
GET  /categories         # Lista + formulario crear
POST /categories         # Procesar creación

// Upload de archivos
POST /api/upload-image   # Subir imagen desde editor
```

### APIs Pendientes de Implementar
```php
// REST API para frontend público
GET  /api/articles                    # Lista pública
GET  /api/articles/{slug}             # Artículo individual
GET  /api/categories                  # Lista de categorías
GET  /api/tags                        # Lista de tags
POST /api/comments                    # Crear comentario
POST /api/newsletter/subscribe        # Suscribirse

// APIs administrativas
GET  /api/admin/stats                 # Estadísticas completas
GET  /api/admin/users                 # Gestión usuarios
POST /api/admin/media                 # Subir archivos
GET  /api/admin/analytics/{period}    # Métricas por período
```

---

## 🔒 Seguridad

### Medidas Implementadas
- [x] **Hash de contraseñas:** bcrypt con salt automático
- [x] **Validación de sesiones:** Verificación en cada request
- [x] **Escape de HTML:** htmlspecialchars() en todas las salidas
- [x] **Prepared statements:** Prevención de SQL injection
- [x] **CSRF protection:** Token en formularios (pendiente implementar)
- [x] **Validación de archivos:** Tipos MIME y extensiones permitidas

### Medidas de Seguridad Pendientes
- [ ] **Rate limiting:** Prevenir ataques de fuerza bruta
- [ ] **2FA:** Autenticación de dos factores
- [ ] **HTTPS enforcement:** Redirection automática
- [ ] **Content Security Policy:** Headers de seguridad
- [ ] **Input sanitization:** Validación más estricta
- [ ] **Password policies:** Complejidad mínima
- [ ] **Audit logging:** Registro de acciones administrativas

### Configuración de Seguridad Recomendada
```php
// php.ini settings
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
session.cookie_samesite = "Strict"

// Headers de seguridad
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000
```

---

## 🚀 Instalación y Configuración

### Requisitos del Sistema
```
PHP: >= 8.0
MySQL: >= 8.0 o MariaDB >= 10.4
Apache: >= 2.4 (con mod_rewrite)
Extensiones PHP requeridas:
  - pdo_mysql
  - session
  - json
  - mbstring
  - fileinfo
  - gd (para manipulación de imágenes)
```

### Instalación Automática
```powershell
# 1. Ejecutar script de instalación
.\install-san-luis-opina.ps1

# 2. Ejecutar script SQL en phpMyAdmin
# (copiar contenido de mysql-database.sql)

# 3. Configurar permisos de archivos
chmod 755 public/uploads/
chmod 644 public/assets/

# 4. Verificar .htaccess
# Asegurar que mod_rewrite esté habilitado
```

### Configuración Manual
```php
// public/index.php - Configuración de BD
$db_config = [
    'host' => 'localhost',
    'dbname' => 'san_luis_opina',
    'user' => 'root',           // Cambiar en producción
    'pass' => '',               // Configurar contraseña
    'charset' => 'utf8mb4'
];

// Configuraciones adicionales
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024);    // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);
define('POSTS_PER_PAGE', 10);
define('DEBUG_MODE', false);                    // true solo en desarrollo
```

### Variables de Entorno (Recomendado)
```bash
# .env file
DB_HOST=localhost
DB_NAME=san_luis_opina
DB_USER=cms_user
DB_PASS=secure_password_here
APP_ENV=production
APP_DEBUG=false
UPLOAD_PATH=/uploads/
CDN_URL=https://cdn.sanluisopina.com
```

---

## 👨‍💻 Guía de Desarrollo

### Estándares de Código
```php
// PSR-12 compliant
class ArticleManager 
{
    private PDO $pdo;
    
    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }
    
    public function createArticle(array $data): int 
    {
        // Validación
        $this->validateArticleData($data);
        
        // Preparar consulta
        $stmt = $this->pdo->prepare("
            INSERT INTO articles (title, slug, content, author_id, status) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        // Ejecutar
        $stmt->execute([
            $data['title'],
            $this->generateSlug($data['title']),
            $data['content'],
            $data['author_id'],
            $data['status'] ?? 'draft'
        ]);
        
        return $this->pdo->lastInsertId();
    }
}
```

### Estructura de Templates
```php
<?php 
// templates/articles/create.php
$title = 'Crear Artículo - San Luis Opina';
ob_start();
?>

<div class="content-wrapper">
    <h1><?= htmlspecialchars($title) ?></h1>
    
    <form method="POST" action="/articles" class="article-form">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        
        <div class="form-group">
            <label for="title">Título</label>
            <input type="text" id="title" name="title" required maxlength="255">
        </div>
        
        <!-- Más campos del formulario -->
        
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
</div>

<?php 
$content = ob_get_clean();
include '../base.php'; 
?>
```

### Testing (Pendiente Implementar)
```php
// tests/ArticleTest.php
class ArticleTest extends PHPUnit\Framework\TestCase 
{
    public function testCreateArticle() 
    {
        $article = new Article();
        $result = $article->create([
            'title' => 'Test Article',
            'content' => '<p>Test content</p>',
            'author_id' => 1
        ]);
        
        $this->assertIsInt($result);
        $this->assertGreaterThan(0, $result);
    }
}
```

---

## 🗺️ Roadmap de Desarrollo

### Fase 1: Core CMS (Completado ✅)
- Sistema de autenticación básico
- CRUD de artículos y categorías
- Dashboard administrativo
- Editor WYSIWYG
- Diseño responsive

### Fase 2: Funcionalidades Avanzadas (En desarrollo 🚧)
**Tiempo estimado: 2-3 semanas**

#### Sprint 2.1 (Semana 1)
- [ ] Sistema completo de tags
- [ ] Gestor de medios con galería
- [ ] Editor avanzado con autoguardado
- [ ] SEO básico (meta tags)

#### Sprint 2.2 (Semana 2)
- [ ] Sistema de comentarios
- [ ] Gestión completa de usuarios
- [ ] Permisos granulares
- [ ] Audit trail

#### Sprint 2.3 (Semana 3)
- [ ] Frontend público básico
- [ ] APIs REST principales
- [ ] Sistema de búsqueda
- [ ] Cache básico

### Fase 3: Optimización y Escalabilidad (Futuro)
**Tiempo estimado: 3-4 semanas**

#### Sprint 3.1
- [ ] Performance optimization
- [ ] Advanced caching
- [ ] CDN integration
- [ ] Database optimization

#### Sprint 3.2
- [ ] Analytics avanzados
- [ ] Newsletter completo
- [ ] Multiidioma
- [ ] PWA features

#### Sprint 3.3
- [ ] Mobile app API
- [ ] Advanced SEO
- [ ] Social media integration
- [ ] Enterprise features

### Fase 4: Ecosystem (A largo plazo)
- [ ] Plugin system
- [ ] Theme marketplace
- [ ] Cloud deployment
- [ ] SaaS platform

---

## 📈 Métricas y KPIs

### Métricas Técnicas
- **Performance:** < 2s tiempo de carga
- **Uptime:** 99.9% disponibilidad
- **Security:** 0 vulnerabilidades críticas
- **Code Quality:** 90%+ test coverage

### Métricas de Usuario
- **Usabilidad:** < 3 clics para cualquier acción
- **Adoption:** 100% usuarios usando nuevas features
- **Satisfaction:** > 4.5/5 en feedback
- **Productivity:** 50% reducción en tiempo de publicación

---

## 🔧 Troubleshooting Común

### Problemas de Instalación
```bash
# Error: Apache mod_rewrite no habilitado
sudo a2enmod rewrite
sudo systemctl restart apache2

# Error: Permisos de archivos
chmod -R 755 public/uploads/
chown -R www-data:www-data public/uploads/

# Error: Conexión a MySQL
# Verificar credenciales en public/index.php
# Verificar que MySQL esté corriendo
```

### Problemas de Desarrollo
```php
// Error: Sesión no persiste
ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 86400);

// Error: Upload de archivos falla
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
ini_set('max_execution_time', 300);
```

---

## 📞 Soporte y Contribución

### Contacto Técnico
- **Arquitecto de Software:** [Tu nombre]
- **DevOps:** [Encargado infraestructura]
- **QA:** [Encargado testing]

### Proceso de Contribución
1. Fork del repositorio
2. Crear feature branch
3. Implementar cambios + tests
4. Pull request con descripción detallada
5. Code review
6. Merge a develop branch

### Convenciones de Git
```bash
# Formato de commits
feat: add user management system
fix: resolve login session timeout
docs: update API documentation
style: improve dashboard responsive design
refactor: optimize database queries
test: add article creation test suite
```

---

## 📜 Licencia y Copyright

**San Luis Opina CMS**
Copyright © 2024 San Luis Opina. Todos los derechos reservados.

Este sistema está desarrollado específicamente para San Luis Opina y contiene código propietario. 

**Tecnologías de terceros utilizadas:**
- Bootstrap 5.1.3 (MIT License)
- Font Awesome 6.x (MIT License)  
- Quill.js (BSD License)
- Inter Font (SIL Open Font License)

---

## 📊 Estado Actual del Proyecto

### Progreso General: 45% Completado

**Backend:** 60% ✅
- ✅ Router y arquitectura base
- ✅ Sistema de autenticación
- ✅ CRUD artículos básico
- ✅ CRUD categorías
- ⏳ Sistema de tags (70%)
- ❌ Gestor de medios
- ❌ Sistema de comentarios
- ❌ APIs REST

**Frontend:** 70% ✅
- ✅ Dashboard administrativo
- ✅ Diseño responsive
- ✅ Editor WYSIWYG
- ✅ Sistema de navegación
- ❌ Frontend público
- ❌ SEO optimization

**Database:** 90% ✅
- ✅ Esquema principal diseñado
- ✅ Relaciones definidas
- ✅ Índices optimizados
- ⏳ Stored procedures (50%)
- ❌ Views para reporting

**Security:** 40% ✅
- ✅ Hash de contraseñas
- ✅ SQL injection prevention
- ✅ XSS protection básico
- ❌ CSRF tokens
- ❌ Rate limiting
- ❌ 2FA

---

*Última actualización: 18 de Agosto de 2024*
*Versión del documento: 1.0*