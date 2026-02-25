<?php
$host = 'db';
$db   = 'proyecto';
$user = 'postgres';
$pass = 'postgres';
$port = '5432';

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
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
                        VALUES (:nombre, :motivo, NOW(), false)";
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
                $visitante = $stmt->fetch(PDO::FETCH_ASSOC);
                echo json_encode(['success' => true, 'data' => $visitante]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
            exit;
    }
}

#obtener datos Landing Page
try {
    $sqlEstudiante = "SELECT * FROM estudiante LIMIT 1";
    $stmtEstudiante = $pdo->prepare($sqlEstudiante);
    $stmtEstudiante->execute();
    $estudiante = $stmtEstudiante->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $estudiante = null;
}

try {
    $sql = "SELECT * FROM visitantes ORDER BY fecha_ingreso DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $visitantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
        }
        
        .landing-section {
            padding: 40px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        
        .estudiante-info {
            display: flex;
            gap: 30px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .foto-estudiante {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .info-text {
            flex: 1;
            min-width: 300px;
        }
        
        .info-text h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.8em;
        }
        
        .habilidades {
            margin-top: 15px;
        }
        
        .habilidades h3 {
            color: #667eea;
            margin-bottom: 10px;
        }
        
        .tag {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            margin: 5px 5px 5px 0;
            font-size: 0.9em;
        }
        
        .form-section {
            padding: 40px;
        }
        
        .form-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 600;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        
        .btn-info:hover {
            background: #138496;
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85em;
            margin: 2px;
        }
        
        .table-section {
            padding: 40px;
            overflow-x: auto;
        }
        
        .table-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: slideDown 0.3s;
        }
        
        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .actions-cell {
            white-space: nowrap;
        }
        
        @media (max-width: 768px) {
            .estudiante-info {
                flex-direction: column;
                text-align: center;
            }
            
            .btn-sm {
                display: block;
                width: 100%;
                margin: 5px 0;
            }
            
            table {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏛️ Control de Visitantes al Campus</h1>
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
                <button type="submit" class="btn btn-primary">➕ Registrar Visitante</button>
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
                                        <?php echo $visitante['salida_registrada'] ? '✅ Salida Registrada' : '⏳ En Campus'; ?>
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button onclick="cambiarEstado(<?php echo $visitante['id']; ?>)" 
                                            class="btn btn-info btn-sm">
                                        <?php echo $visitante['salida_registrada'] ? '↩️ Marcar Entrada' : '✅ Registrar Salida'; ?>
                                    </button>
                                    <button onclick="editarVisitante(<?php echo $visitante['id']; ?>)" 
                                            class="btn btn-warning btn-sm">✏️ Editar</button>
                                    <button onclick="eliminarVisitante(<?php echo $visitante['id']; ?>)" 
                                            class="btn btn-danger btn-sm">🗑️ Eliminar</button>
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
            <h2>✏️ Editar Visitante</h2>
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
                <button type="submit" class="btn btn-primary">💾 Guardar Cambios</button>
                <button type="button" onclick="cerrarModal()" class="btn btn-danger">❌ Cancelar</button>
            </form>
        </div>
    </div>
    
    <script>
        // Función para mostrar alertas
        function mostrarAlerta(mensaje, tipo = 'success') {
            const container = document.getElementById('alert-container');
            const alert = document.createElement('div');
            alert.className = `alert alert-${tipo}`;
            alert.textContent = mensaje;
            container.innerHTML = '';
            container.appendChild(alert);
            
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 3000);
        }
        
        // Registrar un nuevo visitante
        document.getElementById('form-visitante').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nombre = document.getElementById('nombre_visitante').value.trim();
            const motivo = document.getElementById('motivo_visita').value.trim();

            if (!nombre || !motivo) {
                mostrarAlerta('Todos los campos son obligatorios', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'insertar');
            formData.append('nombre_visitante', nombre);
            formData.append('motivo_visita', motivo);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta(data.message, 'success');
                    document.getElementById('form-visitante').reset();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    mostrarAlerta(data.message, 'error');
                }
            })
            .catch(error => {
                mostrarAlerta('Error al procesar la solicitud', 'error');
            });
        });
        
        // Cambiar el estado de salida
        function cambiarEstado(id) {
            const formData = new FormData();
            formData.append('action', 'cambiar_estado');
            formData.append('id', id);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta(data.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    mostrarAlerta(data.message, 'error');
                }
            })
            .catch(error => {
                mostrarAlerta('Error al cambiar el estado', 'error');
            });
        }
        
        // Editar el visitante
        function editarVisitante(id) {
            const formData = new FormData();
            formData.append('action', 'obtener_visitante');
            formData.append('id', id);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('edit-id').value = data.data.id;
                    document.getElementById('edit-nombre').value = data.data.nombre_visitante;
                    document.getElementById('edit-motivo').value = data.data.motivo_visita;
                    document.getElementById('modal-editar').style.display = 'block';
                } else {
                    mostrarAlerta('Error al cargar los datos', 'error');
                }
            })
            .catch(error => {
                mostrarAlerta('Error al cargar los datos', 'error');
            });
        }
        
        // Guardar la edición
        document.getElementById('form-editar').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const nombre = document.getElementById('edit-nombre').value.trim();
            const motivo = document.getElementById('edit-motivo').value.trim();

            if (!nombre || !motivo) {
                mostrarAlerta('Todos los campos son obligatorios', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'editar');
            formData.append('id', document.getElementById('edit-id').value);
            formData.append('nombre_visitante', nombre);
            formData.append('motivo_visita', motivo);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta(data.message, 'success');
                    cerrarModal();
                    setTimeout(() => location.reload(), 500);
                } else {
                    mostrarAlerta(data.message, 'error');
                }
            })
            .catch(error => {
                mostrarAlerta('Error al actualizar', 'error');
            });
        });
        
        // Eliminar el visitante
        function eliminarVisitante(id) {
            if (!confirm('¿Está seguro de que desea eliminar este registro?')) {
                return;
            }
            
            const formData = new FormData();
            formData.append('action', 'eliminar');
            formData.append('id', id);
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarAlerta(data.message, 'success');
                    const row = document.getElementById('row-' + id);
                    if (row) {
                        row.style.opacity = '0';
                        setTimeout(() => {
                            row.remove();
                            if (document.querySelectorAll('tbody tr').length === 0) {
                                location.reload();
                            }
                        }, 500);
                    }
                } else {
                    mostrarAlerta(data.message, 'error');
                }
            })
            .catch(error => {
                mostrarAlerta('Error al eliminar', 'error');
            });
        }
        
        // Cerrar 
        function cerrarModal() {
            document.getElementById('modal-editar').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('modal-editar');
            if (event.target == modal) {
                cerrarModal();
            }
        }
    </script>
</body>
</html>
