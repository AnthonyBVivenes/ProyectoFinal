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
  const modal = document.getElementById("modal-editar");
  if (event.target == modal) {
    cerrarModal();
  }
};
