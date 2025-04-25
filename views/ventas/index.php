<?php include '../includes/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-shopping-cart mr-2"></i> Ventas</h2>
        <div>
            <a href="ventas.php?action=stats" class="btn btn-info mr-2">
                <i class="fas fa-chart-bar"></i> Estadísticas
            </a>
            <a href="ventas.php?action=create_form" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Venta
            </a>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Buscador por fecha -->
        <form action="ventas.php" method="GET" class="search-form mb-4">
            <input type="hidden" name="action" value="search">
            <div class="row">
                <div class="col-md-4">
                    <label>Desde:</label>
                    <input type="date" name="desde" class="form-control" value="<?= isset($_GET['desde']) ? $_GET['desde'] : '' ?>">
                </div>
                <div class="col-md-4">
                    <label>Hasta:</label>
                    <input type="date" name="hasta" class="form-control" value="<?= isset($_GET['hasta']) ? $_GET['hasta'] : '' ?>">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </form>
        
        <?php if($ventas->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($venta = $ventas->fetch_assoc()): ?>
                            <tr>
                                <td><?= $venta['id'] ?></td>
                                <td><?= $venta['cliente'] ?></td>
                                <td class="price-format"><?= $venta['total'] ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                                <td>
                                    <a href="ventas.php?action=view&id=<?= $venta['id'] ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> Ver
                                    </a>
                                    <a href="ventas.php?action=anular&id=<?= $venta['id'] ?>" class="btn btn-sm btn-danger delete-btn" data-confirm="¿Estás seguro de anular esta venta?">
                                        <i class="fas fa-times"></i> Anular
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
                <p>No se encontraron ventas.</p>
                <?php if(isset($_GET['desde']) || isset($_GET['hasta'])): ?>
                    <a href="ventas.php" class="btn btn-primary mt-2">Ver todas las ventas</a>
                <?php else: ?>
                    <a href="ventas.php?action=create_form" class="btn btn-primary mt-2">Crear la primera venta</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
