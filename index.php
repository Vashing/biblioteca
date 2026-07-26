<?php

require_once "classes/Biblioteca.php";

$biblioteca = new Biblioteca();

$action = $_GET['action'] ?? 'libros';

$mensaje = "";
$error = "";


// ======================================================
// PROCESAR ACCIONES
// ======================================================

try {

    // ==================================================
    // AGREGAR LIBRO
    // ==================================================

    if ($action == 'guardar_libro' && $_SERVER['REQUEST_METHOD'] == 'POST') {

        $titulo = $_POST['titulo'] ?? '';
        $autor = $_POST['autor'] ?? '';
        $isbn = $_POST['isbn'] ?? '';
        $cantidad = $_POST['cantidad'] ?? 1;

        $libro = new Libro(
            $titulo,
            $autor,
            $isbn,
            $cantidad
        );

        if ($biblioteca->agregarLibro($libro)) {

            header("Location: index.php?action=libros&mensaje=libro_agregado");
            exit;

        } else {

            $error = "No se pudo agregar el libro.";

        }
    }


    // ==================================================
    // ELIMINAR LIBRO
    // ==================================================

    if ($action == 'eliminar_libro') {

        $id = $_GET['id'] ?? null;

        if ($id) {

            if ($biblioteca->eliminarLibro($id)) {

                header("Location: index.php?action=libros&mensaje=libro_eliminado");
                exit;

            } else {

                $error = "No se pudo eliminar el libro.";

            }
        }
    }


    // ==================================================
    // EDITAR LIBRO
    // ==================================================

    if ($action == 'actualizar_libro' && $_SERVER['REQUEST_METHOD'] == 'POST') {

        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $autor = $_POST['autor'];
        $isbn = $_POST['isbn'];
        $cantidad = $_POST['cantidad'];

        $datos = [

            'titulo' => $titulo,
            'autor' => $autor,
            'isbn' => $isbn,
            'cantidad' => $cantidad

        ];

        if ($biblioteca->editarLibro($id, $datos)) {

            header("Location: index.php?action=libros&mensaje=libro_actualizado");
            exit;

        } else {

            $error = "No se pudo actualizar el libro.";

        }
    }


    // ==================================================
    // AGREGAR USUARIO
    // ==================================================

    if ($action == 'guardar_usuario' && $_SERVER['REQUEST_METHOD'] == 'POST') {

        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];

        $usuario = new Usuario(
            $nombre,
            $email,
            $telefono
        );

        if ($biblioteca->agregarUsuario($usuario)) {

            header("Location: index.php?action=usuarios&mensaje=usuario_agregado");
            exit;

        } else {

            $error = "No se pudo agregar el usuario.";

        }
    }


    // ==================================================
    // ELIMINAR USUARIO
    // ==================================================

    if ($action == 'eliminar_usuario') {

        $id = $_GET['id'] ?? null;

        if ($id) {

            if ($biblioteca->eliminarUsuario($id)) {

                header("Location: index.php?action=usuarios&mensaje=usuario_eliminado");
                exit;

            } else {

                $error = "No se pudo eliminar el usuario.";

            }
        }
    }


    // ==================================================
    // EDITAR USUARIO
    // ==================================================

    if ($action == 'actualizar_usuario' && $_SERVER['REQUEST_METHOD'] == 'POST') {

        $id = $_POST['id'];
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $telefono = $_POST['telefono'];

        $datos = [

            'nombre' => $nombre,
            'email' => $email,
            'telefono' => $telefono

        ];

        if ($biblioteca->editarUsuario($id, $datos)) {

            header("Location: index.php?action=usuarios&mensaje=usuario_actualizado");
            exit;

        } else {

            $error = "No se pudo actualizar el usuario.";

        }
    }


    // ==================================================
    // PRESTAR LIBRO
    // ==================================================

    if ($action == 'prestar_libro' && $_SERVER['REQUEST_METHOD'] == 'POST') {

        $libro_id = $_POST['libro_id'];
        $usuario_id = $_POST['usuario_id'];

        if ($biblioteca->prestarLibro($libro_id, $usuario_id)) {

            header("Location: index.php?action=prestamos&mensaje=prestamo_realizado");
            exit;

        } else {

            $error = "No se pudo realizar el préstamo. Verifique que el libro tenga ejemplares disponibles.";

        }
    }


    // ==================================================
    // DEVOLVER LIBRO
    // ==================================================

    if ($action == 'devolver_libro') {

        $prestamo_id = $_GET['id'] ?? null;

        if ($prestamo_id) {

            if ($biblioteca->devolverLibro($prestamo_id)) {

                header("Location: index.php?action=prestamos&mensaje=libro_devuelto");
                exit;

            } else {

                $error = "No se pudo realizar la devolución.";

            }
        }
    }


} catch (Exception $e) {

    $error = $e->getMessage();

}


