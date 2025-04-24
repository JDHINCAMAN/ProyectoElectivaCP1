<?php 
include '../includes/header.php';

// Obtener categorías para el select
require_once '../includes/config.php';
$categorias_query = "SELECT id, nombre FROM categorias ORDER BY nombre";
$categorias_result = $conn->query($categorias_query);
?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-edit mr-2"></i> Editar Artículo</h2>
        <a href="articulos.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="articulos.php?action=update" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $articulo->id ?>">
            
            <div class="form-group">
                <label for="codigo" class="form-label">Código *</label>
                <input type="text" id="codigo" name="codigo" class="form-control" value="<?= $articulo->codigo ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= $articulo->nombre ?>" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="4"><?= $articulo->descripcion ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="precio" class="form-label">Precio *</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="number" id="precio" name="precio" class="form-control" step="0.01" min="0" value="<?= $articulo->precio ?>" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="stock" class="form-label">Stock *</label>
                <input type="number" id="stock" name="stock" class="form-control" min="0" value="<?= $articulo->stock ?>" required>
            </div>
            
            <div class="form-group">
                <label for="categoria_id" class="form-label">Categoría</label>
                <select id="categoria_id" name="categoria_id" class="form-control">
                    <option value="">Seleccionar categoría</option>
                    <?php while($categoria = $categorias_result->fetch_assoc()): ?>
                        <option value="<?= $categoria['id'] ?>" <?= $articulo->categoria_id == $categoria['id'] ? 'selected' : '' ?>>
                            <?= $categoria['nombre'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="imagen" class="form-label">Imagen</label>
                <?php if(!empty($articulo->imagen)): ?>
                    <div class="mb-2">
                    <img src="../assets/img/<?= $articulo->imagen ?>" alt="<?= $articulo->nombre ?>" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                        <p class="small text-muted">Imagen actual. Sube una nueva imagen para reemplazarla.</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*" data-preview="#imagePreview">
                <div id="imagePreview" class="mt-2"></div>
            </div>
            
            <div class="form-group text-right">
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-eraser"></i> Cancelar Cambios
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Actualizar
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>