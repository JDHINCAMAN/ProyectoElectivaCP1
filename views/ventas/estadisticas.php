<?php include '../includes/header.php'; ?>

<div class="card">
    <div class="card-header">
        <h2><i class="fas fa-chart-bar mr-2"></i> Estadísticas de Ventas</h2>
        <a href="ventas.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
    
    <div class="card-body">
        <div class="row">
            <!-- Ventas Totales -->
            <div class="col-md-3 mb-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Ventas</h5>
                        <h2 class="card-text"><?= $stats['totales']['total_ventas'] ?></h2>
                        <p class="card-text">Monto Total: $<?= number_format($stats['totales']['total_monto'], 2) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Ventas de Hoy -->
            <div class="col-md-3 mb-4">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Ventas de Hoy</h5>
                        <h2 class="card-text"><?= $stats['hoy']['total_ventas'] ?></h2>
                        <p class="card-text">Monto: $<?= number_format($stats['hoy']['total_monto'], 2) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Ventas de la Semana -->
            <div class="col-md-3 mb-4">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Ventas de la Semana</h5>
                        <h2 class="card-text"><?= $stats['semana']['total_ventas'] ?></h2>
                        <p class="card-text">Monto: $<?= number_format($stats['semana']['total_monto'], 2) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Ventas del Mes -->
            <div class="col-md-3 mb-4">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Ventas del Mes</h5>
                        <h2 class="card-text"><?= $stats['mes']['total_ventas'] ?></h2>
                        <p class="card-text">Monto: $<?= number_format($stats['mes']['total_monto'], 2) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gráficos -->
        <div class="row mt-4">
            <!-- Gráfico de ventas por día -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Ventas por Día (Últimos 7 días)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasDiaChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico de ventas por mes -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Ventas por Mes</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="ventasMesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de ventas por día
    const ctxDia = document.getElementById('ventasDiaChart').getContext('2d');
    new Chart(ctxDia, {
        type: 'line',
        data: {
            labels: ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'],
            datasets: [{
                label: 'Ventas por día',
                data: [12, 19, 3, 5, 2, 3, 7],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1,
                fill: true,
                backgroundColor: 'rgba(75, 192, 192, 0.1)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad de Ventas'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Días de la Semana'
                    }
                }
            }
        }
    });
    
    // Gráfico de ventas por mes
    const ctxMes = document.getElementById('ventasMesChart').getContext('2d');
    new Chart(ctxMes, {
        type: 'doughnut',
        data: {
            labels: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio'],
            datasets: [{
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?> 