// ======================================================
// MENSAJES
// ======================================================

if (isset($_GET['mensaje'])) {

    switch ($_GET['mensaje']) {

        case 'libro_agregado':
            $mensaje = "Libro agregado correctamente.";
            break;

        case 'libro_actualizado':
            $mensaje = "Libro actualizado correctamente.";
            break;

        case 'libro_eliminado':
            $mensaje = "Libro eliminado correctamente.";
            break;

        case 'usuario_agregado':
            $mensaje = "Usuario agregado correctamente.";
            break;

        case 'usuario_actualizado':
            $mensaje = "Usuario actualizado correctamente.";
            break;

        case 'usuario_eliminado':
            $mensaje = "Usuario eliminado correctamente.";
            break;

        case 'prestamo_realizado':
            $mensaje = "Préstamo realizado correctamente.";
            break;

        case 'libro_devuelto':
            $mensaje = "Libro devuelto correctamente.";
            break;

    }

}

?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Sistema de Gestión de Biblioteca</title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {

            font-family: Arial, sans-serif;

            margin: 0;

            background-color: #f4f6f8;

        }


        .container {

            max-width: 1100px;

            margin: 40px auto;

            background-color: white;

            padding: 30px;

            border-radius: 10px;

            box-shadow:
                0 0 10px rgba(0,0,0,0.1);

        }


        h1 {

            color: #333;

            margin-top: 0;

        }


        h2 {

            color: #444;

            margin-top: 30px;

        }


        nav {

            margin-bottom: 30px;

            background-color: #333;

            padding: 15px;

            border-radius: 5px;

        }


        nav a {

            margin-right: 20px;

            text-decoration: none;

            color: white;

            font-weight: bold;

        }


        nav a:hover {

            color: #ddd;

        }


        table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 20px;

        }


        th,
        td {

            padding: 12px;

            border: 1px solid #ddd;

            text-align: left;

        }


        th {

            background-color: #333;

            color: white;

        }


        .btn {

            display: inline-block;

            padding: 8px 12px;

            text-decoration: none;

            border-radius: 4px;

            color: white;

            background-color: #007bff;

            border: none;

            cursor: pointer;

            font-size: 14px;

        }


        .btn:hover {

            background-color: #0056b3;

        }


        .btn-danger {

            background-color: #dc3545;

        }


        .btn-danger:hover {

            background-color: #a71d2a;

        }


        .btn-success {

            background-color: #28a745;

        }


        .btn-success:hover {

            background-color: #1e7e34;

        }


        form {

            margin-top: 20px;

            max-width: 600px;

        }


        label {

            display: block;

            margin-top: 15px;

            margin-bottom: 5px;

            font-weight: bold;

        }


        input,
        select {

            width: 100%;

            padding: 10px;

            border: 1px solid #ccc;

            border-radius: 5px;

        }


        form button {

            margin-top: 20px;

        }


        .mensaje {

            padding: 12px;

            margin-bottom: 20px;

            background-color: #d4edda;

            color: #155724;

            border-radius: 5px;

        }


        .error {

            padding: 12px;

            margin-bottom: 20px;

            background-color: #f8d7da;

            color: #721c24;

            border-radius: 5px;

        }


        .acciones {

            display: flex;

            gap: 5px;

            flex-wrap: wrap;

        }


        .seccion {

            margin-bottom: 30px;

        }

    </style>

</head>


<body>


