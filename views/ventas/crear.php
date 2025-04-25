<?php 
include '../includes/header.php'; 
?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-plus mr-2"></i> Nueva Venta</h2>
        <a href="ventas.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card-body">
        <form action="ventas.php?action=create" method="POST" id="ventaForm">
            <div class="form-group">
                <label for="cliente" class="form-label">Cliente *</label>
                <input type="text" id="cliente" name="cliente" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Artículos</label>
                <div class="table-responsive">
                    <table class="table table-bordered" id="articulosTable">
                        <thead>
                            <tr>
                                <th>Artículo</th>
                                <th>Cantidad</th>
                                <th>Precio Unitario</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="articulosBody">
                            <!-- Los artículos se agregarán dinámicamente aquí -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                                <td colspan="2">
                                    <input type="number" id="total" name="total" class="form-control" readonly>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-success" id="addArticulo">
                    <i class="fas fa-plus"></i> Agregar Artículo
                </button>
            </div>
            
            <div class="form-group text-right">
                <button type="reset" class="btn btn-secondary">
                    <i class="fas fa-eraser"></i> Limpiar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Guardar Venta
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Template para fila de artículo -->
<template id="articuloTemplate">
    <tr>
        <td>
            <select name="articulos[]" class="form-control articulo-select" required>
                <option value="">Seleccionar artículo</option>
                <?php while($articulo = $articulos->fetch_assoc()): ?>
                    <option value="<?= $articulo['id'] ?>" data-precio="<?= $articulo['precio'] ?>">
                        <?= $articulo['nombre'] ?> - $<?= $articulo['precio'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </td>
        <td>
            <input type="number" name="cantidades[]" class="form-control cantidad" min="1" value="1" required>
        </td>
        <td>
            <input type="number" name="precios[]" class="form-control precio" step="0.01" min="0" required>
        </td>
        <td>
            <input type="number" class="form-control subtotal" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-row">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addButton = document.getElementById('addArticulo');
    const articulosBody = document.getElementById('articulosBody');
    const template = document.getElementById('articuloTemplate');
    const totalInput = document.getElementById('total');
    
    // Agregar nueva fila
    addButton.addEventListener('click', function() {
        const clone = template.content.cloneNode(true);
        articulosBody.appendChild(clone);
        updateTotal();
    });
    
    // Eliminar fila
    articulosBody.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            updateTotal();
        }
    });
    
    // Actualizar subtotal y total
    articulosBody.addEventListener('change', function(e) {
        if (e.target.matches('.articulo-select, .cantidad, .precio')) {
            const row = e.target.closest('tr');
            const cantidad = row.querySelector('.cantidad').value;
            const precio = row.querySelector('.precio').value;
            const subtotal = cantidad * precio;
            row.querySelector('.subtotal').value = subtotal.toFixed(2);
            updateTotal();
        }
    });
    
    // Actualizar precio al seleccionar artículo
    articulosBody.addEventListener('change', function(e) {
        if (e.target.matches('.articulo-select')) {
            const precio = e.target.options[e.target.selectedIndex].dataset.precio;
            e.target.closest('tr').querySelector('.precio').value = precio;
            updateTotal();
        }
    });
    
    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        totalInput.value = total.toFixed(2);
    }
    
    // Agregar primera fila al cargar
    addButton.click();
});
</script>

<?php include '../includes/footer.php'; ?>
