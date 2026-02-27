<?php

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$port = getenv('DB_PORT');





try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("<h3>Error de conexión: " . htmlspecialchars($e->getMessage()) . "</h3>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');

    switch ($_POST['action']) {
        case 'insertar':
            if (empty($_POST['nombre_visitante']) || empty($_POST['motivo_visita'])) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                exit;
            }

            try {
                $sql = "INSERT INTO visitantes (nombre_visitante, motivo_visita, fecha_ingreso, salida_registrada) 
                        VALUES (:nombre, :motivo, NOW(), FALSE)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre' => $_POST['nombre_visitante'],
                    ':motivo' => $_POST['motivo_visita']
                ]);
                echo json_encode(['success' => true, 'message' => 'Visitante registrado correctamente']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;

        case 'cambiar_estado':
            if (!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            try {
                $sql = "UPDATE visitantes SET salida_registrada = NOT salida_registrada WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;

        case 'editar':
            if (!isset($_POST['id']) || empty($_POST['nombre_visitante']) || empty($_POST['motivo_visita'])) {
                echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
                exit;
            }

            try {
                $sql = "UPDATE visitantes SET nombre_visitante = :nombre, motivo_visita = :motivo WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nombre' => $_POST['nombre_visitante'],
                    ':motivo' => $_POST['motivo_visita'],
                    ':id' => $_POST['id']
                ]);
                echo json_encode(['success' => true, 'message' => 'Visitante actualizado correctamente']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;

        case 'eliminar':
            if (!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            try {
                $sql = "DELETE FROM visitantes WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Visitante eliminado correctamente']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;

        case 'obtener_visitante':
            if (!isset($_POST['id'])) {
                echo json_encode(['success' => false]);
                exit;
            }

            try {
                $sql = "SELECT * FROM visitantes WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $_POST['id']]);
                $visitante = $stmt->fetch();
                echo json_encode(['success' => true, 'data' => $visitante]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
    }
}


try {
    $sqlEstudiante = "SELECT * FROM estudiante LIMIT 1";
    $stmtEstudiante = $pdo->prepare($sqlEstudiante);
    $stmtEstudiante->execute();
    $estudiante = $stmtEstudiante->fetch();
} catch (PDOException $e) {
    $estudiante = null;
}

try {
    $sql = "SELECT * FROM visitantes ORDER BY fecha_ingreso DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $visitantes = $stmt->fetchAll();
} catch (PDOException $e) {
    $visitantes = [];
    $errorConsulta = $e->getMessage();
}
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Visitantes al Campus</title>

    <link href="styles.css" rel="stylesheet">

</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Control de Visitantes al Campus</h1>
            <p>Sistema de Registro y Gestión de Seguridad</p>
        </div>

        <div class="landing-section">
            <?php if ($estudiante): ?>
                <div class="estudiante-info">
                    <?php if (!empty($estudiante['foto'])): ?>
                        <img src="<?php echo htmlspecialchars($estudiante['foto']); ?>"
                            alt="Foto del estudiante" class="foto-estudiante">
                    <?php else: ?>
                        <div class="foto-estudiante" style="background: #667eea; display: flex; align-items: center; justify-content: center; color: white; font-size: 3em;">
                            👤
                        </div>
                    <?php endif; ?>
                    <div class="info-text">
                        <h2><?php echo htmlspecialchars($estudiante['nombre'] ?? 'Estudiante'); ?></h2>
                        <p><?php echo htmlspecialchars($estudiante['bio'] ?? 'Estudiante de desarrollo web con experiencia en PHP, PostgreSQL y Docker.'); ?></p>
                        <?php if (!empty($estudiante['habilidades'])): ?>
                            <div class="habilidades">
                                <h3>Habilidades:</h3>
                                <?php
                                $habilidades = is_string($estudiante['habilidades'])
                                    ? explode(',', $estudiante['habilidades'])
                                    : (is_array($estudiante['habilidades']) ? $estudiante['habilidades'] : []);
                                foreach ($habilidades as $habilidad):
                                ?>
                                    <span class="tag"><?php echo htmlspecialchars(trim($habilidad)); ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="estudiante-info">
                    <div class="foto-estudiante" style="background: #667eea; display: flex; align-items: center; justify-content: center; color: white; font-size: 3em;">
                        👤
                    </div>
                    <div class="info-text">
                        <h2>Estudiante:</h2>
                        <p>Información del estudiante no disponible. Configurar la tabla 'estudiante' en la base de datos.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-section">
            <h2>Registrar Nuevo Visitante</h2>
            <div id="alert-container"></div>
            <form id="form-visitante">
                <div class="form-group">
                    <label for="nombre_visitante">Nombre del Visitante *</label>
                    <input type="text" id="nombre_visitante" name="nombre_visitante" required
                        placeholder="Ingrese el nombre completo">
                </div>
                <div class="form-group">
                    <label for="motivo_visita">Motivo de la Visita *</label>
                    <textarea id="motivo_visita" name="motivo_visita" required
                        placeholder="Describa el motivo de la visita"></textarea>
                </div>
                <button type="submit" class="btn btn-primary"> Registrar Visitante</button>
            </form>
        </div>

        <!-- Tabla de Visitantes -->
        <div class="table-section">
            <h2>Registro de Visitantes</h2>
            <?php if (isset($errorConsulta)): ?>
                <div class="alert alert-error">
                    Error al consultar: <?php echo htmlspecialchars($errorConsulta); ?>
                </div>
            <?php elseif (empty($visitantes)): ?>
                <div class="alert alert-error">
                    No hay visitantes registrados aún.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Motivo de Visita</th>
                            <th>Fecha de Ingreso</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($visitantes as $visitante): ?>
                            <tr id="row-<?php echo $visitante['id']; ?>">
                                <td><?php echo htmlspecialchars($visitante['id']); ?></td>
                                <td><?php echo htmlspecialchars($visitante['nombre_visitante']); ?></td>
                                <td><?php echo htmlspecialchars($visitante['motivo_visita']); ?></td>
                                <td><?php echo htmlspecialchars($visitante['fecha_ingreso']); ?></td>
                                <td>
                                    <span class="badge <?php echo $visitante['salida_registrada'] ? 'badge-success' : 'badge-danger'; ?>">
                                        <?php echo $visitante['salida_registrada'] ? 'Salida Registrada' : 'En Campus'; ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button onclick="cambiarEstado(<?php echo $visitante['id']; ?>)"
                                        class="btn btn-info btn-sm">
                                        <?php echo $visitante['salida_registrada'] ? 'Marcar Entrada' : 'Registrar Salida'; ?>
                                    </button>
                                    <button onclick="editarVisitante(<?php echo $visitante['id']; ?>)"
                                        class="btn btn-warning btn-sm">Editar</button>
                                    <button onclick="eliminarVisitante(<?php echo $visitante['id']; ?>)"
                                        class="btn btn-danger btn-sm">Eliminar</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para Editar -->
    <div id="modal-editar" class="modal">
        <div class="modal-content">
            <span class="close" onclick="cerrarModal()">&times;</span>
            <h2>Editar Visitante</h2>
            <form id="form-editar">
                <input type="hidden" id="edit-id" name="id">
                <div class="form-group">
                    <label for="edit-nombre">Nombre del Visitante *</label>
                    <input type="text" id="edit-nombre" name="nombre_visitante" required>
                </div>
                <div class="form-group">
                    <label for="edit-motivo">Motivo de la Visita *</label>
                    <textarea id="edit-motivo" name="motivo_visita" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                <button type="button" onclick="cerrarModal()" class="btn btn-danger">Cancelar</button>
            </form>
        </div>
    </div>

    <script src="app.js"></script>
</body>

</html>