<div class="container">


    <h1>Sistema de Gestión de Biblioteca</h1>


    <!-- ================================================= -->
    <!-- NAVEGACIÓN -->
    <!-- ================================================= -->


    <nav>

        <a href="index.php?action=libros">

            📚 Libros

        </a>


        <a href="index.php?action=usuarios">

            👥 Usuarios

        </a>


        <a href="index.php?action=prestamos">

            📖 Préstamos

        </a>

    </nav>


    <!-- ================================================= -->
    <!-- MENSAJES -->
    <!-- ================================================= -->


    <?php if ($mensaje): ?>

        <div class="mensaje">

            <?php echo htmlspecialchars($mensaje); ?>

        </div>

    <?php endif; ?>


    <?php if ($error): ?>

        <div class="error">

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>


    <!-- ================================================= -->
    <!-- LIBROS -->
    <!-- ================================================= -->


    <?php if ($action == 'libros'): ?>


        <div class="seccion">


            <h2>📚 Gestión de Libros</h2>


            <a
                class="btn btn-success"
                href="index.php?action=nuevo_libro"
            >

                + Agregar nuevo libro

            </a>


            <?php

            $libros = $biblioteca->obtenerLibros();

            ?>


            <table>


                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Título</th>

                        <th>Autor</th>

                        <th>ISBN</th>

                        <th>Cantidad</th>

                        <th>Acciones</th>

                    </tr>

                </thead>


                <tbody>


                <?php if (count($libros) > 0): ?>


                    <?php foreach ($libros as $libro): ?>


                        <tr>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $libro['id']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $libro['titulo']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $libro['autor']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $libro['isbn']
                                );
                                ?>

                            </td>


                            <td>

                                <?php
                                echo htmlspecialchars(
                                    $libro['cantidad']
                                );
                                ?>

                            </td>


                            <td>


                                <div class="acciones">


                                    <a
                                        class="btn"
                                        href="index.php?action=editar_libro&id=<?php echo $libro['id']; ?>"
                                    >

                                        Editar

                                    </a>


                                    <a
                                        class="btn btn-danger"
                                        href="index.php?action=eliminar_libro&id=<?php echo $libro['id']; ?>"
                                        onclick="return confirm('¿Está seguro de eliminar este libro?');"
                                    >

                                        Eliminar

                                    </a>


                                </div>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php else: ?>


                    <tr>

                        <td colspan="6">

                            No hay libros registrados.

                        </td>

                    </tr>


                <?php endif; ?>


                </tbody>


            </table>


        </div>


    <!-- ================================================= -->
    <!-- NUEVO LIBRO -->
    <!-- ================================================= -->


    <?php elseif ($action == 'nuevo_libro'): ?>


        <h2>➕ Agregar nuevo libro</h2>


        <form
            method="POST"
            action="index.php?action=guardar_libro"
        >


            <label>

                Título:

            </label>


            <input
                type="text"
                name="titulo"
                required
            >


            <label>

                Autor:

            </label>


            <input
                type="text"
                name="autor"
                required
            >


            <label>

                ISBN:

            </label>


            <input
                type="text"
                name="isbn"
            >


            <label>

                Cantidad:

            </label>


            <input
                type="number"
                name="cantidad"
                min="1"
                value="1"
                required
            >


            <button
                class="btn btn-success"
                type="submit"
            >

                Guardar libro

            </button>


            <a
                class="btn"
                href="index.php?action=libros"
            >

                Cancelar

            </a>


        </form>


    <!-- ================================================= -->
    <!-- EDITAR LIBRO -->
    <!-- ================================================= -->


    <?php elseif ($action == 'editar_libro'): ?>


        <?php

        $id = $_GET['id'] ?? null;

        $libro = $biblioteca->buscarLibro($id);

        ?>


        <h2>✏️ Editar libro</h2>


        <?php if ($libro): ?>


            <form
                method="POST"
                action="index.php?action=actualizar_libro"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $libro['id']; ?>"
                >


                <label>

                    Título:

                </label>


                <input
                    type="text"
                    name="titulo"
                    value="<?php echo htmlspecialchars($libro['titulo']); ?>"
                    required
                >


                <label>

                    Autor:

                </label>


                <input
                    type="text"
                    name="autor"
                    value="<?php echo htmlspecialchars($libro['autor']); ?>"
                    required
                >


                <label>

                    ISBN:

                </label>


                <input
                    type="text"
                    name="isbn"
                    value="<?php echo htmlspecialchars($libro['isbn']); ?>"
                >


                <label>

                    Cantidad:

                </label>


                <input
                    type="number"
                    name="cantidad"
                    min="0"
                    value="<?php echo $libro['cantidad']; ?>"
                    required
                >


                <button
                    class="btn"
                    type="submit"
                >

                    Actualizar libro

                </button>


                <a
                    class="btn"
                    href="index.php?action=libros"
                >

                    Cancelar

                </a>


            </form>


        <?php else: ?>


            <div class="error">

                El libro no existe.

            </div>


        <?php endif; ?>


    <!-- ================================================= -->
    <!-- USUARIOS -->
    <!-- ================================================= -->


    <?php elseif ($action == 'usuarios'): ?>


        <h2>👥 Gestión de Usuarios</h2>


        <a
            class="btn btn-success"
            href="index.php?action=nuevo_usuario"
        >

            + Agregar usuario

        </a>


        <?php

        $usuarios = $biblioteca->obtenerUsuarios();

        ?>


        <table>


            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nombre</th>

                    <th>Email</th>

                    <th>Teléfono</th>

                    <th>Acciones</th>

                </tr>

            </thead>


            <tbody>


            <?php if (count($usuarios) > 0): ?>


                <?php foreach ($usuarios as $usuario): ?>


                    <tr>


                        <td>

                            <?php echo $usuario['id']; ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $usuario['nombre']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $usuario['email']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $usuario['telefono']
                            );
                            ?>

                        </td>


                        <td>


                            <div class="acciones">


                                <a
                                    class="btn"
                                    href="index.php?action=editar_usuario&id=<?php echo $usuario['id']; ?>"
                                >

                                    Editar

                                </a>


                                <a
                                    class="btn btn-danger"
                                    href="index.php?action=eliminar_usuario&id=<?php echo $usuario['id']; ?>"
                                    onclick="return confirm('¿Está seguro de eliminar este usuario?');"
                                >

                                    Eliminar

                                </a>


                            </div>


                        </td>


                    </tr>


                <?php endforeach; ?>


            <?php else: ?>


                <tr>

                    <td colspan="5">

                        No hay usuarios registrados.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>


        </table>


    <!-- ================================================= -->
    <!-- NUEVO USUARIO -->
    <!-- ================================================= -->


    <?php elseif ($action == 'nuevo_usuario'): ?>


        <h2>➕ Agregar usuario</h2>


        <form
            method="POST"
            action="index.php?action=guardar_usuario"
        >


            <label>

                Nombre:

            </label>


            <input
                type="text"
                name="nombre"
                required
            >


            <label>

                Email:

            </label>


            <input
                type="email"
                name="email"
                required
            >


            <label>

                Teléfono:

            </label>


            <input
                type="text"
                name="telefono"
            >


            <button
                class="btn btn-success"
                type="submit"
            >

                Guardar usuario

            </button>


            <a
                class="btn"
                href="index.php?action=usuarios"
            >

                Cancelar

            </a>


        </form>


    <!-- ================================================= -->
    <!-- EDITAR USUARIO -->
    <!-- ================================================= -->


    <?php elseif ($action == 'editar_usuario'): ?>


        <?php

        $id = $_GET['id'] ?? null;

        // Buscar usuario directamente mediante la lista
        $usuarios = $biblioteca->obtenerUsuarios();

        $usuarioEncontrado = null;


        foreach ($usuarios as $usuario) {

            if ($usuario['id'] == $id) {

                $usuarioEncontrado = $usuario;

                break;

            }

        }

        ?>


        <h2>✏️ Editar usuario</h2>


        <?php if ($usuarioEncontrado): ?>


            <form
                method="POST"
                action="index.php?action=actualizar_usuario"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $usuarioEncontrado['id']; ?>"
                >


                <label>

                    Nombre:

                </label>


                <input
                    type="text"
                    name="nombre"
                    value="<?php echo htmlspecialchars($usuarioEncontrado['nombre']); ?>"
                    required
                >


                <label>

                    Email:

                </label>


                <input
                    type="email"
                    name="email"
                    value="<?php echo htmlspecialchars($usuarioEncontrado['email']); ?>"
                    required
                >


                <label>

                    Teléfono:

                </label>


                <input
                    type="text"
                    name="telefono"
                    value="<?php echo htmlspecialchars($usuarioEncontrado['telefono']); ?>"
                >


                <button
                    class="btn"
                    type="submit"
                >

                    Actualizar usuario

                </button>


                <a
                    class="btn"
                    href="index.php?action=usuarios"
                >

                    Cancelar

                </a>


            </form>


        <?php else: ?>


            <div class="error">

                El usuario no existe.

            </div>


        <?php endif; ?>


    <!-- ================================================= -->
    <!-- PRÉSTAMOS -->
    <!-- ================================================= -->


    <?php elseif ($action == 'prestamos'): ?>


        <h2>📖 Gestión de Préstamos</h2>


        <a
            class="btn btn-success"
            href="index.php?action=nuevo_prestamo"
        >

            + Registrar préstamo

        </a>


        <?php

        $prestamos =
            $biblioteca->obtenerPrestamosActivos();

        ?>


        <table>


            <thead>

                <tr>

                    <th>ID</th>

                    <th>Libro</th>

                    <th>Usuario</th>

                    <th>Fecha de préstamo</th>

                    <th>Estado</th>

                    <th>Acciones</th>

                </tr>

            </thead>


            <tbody>


            <?php if (count($prestamos) > 0): ?>


                <?php foreach ($prestamos as $prestamo): ?>


                    <tr>


                        <td>

                            <?php echo $prestamo['id']; ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $prestamo['libro']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $prestamo['usuario']
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo $prestamo['fecha_prestamo'];
                            ?>

                        </td>


                        <td>

                            <?php
                            echo $prestamo['estado'];
                            ?>

                        </td>


                        <td>


                            <a
                                class="btn btn-success"
                                href="index.php?action=devolver_libro&id=<?php echo $prestamo['id']; ?>"
                                onclick="return confirm('¿Confirmar devolución del libro?');"
                            >

                                Devolver

                            </a>


                        </td>


                    </tr>


                <?php endforeach; ?>


            <?php else: ?>


                <tr>

                    <td colspan="6">

                        No hay préstamos activos.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>


        </table>


    <!-- ================================================= -->
    <!-- NUEVO PRÉSTAMO -->
    <!-- ================================================= -->


    <?php elseif ($action == 'nuevo_prestamo'): ?>


        <h2>📖 Registrar préstamo</h2>


        <?php

        $libros = $biblioteca->obtenerLibros();

        $usuarios = $biblioteca->obtenerUsuarios();

        ?>


        <form
            method="POST"
            action="index.php?action=prestar_libro"
        >


            <label>

                Seleccionar libro:

            </label>


            <select
                name="libro_id"
                required
            >


                <option value="">

                    Seleccione un libro

                </option>


                <?php foreach ($libros as $libro): ?>


                    <?php if ($libro['cantidad'] > 0): ?>


                        <option
                            value="<?php echo $libro['id']; ?>"
                        >

                            <?php
                            echo htmlspecialchars(
                                $libro['titulo']
                            );

                            echo " - Disponibles: ";

                            echo $libro['cantidad'];
                            ?>

                        </option>


                    <?php endif; ?>


                <?php endforeach; ?>


            </select>


            <label>

                Seleccionar usuario:

            </label>


            <select
                name="usuario_id"
                required
            >


                <option value="">

                    Seleccione un usuario

                </option>


                <?php foreach ($usuarios as $usuario): ?>


                    <option
                        value="<?php echo $usuario['id']; ?>"
                    >

                        <?php
                        echo htmlspecialchars(
                            $usuario['nombre']
                        );
                        ?>

                    </option>


                <?php endforeach; ?>


            </select>


            <button
                class="btn btn-success"
                type="submit"
            >

                Registrar préstamo

            </button>


            <a
                class="btn"
                href="index.php?action=prestamos"
            >

                Cancelar

            </a>


        </form>


    <?php else: ?>


        <h2>Sección no encontrada</h2>


        <p>

            La sección que intenta visitar no existe.

        </p>


    <?php endif; ?>


</div>


</body>

</html>