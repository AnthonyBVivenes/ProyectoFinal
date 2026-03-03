// Función para mostrar alertas
function mostrarAlerta(mensaje, tipo = "success") {
  const container = document.getElementById("alert-container");
  const alert = document.createElement("div");
  alert.className = `alert alert-${tipo}`;
  alert.textContent = mensaje;
  container.innerHTML = "";
  container.appendChild(alert);

  setTimeout(() => {
    alert.style.opacity = "0";
    setTimeout(() => alert.remove(), 300);
  }, 3000);
}

// Registrar un nuevo visitante
document
  .getElementById("form-visitante")
  .addEventListener("submit", function (e) {
    e.preventDefault();

    const nombre = document.getElementById("nombre_visitante").value.trim();
    const motivo = document.getElementById("motivo_visita").value.trim();

    if (!nombre || !motivo) {
      mostrarAlerta("Todos los campos son obligatorios", "error");
      return;
    }

    const formData = new FormData();
    formData.append("action", "insertar");
    formData.append("nombre_visitante", nombre);
    formData.append("motivo_visita", motivo);

    fetch("", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          mostrarAlerta(data.message, "success");
          document.getElementById("form-visitante").reset();
          setTimeout(() => location.reload(), 1000);
        } else {
          mostrarAlerta(data.message, "error");
        }
      })
      .catch((error) => {
        mostrarAlerta("Error al procesar la solicitud", "error");
      });
  });

// Cambiar el estado de salida
function cambiarEstado(id) {
  const formData = new FormData();
  formData.append("action", "cambiar_estado");
  formData.append("id", id);

  fetch("", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarAlerta(data.message, "success");
        setTimeout(() => location.reload(), 500);
      } else {
        mostrarAlerta(data.message, "error");
      }
    })
    .catch((error) => {
      mostrarAlerta("Error al cambiar el estado", "error");
    });
}

// Editar el visitante
function editarVisitante(id) {
  const formData = new FormData();
  formData.append("action", "obtener_visitante");
  formData.append("id", id);

  fetch("", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        document.getElementById("edit-id").value = data.data.id;
        document.getElementById("edit-nombre").value =
          data.data.nombre_visitante;
        document.getElementById("edit-motivo").value = data.data.motivo_visita;
        document.getElementById("modal-editar").style.display = "block";
      } else {
        mostrarAlerta("Error al cargar los datos", "error");
      }
    })
    .catch((error) => {
      mostrarAlerta("Error al cargar los datos", "error");
    });
}

// Guardar la edición
document.getElementById("form-editar").addEventListener("submit", function (e) {
  e.preventDefault();

  const nombre = document.getElementById("edit-nombre").value.trim();
  const motivo = document.getElementById("edit-motivo").value.trim();

  if (!nombre || !motivo) {
    mostrarAlerta("Todos los campos son obligatorios", "error");
    return;
  }

  const formData = new FormData();
  formData.append("action", "editar");
  formData.append("id", document.getElementById("edit-id").value);
  formData.append("nombre_visitante", nombre);
  formData.append("motivo_visita", motivo);

  fetch("", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarAlerta(data.message, "success");
        cerrarModal();
        setTimeout(() => location.reload(), 500);
      } else {
        mostrarAlerta(data.message, "error");
      }
    })
    .catch((error) => {
      mostrarAlerta("Error al actualizar", "error");
    });
});

// Eliminar el visitante
function eliminarVisitante(id) {
  if (!confirm("¿Está seguro de que desea eliminar este registro?")) {
    return;
  }

  const formData = new FormData();
  formData.append("action", "eliminar");
  formData.append("id", id);

  fetch("", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarAlerta(data.message, "success");
        const row = document.getElementById("row-" + id);
        if (row) {
          row.style.opacity = "0";
          setTimeout(() => {
            row.remove();
            if (document.querySelectorAll("tbody tr").length === 0) {
              location.reload();
            }
          }, 500);
        }
      } else {
        mostrarAlerta(data.message, "error");
      }
    })
    .catch((error) => {
      mostrarAlerta("Error al eliminar", "error");
    });
}

// Cerrar
function cerrarModal() {
  document.getElementById("modal-editar").style.display = "none";
}

window.onclick = function (event) {
  const modalEditar = document.getElementById("modal-editar");
  const modalHistorial = document.getElementById("modal-historial");
  if (event.target == modalEditar) {
    cerrarModal();
  }
  if (event.target == modalHistorial) {
    cerrarModalHistorial();
  }
};

