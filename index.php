<?php
// Incluimos la configuración
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Iniciamos sesión
session_start();

// Consultamos los artículos destacados
$sql = "SELECT a.*, c.nombre as categoria_nombre 
        FROM articulos a
        LEFT JOIN categorias c ON a.categoria_id = c.id
        WHERE a.stock > 0
        ORDER BY a.fecha_creacion DESC
        LIMIT 8";
$result = $conn->query($sql);

// Consultamos las categorías
$sql_categorias = "SELECT c.*, COUNT(a.id) as articulos_count 
                FROM categorias c
                LEFT JOIN articulos a ON c.id = a.categoria_id
                GROUP BY c.id
                ORDER BY articulos_count DESC
                LIMIT 5";
$categorias_result = $conn->query($sql_categorias);

// Incluimos el header
include 'includes/header.php';
?>

<!-- Hero Section -->
<div class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Sistema de Venta de Artículos</h1>
        <p class="hero-text">Gestiona tus productos y ventas de manera eficiente</p>
        <div class="hero-buttons">
            <a href="/ProyectoElectivaCP1/controllers/articulos.php" class="btn btn-primary btn-lg">
                <i class="fas fa-box"></i> Ver Artículos
            </a>
            <a href="/ProyectoElectivaCP1/controllers/ventas.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-shopping-cart"></i> Gestionar Ventas
            </a>
        </div>
    </div>
</div>

<!-- Destacados Section -->
<section class="section">
    <div class="section-header">
        <h2><i class="fas fa-star"></i> Productos Destacados</h2>
        <a href="/ProyectoElectivaCP1/controllers/articulos.php" class="btn btn-primary">Ver Todos</a>
    </div>
    
    <?php if($result->num_rows > 0): ?>
        <div class="grid">
            <?php while($articulo = $result->fetch_assoc()): ?>
                <div class="product-card fade-in">
                    <img src="<?= !empty($articulo['imagen']) ? 'assets/img/' . $articulo['imagen'] : 'assets/img/no-image.jpg' ?>" alt="<?= $articulo['nombre'] ?>" class="product-img">
                    <div class="product-body">
                        <h3 class="product-title"><?= $articulo['nombre'] ?></h3>
                        <div class="product-price price-format"><?= $articulo['precio'] ?></div>
                        <?php if(!empty($articulo['categoria_nombre'])): ?>
                            <span class="product-category"><?= $articulo['categoria_nombre'] ?></span>
                        <?php endif; ?>
                        <p><?= substr($articulo['descripcion'], 0, 100) . (strlen($articulo['descripcion']) > 100 ? '...' : '') ?></p>
                        <div class="mt-2">
                            <span class="badge badge-success">
                                En stock (<?= $articulo['stock'] ?>)
                            </span>
                        </div>
                    </div>
                    <div class="product-footer">
                        <a href="/ProyectoElectivaCP1/controllers/articulos.php?action=view&id=<?= $articulo['id'] ?>" class="btn btn-sm btn-secondary">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                        <button class="btn btn-sm btn-primary" onclick="addToCart(<?= $articulo['id'] ?>, '<?= htmlspecialchars($articulo['nombre'], ENT_QUOTES) ?>', <?= $articulo['precio'] ?>)">
                            <i class="fas fa-cart-plus"></i> Añadir
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <p>No hay productos disponibles actualmente.</p>
            <a href="/ProyectoElectivaCP1/controllers/articulos.php?action=create_form" class="btn btn-primary mt-2">Crear el primer artículo</a>
        </div>
    <?php endif; ?>
</section>

<!-- Categorías Section -->
<section class="section bg-light">
    <div class="section-header">
        <h2><i class="fas fa-tags"></i> Categorías</h2>
        <a href="/ProyectoElectivaCP1/controllers/categorias.php" class="btn btn-primary">Ver Todas</a>
    </div>
    
    <?php if($categorias_result->num_rows > 0): ?>
        <div class="categories-grid">
            <?php while($categoria = $categorias_result->fetch_assoc()): ?>
                <div class="category-card fade-in">
                    <div class="category-body">
                        <h3 class="category-title"><?= $categoria['nombre'] ?></h3>
                        <p><?= substr($categoria['descripcion'], 0, 100) . (strlen($categoria['descripcion']) > 100 ? '...' : '') ?></p>
                        <div class="category-stats">
                            <span class="badge badge-primary">
                                <?= $categoria['articulos_count'] ?> Artículos
                            </span>
                        </div>
                    </div>
                    <div class="category-footer">
                        <a href="/ProyectoElectivaCP1/controllers/articulos.php?action=search&categoria_id=<?= $categoria['id'] ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-search"></i> Ver Artículos
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle fa-2x mb-3"></i>
            <p>No hay categorías disponibles actualmente.</p>
            <a href="/ProyectoElectivaCP1/controllers/categorias.php?action=create_form" class="btn btn-primary mt-2">Crear la primera categoría</a>
        </div>
    <?php endif; ?>
</section>

<!-- Features Section -->
<section class="section">
    <div class="section-header text-center">
        <h2><i class="fas fa-check-circle"></i> Características</h2>
    </div>
    
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-box"></i>
            </div>
            <h3>Gestión de Artículos</h3>
            <p>Administra fácilmente tu inventario, añade, edita y elimina productos.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-tags"></i>
            </div>
            <h3>Categorización</h3>
            <p>Organiza tus productos en categorías para una mejor gestión.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h3>Ventas</h3>
            <p>Registra ventas y mantén un control del historial de transacciones.</p>
        </div>
        
        <div class="feature-card">
            <div class="feature-icon">
                <i class="fas fa-search"></i>
            </div>
            <h3>Búsqueda</h3>
            <p>Encuentra rápidamente productos mediante búsquedas avanzadas.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>