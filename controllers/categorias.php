<?php
// Incluimos la configuración y los modelos necesarios
require_once '../includes/config.php';
require_once '../models/Categoria.php';

// Inicializamos la categoría
$categoria = new Categoria($conn);

// Procesamos la acción solicitada
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

switch ($action) {
    case 'list':
        // Obtenemos todas las categorías
        $result = $categoria->getAll();
        $categorias = $result->get_result();
        
        // Los mostramos en la vista
        include '../views/categorias/index.php';
        break;
        
    case 'view':
        // Verificamos que exista el ID
        if(isset($_GET['id'])) {
            $categoria->id = $_GET['id'];
            // Obtenemos la categoría
            if($categoria->getSingle()) {
                include '../views/categorias/view.php';
            } else {
                $_SESSION['mensaje'] = 'Categoría no encontrada';
                header('Location: categorias.php');
                exit;
            }
        } else {
            $_SESSION['mensaje'] = 'ID no especificado';
            header('Location: categorias.php');
            exit;
        }
        break;
        
    case 'create_form':
        // Mostramos el formulario de creación
        include '../views/categorias/crear.php';
        break;
        
    case 'create':
        // Procesamos la creación de la categoría
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtenemos los datos del formulario
            $categoria->nombre = $_POST['nombre'];
            $categoria->descripcion = $_POST['descripcion'];
            
            // Creamos la categoría
            if($categoria->create()) {
                $_SESSION['mensaje'] = "Categoría creada correctamente";
                header('Location: categorias.php');
                exit;
            } else {
                $_SESSION['mensaje'] = "Error al crear la categoría";
                include '../views/categorias/crear.php';
            }
        }
        break;
        
    case 'edit_form':
        // Verificamos que exista el ID
        if(isset($_GET['id'])) {
            $categoria->id = $_GET['id'];
            // Obtenemos la categoría
            if($categoria->getSingle()) {
                include '../views/categorias/editar.php';
            } else {
                $_SESSION['mensaje'] = 'Categoría no encontrada';
                header('Location: categorias.php');
                exit;
            }
        } else {
            $_SESSION['mensaje'] = 'ID no especificado';
            header('Location: categorias.php');
            exit;
        }
        break;
        
    case 'update':
        // Procesamos la actualización de la categoría
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtenemos los datos del formulario
            $categoria->id = $_POST['id'];
            $categoria->nombre = $_POST['nombre'];
            $categoria->descripcion = $_POST['descripcion'];
            
            // Actualizamos la categoría
            if($categoria->update()) {
                $_SESSION['mensaje'] = "Categoría actualizada correctamente";
                header('Location: categorias.php');
                exit;
            } else {
                $_SESSION['mensaje'] = "Error al actualizar la categoría";
                include '../views/categorias/editar.php';
            }
        }
        break;
        
    case 'delete':
        // Verificamos que exista el ID
        if(isset($_GET['id'])) {
            $categoria->id = $_GET['id'];
            
            // Eliminamos la categoría
            if($categoria->delete()) {
                $_SESSION['mensaje'] = "Categoría eliminada correctamente";
            } else {
                $_SESSION['mensaje'] = "Error al eliminar la categoría. Asegúrese de que no haya artículos asociados a esta categoría.";
            }
        } else {
            $_SESSION['mensaje'] = "ID no especificado";
        }
        
        header('Location: categorias.php');
        exit;
        break;
        
    case 'search':
        // Buscamos categorías
        if(isset($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
            $result = $categoria->search($keyword);
            $categorias = $result->get_result();
            
            include '../views/categorias/index.php';
        } else {
            header('Location: categorias.php');
            exit;
        }
        break;
        
    default:
        header('Location: categorias.php');
        exit;
        break;
}
?>