// Abrir modal de Historial
function abrirHistorial() {
  document.getElementById("modal-historial").style.display = "block";
  // Resetear a vista de lista
  volverALista();
}

// Cerrar modal de Historial
function cerrarModalHistorial() {
  document.getElementById("modal-historial").style.display = "none";
  volverALista();
}

// Array para almacenar los datos de estudiantes (se llenará desde PHP)
let estudiantesData = [];

// Función para cargar datos de estudiantes desde el HTML
function cargarEstudiantesData() {
  const container = document.getElementById('lista-estudiantes');
  if (container && container.dataset.estudiantes) {
    estudiantesData = JSON.parse(container.dataset.estudiantes);
  }
}

// Mostrar detalle de un estudiante
function mostrarDetalleEstudiante(index) {
  // Obtener datos del estudiante desde el data attribute del contenedor
  const listaContainer = document.getElementById('lista-estudiantes');
  const estudiantes = JSON.parse(listaContainer.dataset.estudiantes || '[]');
  const est = estudiantes[index];
  
  if (!est) return;
  
  // Construir HTML del detalle
  const detalleHTML = construirDetalleHTML(est);
  document.getElementById('detalle-contenido').innerHTML = detalleHTML;
  
  // Ocultar lista, mostrar detalle
  document.getElementById('lista-estudiantes').style.display = 'none';
  document.getElementById('detalle-estudiante').style.display = 'block';
}

// Construir HTML del detalle del estudiante
function construirDetalleHTML(est) {
  const nombreCompleto = (est.nombre || '') + ' ' + (est.apellido || '');
  const foto = est.foto_perfil || '';
  const biografia = est.biografia || est.bio || 'Biografía no disponible';
  const habilidades = est.habilidades ? est.habilidades.split(',').map(h => h.trim()) : [];
  
  let html = `
    <div class="historial-container">
      <div class="historial-header">
        ${foto ? `<img src="${foto}" alt="Foto" class="historial-foto">` : `<div class="historial-foto historial-foto-placeholder">FOTO</div>`}
        <div class="historial-titulo">
          <h3>${nombreCompleto}</h3>
        </div>
        <button onclick="editarEstudiante(${est.id})" class="btn-editar-estudiante" title="Editar estudiante">Editar</button>
      </div>
      
      <div class="historial-datos" id="datos-estudiante-${est.id}">
        <div class="dato-card">
          <span class="dato-label">Biografía</span>
          <p class="dato-value">${biografia}</p>
        </div>
        
        ${est.email ? `
        <div class="dato-card">
          <span class="dato-label">Email</span>
          <p class="dato-value">${est.email}</p>
        </div>
        ` : ''}
        
        ${est.carrera ? `
        <div class="dato-card">
          <span class="dato-label">Carrera</span>
          <p class="dato-value">${est.carrera}</p>
        </div>
        ` : ''}
        
        ${est.semestre ? `
        <div class="dato-card">
          <span class="dato-label">Semestre</span>
          <p class="dato-value">${est.semestre}</p>
        </div>
        ` : ''}
        
        ${est.fecha_nacimiento ? `
        <div class="dato-card">
          <span class="dato-label">Fecha de Nacimiento</span>
          <p class="dato-value">${est.fecha_nacimiento}</p>
        </div>
        ` : ''}
        
        ${est.github_url ? `
        <div class="dato-card">
          <span class="dato-label">GitHub</span>
          <p class="dato-value"><a href="${est.github_url}" target="_blank" style="color: #667eea;">${est.github_url}</a></p>
        </div>
        ` : ''}
        
        ${est.linkedin_url ? `
        <div class="dato-card">
          <span class="dato-label">LinkedIn</span>
          <p class="dato-value"><a href="${est.linkedin_url}" target="_blank" style="color: #667eea;">${est.linkedin_url}</a></p>
        </div>
        ` : ''}
      </div>
      
      ${habilidades.length > 0 ? `
        <div class="historial-habilidades">
          <h4>Habilidades</h4>
          <div class="habilidades-list">
            ${habilidades.map(h => `<span class="habilidad-tag">${h}</span>`).join('')}
          </div>
        </div>
      ` : ''}

      <div class="acciones-estudiante">
        <button onclick="eliminarEstudiante(${est.id}, '${nombreCompleto.replace(/'/g, "\\'")}')" class="btn-eliminar-estudiante">
         Eliminar Estudiante
        </button>
      </div>
    </div>
  `;
  
  return html;
}

// Volver a la lista de estudiantes
function volverALista() {
  document.getElementById('lista-estudiantes').style.display = 'block';
  document.getElementById('detalle-estudiante').style.display = 'none';
}

