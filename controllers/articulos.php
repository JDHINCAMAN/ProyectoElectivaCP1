<?php
// Incluimos la configuración y los modelos necesarios
require_once '../includes/config.php';
require_once '../models/Articulo.php';

// Inicializamos el artículo
$articulo = new Articulo($conn);

// Procesamos la acción solicitada
$action = isset($_GET['action']) ? $_GET['action'] : 'list';

switch ($action) {
    case 'list':
        // Obtenemos todos los artículos
        $result = $articulo->getAll();
        $articulos = $result->get_result();
        
        // Los mostramos en la vista
        include '../views/articulos/index.php';
        break;
        
    case 'view':
        // Verificamos que exista el ID
        if(isset($_GET['id'])) {
            $articulo->id = $_GET['id'];
            // Obtenemos el artículo
            if($articulo->getSingle()) {
                include '../views/articulos/view.php';
            } else {
                $_SESSION['mensaje'] = 'Artículo no encontrado';
                header('Location: ../views/articulos/index.php');
                exit;
            }
        } else {
            $_SESSION['mensaje'] = 'ID no especificado';
            header('Location: ../views/articulos/index.php');
            exit;
        }
        break;
        
    case 'create_form':
        // Mostramos el formulario de creación
        include '../views/articulos/crear.php';
        break;
        
    case 'create':
        // Procesamos la creación del artículo
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtenemos los datos del formulario
            $articulo->codigo = $_POST['codigo'];
            $articulo->nombre = $_POST['nombre'];
            $articulo->descripcion = $_POST['descripcion'];
            $articulo->precio = $_POST['precio'];
            $articulo->stock = $_POST['stock'];
            $articulo->categoria_id = $_POST['categoria_id'];
            
            // Procesamos la imagen si existe
            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $target_dir = "../assets/img/";
                $filename = basename($_FILES["imagen"]["name"]);
                $target_file = $target_dir . $filename;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Verificamos que sea una imagen real
                $check = getimagesize($_FILES["imagen"]["tmp_name"]);
                if($check !== false) {
                    // Verificamos tamaño y formato
                    if ($_FILES["imagen"]["size"] > 500000) {
                        $_SESSION['mensaje'] = "El archivo es demasiado grande.";
                    } elseif($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                        $_SESSION['mensaje'] = "Solo se permiten archivos JPG, JPEG, PNG & GIF.";
                    } else {
                        // Si todo está bien, subimos la imagen
                        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
                            $articulo->imagen = $filename;
                        } else {
                            $_SESSION['mensaje'] = "Hubo un error al subir el archivo.";
                        }
                    }
                } else {
                    $_SESSION['mensaje'] = "El archivo no es una imagen.";
                }
            }
            
            // Creamos el artículo
            if($articulo->create()) {
                $_SESSION['mensaje'] = "Artículo creado correctamente";
                header('Location: articulos.php');
                exit;
            } else {
                $_SESSION['mensaje'] = "Error al crear el artículo";
                include '../views/articulos/crear.php';
            }
        }
        break;
        
    case 'edit_form':
        // Verificamos que exista el ID
        if(isset($_GET['id'])) {
            $articulo->id = $_GET['id'];
            // Obtenemos el artículo
            if($articulo->getSingle()) {
                include '../views/articulos/editar.php';
            } else {
                $_SESSION['mensaje'] = 'Artículo no encontrado';
                header('Location: articulos.php');
                exit;
            }
        } else {
            $_SESSION['mensaje'] = 'ID no especificado';
            header('Location: articulos.php');
            exit;
        }
        break;
        
    case 'update':
        // Procesamos la actualización del artículo
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtenemos los datos del formulario
            $articulo->id = $_POST['id'];
            $articulo->codigo = $_POST['codigo'];
            $articulo->nombre = $_POST['nombre'];
            $articulo->descripcion = $_POST['descripcion'];
            $articulo->precio = $_POST['precio'];
            $articulo->stock = $_POST['stock'];
            $articulo->categoria_id = $_POST['categoria_id'];
            
            // Comprobamos si ya existe la imagen
            $articulo->getSingle();
            $existing_image = $articulo->imagen;
            
            // Procesamos la imagen si existe una nueva
            if(isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
                $target_dir = "../assets/img/";
                $filename = basename($_FILES["imagen"]["name"]);
                $target_file = $target_dir . $filename;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                
                // Verificamos que sea una imagen real
                $check = getimagesize($_FILES["imagen"]["tmp_name"]);
                if($check !== false) {
                    // Verificamos tamaño y formato
                    if ($_FILES["imagen"]["size"] > 500000) {
                        $_SESSION['mensaje'] = "El archivo es demasiado grande.";
                    } elseif($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                        $_SESSION['mensaje'] = "Solo se permiten archivos JPG, JPEG, PNG & GIF.";
                    } else {
                        // Si todo está bien, subimos la imagen
                        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
                            $articulo->imagen = $filename;
                            
                            // Borramos la imagen anterior si existe
                            if(!empty($existing_image) && file_exists($target_dir . $existing_image)) {
                                unlink($target_dir . $existing_image);
                            }
                        } else {
                            $_SESSION['mensaje'] = "Hubo un error al subir el archivo.";
                        }
                    }
                } else {
                    $_SESSION['mensaje'] = "El archivo no es una imagen.";
                }
            } else {
                // Mantenemos la imagen actual
                $articulo->imagen = $existing_image;
            }
            
            // Actualizamos el artículo
            if($articulo->update()) {
                $_SESSION['mensaje'] = "Artículo actualizado correctamente";
                header('Location: articulos.php');
                exit;
            } else {
                $_SESSION['mensaje'] = "Error al actualizar el artículo";
                include '../views/articulos/editar.php';
            }
        }
        break;
        
    case 'delete':
        // Verificamos que exista el ID
        if(isset($_GET['id'])) {
            $articulo->id = $_GET['id'];
            
            // Obtenemos la información del artículo para eliminar la imagen
            if($articulo->getSingle()) {
                $target_dir = "../assets/img/";
                $existing_image = $articulo->imagen;
                
                // Eliminamos el artículo
                if($articulo->delete()) {
                    // Borramos la imagen si existe
                    if(!empty($existing_image) && file_exists($target_dir . $existing_image)) {
                        unlink($target_dir . $existing_image);
                    }
                    
                    $_SESSION['mensaje'] = "Artículo eliminado correctamente";
                } else {
                    $_SESSION['mensaje'] = "Error al eliminar el artículo";
                }
            } else {
                $_SESSION['mensaje'] = "Artículo no encontrado";
            }
        } else {
            $_SESSION['mensaje'] = "ID no especificado";
        }
        
        header('Location: articulos.php');
        exit;
        break;
        
    case 'search':
        // Buscamos artículos
        if(isset($_GET['keyword'])) {
            $keyword = $_GET['keyword'];
            $result = $articulo->search($keyword);
            $articulos = $result->get_result();
            
            include '../views/articulos/index.php';
        } else {
            header('Location: articulos.php');
            exit;
        }
        break;
        
    default:
        header('Location: articulos.php');
        exit;
        break;
}
?>