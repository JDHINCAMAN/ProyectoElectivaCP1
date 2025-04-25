<?php 
include '../includes/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-plus mr-2"></i> Nueva Categoría</h2>
        <a href="categorias.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="categorias.php?action=create" method="POST">
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="4"></textarea>
            </div>
            
            <div class="form-group text-right">
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
