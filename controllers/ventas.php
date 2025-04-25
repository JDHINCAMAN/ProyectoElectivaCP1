<?php
// Incluimos la configuración y los modelos necesarios
require_once '../includes/config.php';
require_once '../models/Venta.php';
require_once '../models/Articulo.php';

// Inicializamos la venta
$venta = new Venta($conn);

// Procesamos la acción solicitada
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

switch ($action) {
    case 'list':
        // Obtenemos todas las ventas
        $result = $venta->getAll();
        $ventas = $result->get_result();
        
        // Los mostramos en la vista
        include '../views/ventas/index.php';
        break;
        
    case 'view':
        // Verificamos que exista el ID
        if(isset($_GET['id'])) {
            $venta->id = $_GET['id'];
            // Obtenemos la venta
            if($venta->getSingle()) {
                include '../views/ventas/view.php';
            } else {
                $_SESSION['mensaje'] = 'Venta no encontrada';
                header('Location: ventas.php');
                exit;
            }
        } else {
            $_SESSION['mensaje'] = 'ID no especificado';
            header('Location: ventas.php');
            exit;
        }
        break;
        
    case 'create_form':
        // Obtenemos los artículos disponibles
        $articulo = new Articulo($conn);
        $result = $articulo->getAll();
        $articulos = $result->get_result();
        
        // Mostramos el formulario de creación
        include '../views/ventas/crear.php';
        break;
        
    case 'create':
        // Procesamos la creación de la venta
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtenemos los datos del formulario
            $venta->cliente = $_POST['cliente'];
            $venta->total = $_POST['total'];
            
            // Procesamos el detalle de la venta
            $detalle = [];
            $articulos = $_POST['articulos'];
            $cantidades = $_POST['cantidades'];
            $precios = $_POST['precios'];
            
            for($i = 0; $i < count($articulos); $i++) {
                if($cantidades[$i] > 0) {
                    $detalle[] = [
                        'articulo_id' => $articulos[$i],
                        'cantidad' => $cantidades[$i],
                        'precio_unitario' => $precios[$i],
                        'subtotal' => $cantidades[$i] * $precios[$i]
                    ];
                }
            }
            
            $venta->detalle = $detalle;
            
            // Creamos la venta
            if($venta->create()) {
                $_SESSION['mensaje'] = "Venta creada correctamente";
                header('Location: ventas.php');
                exit;
            } else {
                $_SESSION['mensaje'] = "Error al crear la venta";
                include '../views/ventas/crear.php';
            }
        }
        break;
        
    case 'anular':
        // Verificamos que exista el ID
        if(isset($_GET['id'])) {
            $venta->id = $_GET['id'];
            
            // Anulamos la venta
            if($venta->anular()) {
                $_SESSION['mensaje'] = "Venta anulada correctamente";
            } else {
                $_SESSION['mensaje'] = "Error al anular la venta";
            }
        } else {
            $_SESSION['mensaje'] = "ID no especificado";
        }
        
        header('Location: ventas.php');
        exit;
        break;
        
    case 'search':
        // Buscamos ventas por rango de fechas
        if(isset($_GET['desde']) && isset($_GET['hasta'])) {
            $desde = $_GET['desde'];
            $hasta = $_GET['hasta'];
            
            $result = $venta->getByDateRange($desde, $hasta);
            $ventas = $result->get_result();
            
            include '../views/ventas/index.php';
        } else {
            header('Location: ventas.php');
            exit;
        }
        break;
        
    case 'stats':
        // Obtenemos las estadísticas de ventas
        $stats = $venta->getStats();
        include '../views/ventas/estadisticas.php';
        break;
        
    default:
        header('Location: ventas.php');
        exit;
        break;
}
?>
