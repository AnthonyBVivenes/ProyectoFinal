<?php
header('Content-Type: text/html; charset=utf-8');

$host = getenv('DB_HOST');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$port = getenv('DB_PORT');





try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
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

        case 'editar_estudiante':
            if (!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            try {
                $sql = "UPDATE estudiante SET 
                    nombre = :nombre,
                    apellido = :apellido,
                    biografia = :biografia,
                    carrera = :carrera,
                    semestre = :semestre,
                    email = :email,
                    habilidades = :habilidades,
                    github_url = :github_url,
                    linkedin_url = :linkedin_url
                    WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':id' => $_POST['id'],
                    ':nombre' => $_POST['nombre'] ?? '',
                    ':apellido' => $_POST['apellido'] ?? '',
                    ':biografia' => $_POST['biografia'] ?? '',
                    ':carrera' => $_POST['carrera'] ?? '',
                    ':semestre' => $_POST['semestre'] ?? null,
                    ':email' => $_POST['email'] ?? '',
                    ':habilidades' => $_POST['habilidades'] ?? '',
                    ':github_url' => $_POST['github_url'] ?? '',
                    ':linkedin_url' => $_POST['linkedin_url'] ?? ''
                ]);
                echo json_encode(['success' => true, 'message' => 'Estudiante actualizado correctamente']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;

        case 'eliminar_estudiante':
            if (!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID requerido']);
                exit;
            }

            try {
                $sql = "DELETE FROM estudiante WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id' => $_POST['id']]);
                echo json_encode(['success' => true, 'message' => 'Estudiante eliminado correctamente']);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
    }
}


try {
    $sqlEstudiante = "SELECT * FROM estudiante ORDER BY fecha_registro DESC";
    $stmtEstudiante = $pdo->prepare($sqlEstudiante);
    $stmtEstudiante->execute();
    $estudiantes = $stmtEstudiante->fetchAll();
    $estudiante = $estudiantes[0] ?? null; // Mantener primer estudiante para la landing section
} catch (PDOException $e) {
    $estudiantes = [];
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

$reportePath = __DIR__ . '/reportes/reporte.txt';
$reporteContenido = null;
if (file_exists($reportePath) && is_readable($reportePath)) {
    $reporteContenido = file_get_contents($reportePath);
}
?>




<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
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
                        <p><?php echo htmlspecialchars($estudiante['bio'] ?? 'Estudiante de desarrollo web con experiencia en PHP, MySQL y Docker.'); ?></p>
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
                        FOTO
                    </div>
                    <div class="info-text">
                        <h2>Estudiante:</h2>
                        <p>Información del estudiante</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="report-section">
            <div class="report-header">
                <h2>Reporte</h2>
                <button onclick="abrirHistorial()" class="btn btn-primary btn-historial">📋 Historial</button>
            </div>
            <?php if ($reporteContenido): ?>
                <pre class="report-content"><?php echo htmlspecialchars($reporteContenido); ?></pre>
            <?php else: ?>
                <p class="report-empty">No hay reporte aun</p>
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

    <!-- Modal para Historial -->
    <div id="modal-historial" class="modal">
        <div class="modal-content modal-historial-content">
            <span class="close" onclick="cerrarModalHistorial()">&times;</span>
            <h2>Historial de Estudiantes</h2>
            
            <!-- Vista de Lista de Estudiantes -->
            <div id="lista-estudiantes" class="lista-estudiantes-container" data-estudiantes='<?php echo htmlspecialchars(json_encode($estudiantes), ENT_QUOTES, 'UTF-8'); ?>'>
                <?php if (!empty($estudiantes)): ?>
                    <div class="estudiantes-grid">
                        <?php foreach ($estudiantes as $index => $est): ?>
                            <div class="estudiante-card" onclick="mostrarDetalleEstudiante(<?php echo $index; ?>)">
                                <?php if (!empty($est['foto_perfil'])): ?>
                                    <img src="<?php echo htmlspecialchars($est['foto_perfil']); ?>" alt="Foto" class="estudiante-card-foto">
                                <?php else: ?>
                                    <div class="estudiante-card-foto estudiante-card-placeholder">👤</div>
                                <?php endif; ?>
                                <div class="estudiante-card-info">
                                    <h4><?php echo htmlspecialchars(($est['nombre'] ?? '') . ' ' . ($est['apellido'] ?? '')); ?></h4>
                                    <p class="estudiante-card-carrera"><?php echo htmlspecialchars($est['carrera'] ?? 'Sin carrera'); ?></p>
                                    <span class="estudiante-card-semestre">Semestre <?php echo htmlspecialchars($est['semestre'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="estudiante-card-arrow">→</div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="historial-empty">
                        <p>No hay estudiantes registrados.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Vista de Detalle de Estudiante -->
            <div id="detalle-estudiante" class="detalle-estudiante-container" style="display: none;">
                <button onclick="volverALista()" class="btn-volver">← Volver a la lista</button>
                <div id="detalle-contenido"></div>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>

</html>