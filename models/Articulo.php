<?php
class Articulo {
    private $conn;
    private $table = 'articulos';

    // Propiedades del artículo
    public $id;
    public $codigo;
    public $nombre;
    public $descripcion;
    public $precio;
    public $stock;
    public $imagen;
    public $categoria_id;
    public $fecha_creacion;
    public $categoria_nombre;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todos los artículos
    public function getAll() {
        $query = 'SELECT a.*, c.nombre as categoria_nombre 
                FROM ' . $this->table . ' a
                LEFT JOIN categorias c ON a.categoria_id = c.id
                ORDER BY a.fecha_creacion DESC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener un solo artículo
    public function getSingle() {
        $query = 'SELECT a.*, c.nombre as categoria_nombre 
                FROM ' . $this->table . ' a
                LEFT JOIN categorias c ON a.categoria_id = c.id
                WHERE a.id = ?
                LIMIT 1';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            $this->codigo = $row['codigo'];
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            $this->precio = $row['precio'];
            $this->stock = $row['stock'];
            $this->imagen = $row['imagen'];
            $this->categoria_id = $row['categoria_id'];
            $this->categoria_nombre = $row['categoria_nombre'];
            $this->fecha_creacion = $row['fecha_creacion'];
            
            return true;
        }
        
        return false;
    }

    // Crear artículo
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' 
                (codigo, nombre, descripcion, precio, stock, imagen, categoria_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?)';
                
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->codigo = htmlspecialchars(strip_tags($this->codigo));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->imagen = htmlspecialchars(strip_tags($this->imagen));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        
        $stmt->bind_param('sssdiis', 
            $this->codigo, 
            $this->nombre, 
            $this->descripcion, 
            $this->precio, 
            $this->stock, 
            $this->imagen, 
            $this->categoria_id
        );
        
        if($stmt->execute()) {
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Actualizar artículo
    public function update() {
        $query = 'UPDATE ' . $this->table . '
                SET codigo = ?, nombre = ?, descripcion = ?, precio = ?, 
                    stock = ?, imagen = ?, categoria_id = ?
                WHERE id = ?';
                
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->codigo = htmlspecialchars(strip_tags($this->codigo));
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->stock = htmlspecialchars(strip_tags($this->stock));
        $this->imagen = htmlspecialchars(strip_tags($this->imagen));
        $this->categoria_id = htmlspecialchars(strip_tags($this->categoria_id));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bind_param('sssdiisi', 
            $this->codigo, 
            $this->nombre, 
            $this->descripcion, 
            $this->precio, 
            $this->stock, 
            $this->imagen, 
            $this->categoria_id,
            $this->id
        );
        
        if($stmt->execute()) {
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Eliminar artículo
    public function delete() {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = ?';
        
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bind_param('i', $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Buscar artículos
    public function search($keyword) {
        $query = 'SELECT a.*, c.nombre as categoria_nombre 
                FROM ' . $this->table . ' a
                LEFT JOIN categorias c ON a.categoria_id = c.id
                WHERE a.nombre LIKE ? OR a.codigo LIKE ? OR a.descripcion LIKE ?
                ORDER BY a.fecha_creacion DESC';
                
        $keyword = '%' . $keyword . '%';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sss', $keyword, $keyword, $keyword);
        $stmt->execute();
        
        return $stmt;
    }

    // Actualizar stock
    public function updateStock($cantidad) {
        $query = 'UPDATE ' . $this->table . '
                SET stock = stock - ?
                WHERE id = ?';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $cantidad, $this->id);
        
        if($stmt->execute()) {
            return true;
        }
        
        return false;
    }
}
?>