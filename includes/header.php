<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Venta de Artículos</title>
    <!-- Google Fonts - Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="/tienda/assets/css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="/tienda/index.php" class="navbar-brand">
                <i class="fas fa-store mr-2"></i> TiendaApp
            </a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="/tienda/index.php" class="nav-link">
                        <i class="fas fa-home"></i> Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/tienda/controllers/articulos.php" class="nav-link">
                        <i class="fas fa-box"></i> Artículos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/tienda/controllers/categorias.php" class="nav-link">
                        <i class="fas fa-tags"></i> Categorías
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/tienda/controllers/ventas.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i> Ventas
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/tienda/views/carrito.php" class="nav-link">
                        <i class="fas fa-shopping-basket"></i> Carrito
                        <span class="badge badge-danger cart-counter" style="display:none;">0</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <!-- Contenido principal -->
    <main class="main">
        <div class="container">
            <?php if(isset($_SESSION['mensaje'])): ?>
                <div class="alert alert-<?= isset($_SESSION['tipo']) ? $_SESSION['tipo'] : 'info' ?>">
                    <?= $_SESSION['mensaje'] ?>
                    <button type="button" class="close">&times;</button>
                </div>
                <?php 
                // Limpiar mensajes
                unset($_SESSION['mensaje']);
                unset($_SESSION['tipo']);
                ?>
            <?php endif; ?>