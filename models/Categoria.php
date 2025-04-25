<?php
class Categoria {
    private $conn;
    private $table = 'categorias';

    // Propiedades de la categoría
    public $id;
    public $nombre;
    public $descripcion;

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las categorías
    public function getAll() {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY nombre ASC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener una sola categoría
    public function getSingle() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            $this->nombre = $row['nombre'];
            $this->descripcion = $row['descripcion'];
            
            return true;
        }
        
        return false;
    }

    // Crear categoría
    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' (nombre, descripcion) VALUES (?, ?)';
                
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        
        $stmt->bind_param('ss', 
            $this->nombre, 
            $this->descripcion
        );
        
        if($stmt->execute()) {
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Actualizar categoría
    public function update() {
        $query = 'UPDATE ' . $this->table . ' SET nombre = ?, descripcion = ? WHERE id = ?';
                
        $stmt = $this->conn->prepare($query);
        
        // Limpiar datos
        $this->nombre = htmlspecialchars(strip_tags($this->nombre));
        $this->descripcion = htmlspecialchars(strip_tags($this->descripcion));
        $this->id = htmlspecialchars(strip_tags($this->id));
        
        $stmt->bind_param('ssi', 
            $this->nombre, 
            $this->descripcion,
            $this->id
        );
        
        if($stmt->execute()) {
            return true;
        }
        
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    // Eliminar categoría
    public function delete() {
        // Primero verificamos si hay artículos que usan esta categoría
        $check_query = 'SELECT COUNT(*) as count FROM articulos WHERE categoria_id = ?';
        $check_stmt = $this->conn->prepare($check_query);
        $check_stmt->bind_param('i', $this->id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_assoc();
        
        if($row['count'] > 0) {
            // No se puede eliminar, hay artículos que usan esta categoría
            return false;
        }
        
        // Si no hay artículos asociados, procedemos a eliminar
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

    // Buscar categorías
    public function search($keyword) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                WHERE nombre LIKE ? OR descripcion LIKE ?
                ORDER BY nombre ASC';
                
        $keyword = '%' . $keyword . '%';
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $keyword, $keyword);
        $stmt->execute();
        
        return $stmt;
    }
}
?>