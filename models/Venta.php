<?php
class Venta {
    private $conn;
    private $table = 'ventas';

    // Propiedades de la venta
    public $id;
    public $cliente;
    public $total;
    public $fecha;
    public $detalle = [];

    // Constructor
    public function __construct($db) {
        $this->conn = $db;
    }

    // Obtener todas las ventas
    public function getAll() {
        $query = 'SELECT * FROM ' . $this->table . ' ORDER BY fecha DESC';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener una sola venta con su detalle
    public function getSingle() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ? LIMIT 1';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $this->id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            $this->cliente = $row['cliente'];
            $this->total = $row['total'];
            $this->fecha = $row['fecha'];
            
            // Obtener detalle de la venta
            $detalle_query = 'SELECT d.*, a.nombre as articulo_nombre 
                           FROM detalle_ventas d
                           LEFT JOIN articulos a ON d.articulo_id = a.id
                           WHERE d.venta_id = ?';
                           
            $detalle_stmt = $this->conn->prepare($detalle_query);
            $detalle_stmt->bind_param('i', $this->id);
            $detalle_stmt->execute();
            $detalle_result = $detalle_stmt->get_result();
            
            while($detalle_row = $detalle_result->fetch_assoc()) {
                $this->detalle[] = $detalle_row;
            }
            
            return true;
        }
        
        return false;
    }

    // Crear venta y su detalle
    public function create() {
        // Iniciamos una transacción
        $this->conn->begin_transaction();
        
        try {
            // Insertar la venta
            $query = 'INSERT INTO ' . $this->table . ' (cliente, total, fecha) VALUES (?, ?, NOW())';
                    
            $stmt = $this->conn->prepare($query);
            
            // Limpiar datos
            $this->cliente = htmlspecialchars(strip_tags($this->cliente));
            $this->total = htmlspecialchars(strip_tags($this->total));
            
            $stmt->bind_param('sd', 
                $this->cliente, 
                $this->total
            );
            
            $stmt->execute();
            
            // Obtener el ID de la venta insertada
            $this->id = $this->conn->insert_id;
            
            // Insertar el detalle de la venta
            $detalle_query = 'INSERT INTO detalle_ventas 
                           (venta_id, articulo_id, cantidad, precio_unitario, subtotal) 
                           VALUES (?, ?, ?, ?, ?)';
                           
            $detalle_stmt = $this->conn->prepare($detalle_query);
            
            // Insertar cada ítem del detalle
            foreach($this->detalle as $item) {
                $detalle_stmt->bind_param('iiddd', 
                    $this->id, 
                    $item['articulo_id'], 
                    $item['cantidad'], 
                    $item['precio_unitario'], 
                    $item['subtotal']
                );
                
                $detalle_stmt->execute();
                
                // Actualizar el stock del artículo
                $articulo = new Articulo($this->conn);
                $articulo->id = $item['articulo_id'];
                $articulo->updateStock($item['cantidad']);
            }
            
            // Confirmar la transacción
            $this->conn->commit();
            
            return true;
        } catch (Exception $e) {
            // Si ocurre un error, revertimos la transacción
            $this->conn->rollback();
            
            return false;
        }
    }

    // Anular venta
    public function anular() {
        // Iniciamos una transacción
        $this->conn->begin_transaction();
        
        try {
            // Obtener el detalle de la venta
            $this->getSingle();
            
            // Restaurar el stock de los artículos
            foreach($this->detalle as $item) {
                $articulo_query = 'UPDATE articulos SET stock = stock + ? WHERE id = ?';
                $articulo_stmt = $this->conn->prepare($articulo_query);
                $articulo_stmt->bind_param('ii', 
                    $item['cantidad'], 
                    $item['articulo_id']
                );
                $articulo_stmt->execute();
            }
            
            // Marcar la venta como anulada (en este caso, la eliminamos)
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = ?';
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('i', $this->id);
            $stmt->execute();
            
            // Eliminar el detalle de la venta
            $detalle_query = 'DELETE FROM detalle_ventas WHERE venta_id = ?';
            $detalle_stmt = $this->conn->prepare($detalle_query);
            $detalle_stmt->bind_param('i', $this->id);
            $detalle_stmt->execute();
            
            // Confirmar la transacción
            $this->conn->commit();
            
            return true;
        } catch (Exception $e) {
            // Si ocurre un error, revertimos la transacción
            $this->conn->rollback();
            
            return false;
        }
    }

    // Obtener ventas por rango de fechas
    public function getByDateRange($desde, $hasta) {
        $query = 'SELECT * FROM ' . $this->table . ' 
                WHERE DATE(fecha) BETWEEN ? AND ?
                ORDER BY fecha DESC';
                
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $desde, $hasta);
        $stmt->execute();
        
        return $stmt;
    }

    // Obtener estadísticas de ventas
    public function getStats() {
        $stats = [];
        
        // Ventas totales
        $total_query = 'SELECT COUNT(*) as total_ventas, SUM(total) as total_monto FROM ' . $this->table;
        $total_result = $this->conn->query($total_query);
        $stats['totales'] = $total_result->fetch_assoc();
        
        // Ventas de hoy
        $hoy_query = 'SELECT COUNT(*) as total_ventas, SUM(total) as total_monto FROM ' . $this->table . ' 
                    WHERE DATE(fecha) = CURDATE()';
        $hoy_result = $this->conn->query($hoy_query);
        $stats['hoy'] = $hoy_result->fetch_assoc();
        
        // Ventas de la semana
        $semana_query = 'SELECT COUNT(*) as total_ventas, SUM(total) as total_monto FROM ' . $this->table . ' 
                        WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
        $semana_result = $this->conn->query($semana_query);
        $stats['semana'] = $semana_result->fetch_assoc();
        
        // Ventas del mes
        $mes_query = 'SELECT COUNT(*) as total_ventas, SUM(total) as total_monto FROM ' . $this->table . ' 
                    WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())';
        $mes_result = $this->conn->query($mes_query);
        $stats['mes'] = $mes_result->fetch_assoc();
        
        return $stats;
    }
}
?>