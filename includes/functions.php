<?php
/**
 * Funciones útiles para el sistema de venta de artículos
 */

/**
 * Sanitiza una entrada
 * @param string $data Datos a sanitizar
 * @return string Datos sanitizados
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Genera una cadena aleatoria
 * @param int $length Longitud de la cadena
 * @return string Cadena aleatoria
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Formatea un precio a formato de moneda
 * @param float $price Precio
 * @param string $currency Moneda
 * @return string Precio formateado
 */
function formatPrice($price, $currency = '€') {
    return number_format($price, 2, ',', '.') . ' ' . $currency;
}

/**
 * Formatea una fecha en formato legible
 * @param string $date Fecha en formato Y-m-d H:i:s
 * @param string $format Formato deseado
 * @return string Fecha formateada
 */
function formatDate($date, $format = 'd/m/Y H:i') {
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Genera un código único para artículos
 * @param mysqli $conn Conexión a la base de datos
 * @param string $prefix Prefijo del código
 * @return string Código único
 */
function generateUniqueCode($conn, $prefix = 'ART') {
    $code = $prefix . '-' . date('Ym') . '-' . rand(1000, 9999);
    
    // Verificar que el código no exista
    $stmt = $conn->prepare("SELECT id FROM articulos WHERE codigo = ?");
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Si existe, generar otro
    if ($result->num_rows > 0) {
        return generateUniqueCode($conn, $prefix);
    }
    
    return $code;
}

/**
 * Obtiene el total de artículos en el carrito
 * @return int Total de artículos
 */
function getCartItemCount() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['cantidad'];
    }
    
    return $total;
}

/**
 * Calcula el total del carrito
 * @return float Total del carrito
 */
function getCartTotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['precio'] * $item['cantidad'];
    }
    
    return $total;
}

/**
 * Verifica si hay suficiente stock para un artículo
 * @param mysqli $conn Conexión a la base de datos
 * @param int $articuloId ID del artículo
 * @param int $cantidad Cantidad solicitada
 * @return bool True si hay suficiente stock, false en caso contrario
 */
function checkStock($conn, $articuloId, $cantidad) {
    $stmt = $conn->prepare("SELECT stock FROM articulos WHERE id = ?");
    $stmt->bind_param('i', $articuloId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['stock'] >= $cantidad;
    }
    
    return false;
}

/**
 * Actualiza el stock de un artículo
 * @param mysqli $conn Conexión a la base de datos
 * @param int $articuloId ID del artículo
 * @param int $cantidad Cantidad a restar del stock
 * @return bool True si se actualizó correctamente, false en caso contrario
 */
function updateStock($conn, $articuloId, $cantidad) {
    $stmt = $conn->prepare("UPDATE articulos SET stock = stock - ? WHERE id = ?");
    $stmt->bind_param('ii', $cantidad, $articuloId);
    return $stmt->execute();
}

/**
 * Registra una venta en la base de datos
 * @param mysqli $conn Conexión a la base de datos
 * @param string $cliente Nombre del cliente
 * @param array $items Artículos vendidos
 * @param float $total Total de la venta
 * @return int|bool ID de la venta o false en caso de error
 */
function registerSale($conn, $cliente, $items, $total) {
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Insertar la venta
        $stmt = $conn->prepare("INSERT INTO ventas (cliente, total) VALUES (?, ?)");
        $stmt->bind_param('sd', $cliente, $total);
        $stmt->execute();
        
        $ventaId = $conn->insert_id;
        
        // Insertar los detalles
        foreach ($items as $item) {
            $subtotal = $item['precio'] * $item['cantidad'];
            
            $stmt = $conn->prepare("INSERT INTO detalle_ventas (venta_id, articulo_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('iiddd', $ventaId, $item['id'], $item['cantidad'], $item['precio'], $subtotal);
            $stmt->execute();
            
            // Actualizar stock
            updateStock($conn, $item['id'], $item['cantidad']);
        }
        
        // Confirmar transacción
        $conn->commit();
        
        return $ventaId;
    } catch (Exception $e) {
        // Revertir cambios en caso de error
        $conn->rollback();
        return false;
    }
}

/**
 * Generar un enlace de paginación
 * @param string $url URL base
 * @param array $params Parámetros adicionales
 * @param int $page Página actual
 * @param int $totalPages Total de páginas
 * @return string HTML de la paginación
 */
function generatePagination($url, $params, $page, $totalPages) {
    $html = '<ul class="pagination">';
    
    // Construir la URL base con parámetros
    $queryString = '';
    foreach ($params as $key => $value) {
        if ($key != 'page') {
            $queryString .= "&{$key}={$value}";
        }
    }
    
    // Botón anterior
    if ($page > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a href="' . $url . '?page=' . ($page - 1) . $queryString . '" class="page-link">&laquo;</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link">&laquo;</span>';
        $html .= '</li>';
    }
    
    // Páginas
    $start = max(1, $page - 2);
    $end = min($totalPages, $page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            $html .= '<li class="page-item active">';
            $html .= '<span class="page-link">' . $i . '</span>';
            $html .= '</li>';
        } else {
            $html .= '<li class="page-item">';
            $html .= '<a href="' . $url . '?page=' . $i . $queryString . '" class="page-link">' . $i . '</a>';
            $html .= '</li>';
        }
    }
    
    // Botón siguiente
    if ($page < $totalPages) {
        $html .= '<li class="page-item">';
        $html .= '<a href="' . $url . '?page=' . ($page + 1) . $queryString . '" class="page-link">&raquo;</a>';
        $html .= '</li>';
    } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link">&raquo;</span>';
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    
    return $html;
}
?>