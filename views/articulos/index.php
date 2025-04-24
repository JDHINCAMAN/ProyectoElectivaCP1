<?php include '../includes/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-box mr-2"></i> Artículos</h2>
        <a href="articulos.php?action=create_form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nuevo Artículo
        </a>
    </div>
    
    <div class="card-body">
        <!-- Buscador -->
        <form action="articulos.php" method="GET" class="search-form mb-4">
            <input type="hidden" name="action" value="search">
            <input type="text" name="keyword" class="form-control search-input" placeholder="Buscar artículos..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
        
        <?php if($articulos->num_rows > 0): ?>
            <div class="grid">
                <?php while($articulo = $articulos->fetch_assoc()): ?>
                    <div class="product-card fade-in">
                        <img src="<?= !empty($articulo['imagen']) ? '../assets/img/' . $articulo['imagen'] : '../assets/img/no-image.jpg' ?>" alt="<?= $articulo['nombre'] ?>" class="product-img">
                        <div class="product-body">
                            <h3 class="product-title"><?= $articulo['nombre'] ?></h3>
                            <div class="product-price price-format"><?= $articulo['precio'] ?></div>
                            <?php if(!empty($articulo['categoria_nombre'])): ?>
                                <span class="product-category"><?= $articulo['categoria_nombre'] ?></span>
                            <?php endif; ?>
                            <p><?= substr($articulo['descripcion'], 0, 100) . (strlen($articulo['descripcion']) > 100 ? '...' : '') ?></p>
                            <div class="mt-2">
                                <span class="badge <?= $articulo['stock'] > 0 ? 'badge-success' : 'badge-danger' ?>">
                                    <?= $articulo['stock'] > 0 ? 'En stock (' . $articulo['stock'] . ')' : 'Agotado' ?>
                                </span>
                            </div>
                        </div>
                        <div class="product-footer">
                            <div>
                                <a href="articulos.php?action=edit_form&id=<?= $articulo['id'] ?>" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <a href="articulos.php?action=delete&id=<?= $articulo['id'] ?>" class="btn btn-sm btn-danger delete-btn" data-confirm="¿Estás seguro de eliminar '<?= $articulo['nombre'] ?>'?">
                                    <i class="fas fa-trash"></i> Eliminar
                                </a>
                            </div>
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
                <p>No se encontraron artículos.</p>
                <?php if(isset($_GET['keyword'])): ?>
                    <a href="articulos.php" class="btn btn-primary mt-2">Ver todos los artículos</a>
                <?php else: ?>
                    <a href="articulos.php?action=create_form" class="btn btn-primary mt-2">Crear el primer artículo</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>