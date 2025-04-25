<?php include '../includes/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-tags mr-2"></i> Categorías</h2>
        <a href="categorias.php?action=create_form" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nueva Categoría
        </a>
    </div>
    
    <div class="card-body">
        <!-- Buscador -->
        <form action="categorias.php" method="GET" class="search-form mb-4">
            <input type="hidden" name="action" value="search">
            <input type="text" name="keyword" class="form-control search-input" placeholder="Buscar categorías..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
        
        <?php if($categorias->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($categoria = $categorias->fetch_assoc()): ?>
                            <tr>
                                <td><?= $categoria['nombre'] ?></td>
                                <td><?= $categoria['descripcion'] ?></td>
                                <td>
                                    <a href="categorias.php?action=edit_form&id=<?= $categoria['id'] ?>" class="btn btn-sm btn-secondary">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="categorias.php?action=delete&id=<?= $categoria['id'] ?>" class="btn btn-sm btn-danger delete-btn" data-confirm="¿Estás seguro de eliminar '<?= $categoria['nombre'] ?>'?">
                                        <i class="fas fa-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle fa-2x mb-3"></i>
                <p>No se encontraron categorías.</p>
                <?php if(isset($_GET['keyword'])): ?>
                    <a href="categorias.php" class="btn btn-primary mt-2">Ver todas las categorías</a>
                <?php else: ?>
                    <a href="categorias.php?action=create_form" class="btn btn-primary mt-2">Crear la primera categoría</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
