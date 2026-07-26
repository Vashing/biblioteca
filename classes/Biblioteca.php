<?php

require_once 'Database.php';
require_once 'Libro.php';
require_once 'Usuario.php';
require_once 'Prestamo.php';

class Biblioteca {

    private $db;
    private $conn;

    public function __construct() {

        // Crear instancia de Database
        $this->db = new Database();

        // Obtener conexión
        $this->conn = $this->db->getConnection();
    }


    // =====================================================
    // GESTIÓN DE LIBROS
    // =====================================================

    // Agregar un nuevo libro
    public function agregarLibro(Libro $libro) {

        $sql = "INSERT INTO libros
                (titulo, autor, isbn, cantidad)
                VALUES
                (:titulo, :autor, :isbn, :cantidad)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(
            ':titulo',
            $libro->getTitulo()
        );

        $stmt->bindValue(
            ':autor',
            $libro->getAutor()
        );

        $stmt->bindValue(
            ':isbn',
            $libro->getIsbn()
        );

        $stmt->bindValue(
            ':cantidad',
            $libro->getCantidad(),
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }


    // Editar un libro existente
    public function editarLibro($id, $nuevosDatos) {

        $sql = "UPDATE libros
                SET titulo = :titulo,
                    autor = :autor,
                    isbn = :isbn,
                    cantidad = :cantidad
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':titulo',
            $nuevosDatos['titulo']
        );

        $stmt->bindValue(
            ':autor',
            $nuevosDatos['autor']
        );

        $stmt->bindValue(
            ':isbn',
            $nuevosDatos['isbn']
        );

        $stmt->bindValue(
            ':cantidad',
            $nuevosDatos['cantidad'],
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }


    // Eliminar un libro
    public function eliminarLibro($id) {

        $sql = "DELETE FROM libros
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }


    // Obtener todos los libros
    public function obtenerLibros() {

        $sql = "SELECT *
                FROM libros
                ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Buscar un libro específico
    public function buscarLibro($id) {

        $sql = "SELECT *
                FROM libros
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    // =====================================================
    // GESTIÓN DE USUARIOS
    // =====================================================

    // Agregar usuario
    public function agregarUsuario(Usuario $usuario) {

        $sql = "INSERT INTO usuarios
                (nombre, email, telefono)
                VALUES
                (:nombre, :email, :telefono)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(
            ':nombre',
            $usuario->getNombre()
        );

        $stmt->bindValue(
            ':email',
            $usuario->getEmail()
        );

        $stmt->bindValue(
            ':telefono',
            $usuario->getTelefono()
        );

        return $stmt->execute();
    }


    // Editar usuario
    public function editarUsuario($id, $nuevosDatos) {

        $sql = "UPDATE usuarios
                SET nombre = :nombre,
                    email = :email,
                    telefono = :telefono
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        $stmt->bindValue(
            ':nombre',
            $nuevosDatos['nombre']
        );

        $stmt->bindValue(
            ':email',
            $nuevosDatos['email']
        );

        $stmt->bindValue(
            ':telefono',
            $nuevosDatos['telefono']
        );

        return $stmt->execute();
    }


    // Eliminar usuario
    public function eliminarUsuario($id) {

        $sql = "DELETE FROM usuarios
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(
            ':id',
            $id,
            PDO::PARAM_INT
        );

        return $stmt->execute();
    }


    // Obtener todos los usuarios
    public function obtenerUsuarios() {

        $sql = "SELECT *
                FROM usuarios
                ORDER BY id DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // =====================================================
    // GESTIÓN DE PRÉSTAMOS
    // =====================================================

    // Prestar un libro
    public function prestarLibro($libro_id, $usuario_id) {

        try {

            // Iniciar transacción
            $this->conn->beginTransaction();


            // Verificar disponibilidad del libro
            $sql = "SELECT cantidad
                    FROM libros
                    WHERE id = :libro_id
                    FOR UPDATE";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ':libro_id',
                $libro_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $libro = $stmt->fetch(PDO::FETCH_ASSOC);


            // Verificar que el libro exista
            if (!$libro) {

                throw new Exception(
                    "El libro no existe."
                );
            }


            // Verificar disponibilidad
            if ($libro['cantidad'] <= 0) {

                throw new Exception(
                    "No hay ejemplares disponibles."
                );
            }


            // Crear el préstamo
            $sql = "INSERT INTO prestamos
                    (libro_id, usuario_id, fecha_prestamo, estado)
                    VALUES
                    (:libro_id, :usuario_id, CURDATE(), 'activo')";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ':libro_id',
                $libro_id,
                PDO::PARAM_INT
            );

            $stmt->bindValue(
                ':usuario_id',
                $usuario_id,
                PDO::PARAM_INT
            );

            $stmt->execute();


            // Disminuir cantidad del libro
            $sql = "UPDATE libros
                    SET cantidad = cantidad - 1
                    WHERE id = :libro_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ':libro_id',
                $libro_id,
                PDO::PARAM_INT
            );

            $stmt->execute();


            // Confirmar transacción
            $this->conn->commit();

            return true;

        } catch (Exception $e) {

            // Cancelar cambios si ocurrió un error
            if ($this->conn->inTransaction()) {

                $this->conn->rollBack();
            }

            return false;
        }
    }


    // Devolver un libro
    public function devolverLibro($prestamo_id) {

        try {

            // Iniciar transacción
            $this->conn->beginTransaction();


            // Buscar el préstamo
            $sql = "SELECT libro_id
                    FROM prestamos
                    WHERE id = :prestamo_id
                    AND estado = 'activo'
                    FOR UPDATE";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ':prestamo_id',
                $prestamo_id,
                PDO::PARAM_INT
            );

            $stmt->execute();

            $prestamo = $stmt->fetch(PDO::FETCH_ASSOC);


            // Verificar que exista
            if (!$prestamo) {

                throw new Exception(
                    "El préstamo no existe o ya fue devuelto."
                );
            }


            // Actualizar préstamo
            $sql = "UPDATE prestamos
                    SET fecha_devolucion = CURDATE(),
                        estado = 'devuelto'
                    WHERE id = :prestamo_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ':prestamo_id',
                $prestamo_id,
                PDO::PARAM_INT
            );

            $stmt->execute();


            // Aumentar cantidad disponible
            $sql = "UPDATE libros
                    SET cantidad = cantidad + 1
                    WHERE id = :libro_id";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindValue(
                ':libro_id',
                $prestamo['libro_id'],
                PDO::PARAM_INT
            );

            $stmt->execute();


            // Confirmar transacción
            $this->conn->commit();

            return true;

        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {

                $this->conn->rollBack();
            }

            return false;
        }
    }


    // Obtener préstamos activos
    public function obtenerPrestamosActivos() {

        $sql = "SELECT
                    prestamos.id,
                    libros.titulo AS libro,
                    usuarios.nombre AS usuario,
                    prestamos.fecha_prestamo,
                    prestamos.estado
                FROM prestamos

                INNER JOIN libros
                    ON prestamos.libro_id = libros.id

                INNER JOIN usuarios
                    ON prestamos.usuario_id = usuarios.id

                WHERE prestamos.estado = 'activo'

                ORDER BY prestamos.id DESC";

        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>