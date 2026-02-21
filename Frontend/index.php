<?php  ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Visitantes — Portafolio</title>
    <meta name="description" content="Landing page base del proyecto">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{
            --bg-900:#071226;
            --bg-800:#0b2236;
            --card:#0f2a44;
            --muted:#9fb0c8;
            --accent:#7c3aed; /* morado */
            --accent-2:#06b6d4; /* cyan */
            --glass: rgba(255,255,255,0.03);
        }
        *{box-sizing:border-box}
        body{background:linear-gradient(180deg,var(--bg-900),var(--bg-800));color:#e9f4ff;font-family:Inter,system-ui,Segoe UI,Arial;margin:0;padding:36px}
        .wrap{max-width:1100px;margin:0 auto}
        .card-main{background:linear-gradient(180deg,var(--card),#071a2b);border-radius:14px;padding:28px;box-shadow:0 12px 40px rgba(2,6,23,0.6);border:1px solid rgba(255,255,255,0.02)}
        .avatar{width:108px;height:108px;border-radius:14px;object-fit:cover;border:3px solid rgba(255,255,255,0.04)}
        .muted{color:var(--muted)}
        .badge-skill{background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#fff;padding:6px 10px;border-radius:999px;margin-right:6px;font-weight:600}
        .hero-title{font-weight:700;margin-bottom:6px}
        .subtile{color:var(--muted);margin-bottom:14px}
        .section{margin-top:18px}
        footer{margin-top:22px;color:var(--muted);font-size:13px}
        a.link-soft{color:var(--accent-2);text-decoration:none}
        @media(max-width:720px){body{padding:20px}.d-lg-flex{flex-direction:column}.avatar{width:84px;height:84px}}
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card-main">
            <div class="d-flex align-items-center justify-content-between mb-3 d-lg-flex">
                <div class="d-flex align-items-center gap-3">
                    <img class="avatar" src="https://placehold.co/240x240/png?text=Foto" alt="Foto Alumno">
                    <div>
                        <h1 class="hero-title">Control de Visitantes</h1>
                        <div class="subtile muted">Proyecto: Registro de visitantes al campus — Pareja 3</div>
                        <div class="muted">Alumno: <strong>Nombre Apellido</strong></div>
                    </div>
                </div>
                <div class="text-end d-none d-md-block">
                    <div class="badge-skill">PHP</div>
                    <div class="badge-skill" style="margin-left:8px">Python</div>
                    <div class="badge-skill" style="margin-left:8px">Docker</div>
                </div>
            </div>

            <div class="row align-items-start">
                <div class="col-lg-7">
                    <h4>Resumen</h4>
                    <p class="muted">Esta página es la versión base y estática del frontend. Aquí se presentará la información del alumno, foto, habilidades y un formulario/tabla cuando se implemente el backend y la base de datos en contenedores.</p>
                    <div class="section">
                        <h5>Objetivos del proyecto</h5>
                        <ul class="muted">
                            <li>Desplegar una aplicación web 3 capas con contenedores.</li>
                            <li>Implementar CRUD desde la interfaz (posterior implementación).</li>
                            <li>Persistencia con PostgreSQL o MySQL usando volúmenes de Docker.</li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-5">
                    <h5>Contacto</h5>
                    <p class="muted mb-1"><strong>Email:</strong> correo@ejemplo.com</p>
                    <p class="muted mb-1"><strong>Teléfono:</strong> +56 9 0000 0000</p>
                    <div class="section">
                        <h6>Habilidades</h6>
                        <div class="mt-2">
                            <span class="badge-skill">PHP</span>
                            <span class="badge-skill">Python</span>
                            <span class="badge-skill">Docker</span>
                            <span class="badge-skill">SQL</span>
                        </div>
                    </div>
                </div>
            </div>

            <footer>
                <div class="d-flex justify-content-between align-items-center">
                    <div>Versión base estática — Backend por implementar</div>
                    <div class="muted">Guía: Despliegue y gestión web con contenedores</div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
