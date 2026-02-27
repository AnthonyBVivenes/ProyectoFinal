# Control de Visitantes al Campus

Proyecto final de Teoría y Taller de Lenguaje de Programación - Universidad De Oriente Núcleo Anzoátegui

## Integrantes
- **Anthony Vivenes** - Backend (Docker, Python, Base de datos, Correciones al frontend)
- **Gabriel Garanton** - Frontend (PHP, HTML, CSS, JavaScript)

## Descripción
Sistema de registro de visitantes para el campus universitario. Implementa una arquitectura de 3 capas:
- **Frontend**: PHP con Apache
- **Backend**: Python para generación de reportes
- **Base de datos**: MySQL

##  Tecnologías Utilizadas
- **Docker** & Docker Compose
- **MySQL 8.0**
- **PHP 8.2** con Apache
- **Python 3.9**
- **Git** & GitHub

## Configuración Inicial

### Prerrequisitos
- Docker Desktop instalado
- Git instalado
- Puerto 8080, 3306 libres

### Pasos para levantar el proyecto

1. **Clonar el repositorio**
```bash
git clone https://github.com/AnthonyBVivenes/ProyectoFinal.git
cd ProyectoFinal
```

### Configurar variables de entorno
Crear archivo .env en la raíz:

```
DB_HOST=
MYSQL_DATABASE=
MYSQL_USER=
MYSQL_PASSWORD=
MYSQL_ROOT_PASSWORD=
```

## Levantar los contenedores

```
docker-compose up -d
```

Es apropeado esperar unos 10 seg para que mysql y python inicializen correctamente

## Abrir la aplicación
http://localhost:8080