// Editar estudiante - muestra formulario de edición
function editarEstudiante(id) {
  const listaContainer = document.getElementById('lista-estudiantes');
  const estudiantes = JSON.parse(listaContainer.dataset.estudiantes || '[]');
  const est = estudiantes.find(e => e.id == id);
  
  if (!est) return;
  
  const datosContainer = document.getElementById(`datos-estudiante-${id}`);
  
  const formHTML = `
    <form id="form-editar-estudiante-${id}" onsubmit="guardarEstudiante(event, ${id})">
      <div class="dato-card">
        <span class="dato-label">Biografía</span>
        <textarea name="biografia" class="input-editar" rows="3" required>${est.biografia || est.bio || ''}</textarea>
      </div>
      
      <div class="dato-card">
        <span class="dato-label">Nombre</span>
        <input type="text" name="nombre" class="input-editar" value="${est.nombre || ''}" required>
      </div>
      
      <div class="dato-card">
        <span class="dato-label">Apellido</span>
        <input type="text" name="apellido" class="input-editar" value="${est.apellido || ''}" required>
      </div>
      
      <div class="dato-card">
        <span class="dato-label">Email</span>
        <input type="email" name="email" class="input-editar" value="${est.email || ''}">
      </div>
      
      <div class="dato-card">
        <span class="dato-label">Carrera</span>
        <input type="text" name="carrera" class="input-editar" value="${est.carrera || ''}">
      </div>
      
      <div class="dato-card">
        <span class="dato-label">Semestre</span>
        <input type="number" name="semestre" class="input-editar" value="${est.semestre || ''}" min="1" max="12">
      </div>
      
      <div class="dato-card">
        <span class="dato-label">Habilidades</span>
        <input type="text" name="habilidades" class="input-editar" value="${est.habilidades || ''}" placeholder="Separadas por comas">
      </div>
      
      <div class="dato-card">
        <span class="dato-label">GitHub URL</span>
        <input type="url" name="github_url" class="input-editar" value="${est.github_url || ''}">
      </div>
      
      <div class="dato-card">
        <span class="dato-label">LinkedIn URL</span>
        <input type="url" name="linkedin_url" class="input-editar" value="${est.linkedin_url || ''}">
      </div>
      
      <div class="botones-edicion">
        <button type="submit" class="btn-guardar">Guardar Cambios</button>
        <button type="button" onclick="cancelarEdicion(${id})" class="btn-cancelar-edicion">Cancelar</button>
      </div>
    </form>
  `;
  
  datosContainer.dataset.originalHTML = datosContainer.innerHTML;
  datosContainer.innerHTML = formHTML;
  
  // Ocultar botón de editar durante la edición
  document.querySelector('.btn-editar-estudiante').style.display = 'none';
}

// Cancelar edición
function cancelarEdicion(id) {
  const datosContainer = document.getElementById(`datos-estudiante-${id}`);
  const listaContainer = document.getElementById('lista-estudiantes');
  const estudiantes = JSON.parse(listaContainer.dataset.estudiantes || '[]');
  const est = estudiantes.find(e => e.id == id);
  
  // Restaurar vista original
  const detalleHTML = construirDetalleHTML(est);
  document.getElementById('detalle-contenido').innerHTML = detalleHTML;
}

// Guardar cambios del estudiante
function guardarEstudiante(event, id) {
  event.preventDefault();
  
  const form = document.getElementById(`form-editar-estudiante-${id}`);
  const formData = new FormData(form);
  formData.append('action', 'editar_estudiante');
  formData.append('id', id);
  
  fetch("", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarAlerta(data.message, "success");
        // Recargar página para mostrar cambios actualizados
        setTimeout(() => location.reload(), 1000);
      } else {
        mostrarAlerta(data.message, "error");
      }
    })
    .catch((error) => {
      mostrarAlerta("Error al guardar los cambios", "error");
    });
}

// Eliminar estudiante
function eliminarEstudiante(id, nombre) {
  if (!confirm(`¿Está seguro de que desea eliminar al estudiante "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
    return;
  }
  
  const formData = new FormData();
  formData.append('action', 'eliminar_estudiante');
  formData.append('id', id);
  
  fetch("", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        mostrarAlerta(data.message, "success");
        // Recargar página para actualizar la lista
        setTimeout(() => location.reload(), 1000);
      } else {
        mostrarAlerta(data.message, "error");
      }
    })
    .catch((error) => {
      mostrarAlerta("Error al eliminar el estudiante", "error");
    });
}

document.addEventListener('DOMContentLoaded', function() {
});
