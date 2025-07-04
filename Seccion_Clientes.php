<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>CRUD Clientes — API REST</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f0f2f5;
    color: #333;
  }

  h1 {
    color: #2c3e50;
    text-align: center;
    margin-bottom: 2rem;
    font-weight: 700;
  }

  .btn-primary {
    background-color: #3498db;
    border: none;
    color: white;
    font-weight: 600;
  }

  .btn-primary:hover {
    background-color: #2980b9;
  }

  .btn-warning {
    background-color: #f39c12;
    border: none;
    color: white;
  }

  .btn-warning:hover {
    background-color: #e67e22;
  }

  .btn-danger {
    background-color: #e74c3c;
    border: none;
    color: white;
  }

  .btn-danger:hover {
    background-color: #c0392b;
  }

  .table-responsive {
    background: #ffffff;
    border-radius: 12px;
    padding: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
  }

  .table {
    color: #333;
    vertical-align: middle;
  }

  .table th {
    background-color: #ecf0f1 !important;
    color: #34495e;
    font-weight: 600;
  }

  .table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #f9f9f9;
  }

  .thumb {
    width: 80px;
    height: 50px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ccc;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  }

  .modal-content {
    background-color: #ffffff;
    color: #333;
    border-radius: 12px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
  }

  .form-control, .form-check-input {
    background-color: #ffffff;
    color: #333;
    border: 1px solid #ccc;
    border-radius: 8px;
  }

  .form-control:focus {
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.3);
    border-color: #3498db;
    background-color: #fff;
  }

  .form-check-label {
    margin-left: 6px;
    font-weight: 500;
  }

  .btn-close {
    filter: none;
  }

  .container h1 svg {
    margin-right: 8px;
  }

  .modal-header, .modal-footer {
    border: none;
  }
  .titulo-contenedor {
  background: #ffffff;
  border-left: 6px solid #3498db;
  padding: 2rem;
  max-width: 900px;
  margin: 0 auto;
}

.titulo-estilizado {
  font-size: 2.5rem;
  font-weight: 700;
  background: linear-gradient(to right, #3498db, #6dd5fa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  position: relative;
  display: inline-block;
}

.titulo-estilizado::after {
  content: "";
  position: absolute;
  bottom: -10px;
  left: 25%;
  width: 50%;
  height: 3px;
  background: #3498db;
  border-radius: 3px;
  box-shadow: 0 2px 10px rgba(52, 152, 219, 0.3);
  transition: width 0.3s ease;
}

.titulo-estilizado:hover::after {
  width: 80%;
  left: 10%;
}

.subtitulo {
  font-size: 1rem;
  color: #7f8c8d;
  margin-top: 0.5rem;
  font-weight: 500;
}
.btn-agregar {
  background: linear-gradient(to right, #3498db, #5dade2);
  color: white;
  font-weight: 600;
  border: none;
  border-radius: 8px;
  padding: 10px 20px;
  transition: all 0.3s ease;
}

.btn-agregar:hover {
  background: linear-gradient(to right, #2980b9, #3498db);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
}
.table {
  border: 1px solid #dee2e6;
  border-radius: 8px;
  overflow: hidden;
}

.table th, .table td {
  vertical-align: middle;
}

.table th {
  background-color: #e9eff4 !important;
  color: #2c3e50;
  font-weight: 600;
  border-bottom: 2px solid #d1dbe4;
}

.table-striped > tbody > tr:hover {
  background-color: #f1f8ff;
  transition: background 0.2s;
}
</style>

</head>
<body class="p-4">
  <div class="titulo-contenedor text-center py-4 mb-4 shadow-sm rounded">
  <h1 class="titulo-estilizado">
    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#3498db" class="bi bi-stars me-2" viewBox="0 0 16 16">
      <path d="M7.5 0l1.354 3.646L12.5 5l-3.646 1.354L7.5 10l-1.354-3.646L2.5 5l3.646-1.354L7.5 0zM0 13l.75 2 .75-2 2-.75-2-.75-.75-2-.75 2-2 .75 2 .75zm12 0l.75 2 .75-2 2-.75-2-.75-.75-2-.75 2-2 .75 2 .75z"/>
    </svg>
    Gestión de Clientes
  </h1>
  <p class="subtitulo">CRUD con API REST — Control total desde un solo lugar</p>
</div>
  <div class="container">
        <div class="container d-flex align-items-center mb-4">
  <a href="Modulo_API_REST.php" class="btn btn-light me-3" aria-label="Volver al panel principal">
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
         class="bi bi-arrow-left" viewBox="0 0 16 16">
      <path fill-rule="evenodd"
            d="M15 8a.5.5 0 0 1-.5.5H2.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 
               0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L2.707 
               7.5H14.5A.5.5 0 0 1 15 8z"/>
    </svg>
  </a>
</div>

    <button
      class="btn btn-primary mb-3"
      data-bs-toggle="modal"
      data-bs-target="#formModal"
      onclick="openForm()"
    >Agregar Cliente</button>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
      <div class="d-flex align-items-center justify-content-between mb-2 px-2">
  <h5 class="text-primary fw-bold mb-0">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#3498db"
         class="bi bi-table" viewBox="0 0 16 16">
      <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2H0V2zm0 3h16v2H0V5zm0 3h16v2H0V8zm0 
               3h16v2H0v-2z"/>
    </svg>
    Lista de clientes registrados
  </h5>
</div>

      <table id="tbl" class="table table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>ID</th><th>Nombre</th><th>Apellidos</th><th>Teléfono</th>
            <th>Dirección</th><th>Correo</th><th>Estado</th>
            <th>Ciudad</th><th>Tarjeta Digital</th><th>Puntos</th>
          <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Modal para crear/editar -->
  <div class="modal fade" id="formModal">
    <div class="modal-dialog">
      <form id="form" class="modal-content">
        <div class="modal-header">
          <h5 id="title" class="modal-title">Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="id">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input id="nombre" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Apellidos</label>
            <input id="apellidos" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input id="telefono_movil" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input id="direccion" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Correo</label>
            <input id="correo_electronico" type="email" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Estado</label>
            <input id="estado" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Ciudad</label>
            <input id="ciudad" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Tarjeta Digital</label>
            <input id="tarjeta_digital" class="form-control">
          </div>
          <div class="mb-3">
            <label class="form-label">Puntos</label>
            <input id="puntos" type="number" class="form-control" value="0">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary">Guardar</button>
        </div>
      </form>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const API = 'api.php/clientes';

    // Carga y pinta la tabla
    async function load() {
      const res = await fetch(API);
      if (!res.ok) {
        console.error('Error al cargar clientes:', await res.text());
        return;
      }
      const data = await res.json();
      const tbody = document.querySelector('#tbl tbody');
      tbody.innerHTML = '';
      data.forEach(c => {
        tbody.insertAdjacentHTML('beforeend', `
          <tr>
            <td>${c.id_cliente}</td>
            <td>${c.nombre}</td>
            <td>${c.apellidos}</td>
            <td>${c.telefono_movil}</td>
            <td>${c.direccion||''}</td>
            <td>${c.correo_electronico||''}</td>
            <td>${c.estado||''}</td>
            <td>${c.ciudad||''}</td>
            <td>${c.tarjeta_digital||''}</td>
            <td>${c.puntos}</td>
            <td>
              <button
                class="btn btn-sm btn-warning"
                data-bs-toggle="modal"
                data-bs-target="#formModal"
                onclick='openForm(${JSON.stringify(c)})'
              >Editar</button>
              <button class="btn btn-sm btn-danger" onclick="del(${c.id_cliente})">
                Borrar
              </button>
            </td>
          </tr>`);
      });
    }

    // Abre modal y llena campos
    function openForm(c = {}) {
      document.getElementById('id').value = c.id_cliente || '';
      ['nombre','apellidos','telefono_movil','direccion','correo_electronico','estado','ciudad','tarjeta_digital','puntos']
        .forEach(f => document.getElementById(f).value = c[f] ?? '');
      document.getElementById('title').textContent =
        c.id_cliente ? 'Editar Cliente' : 'Nuevo Cliente';
    }

    // Crear / editar
    document.getElementById('form').addEventListener('submit', async e => {
      e.preventDefault();
      const id = document.getElementById('id').value;
      const payload = {
        nombre: document.getElementById('nombre').value,
        apellidos: document.getElementById('apellidos').value,
        telefono_movil: document.getElementById('telefono_movil').value,
        direccion: document.getElementById('direccion').value,
        correo_electronico: document.getElementById('correo_electronico').value,
        estado: document.getElementById('estado').value,
        ciudad: document.getElementById('ciudad').value,
        tarjeta_digital: document.getElementById('tarjeta_digital').value,
        puntos: parseInt(document.getElementById('puntos').value, 10) || 0
      };
      const opts = {
        method: id ? 'PUT' : 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
      };
      const url = id ? `${API}/${id}` : API;
      const res = await fetch(url, opts);
      if (!res.ok) {
        console.error('Error al guardar:', await res.text());
        alert('No se pudo guardar. Mira la consola.');
        return;
      }
      bootstrap.Modal.getInstance(document.getElementById('formModal')).hide();
      load();
    });

    // Eliminar
    async function del(id) {
      if (!confirm('¿Eliminar este cliente?')) return;
      const res = await fetch(`${API}/${id}`, { method: 'DELETE' });
      if (!res.ok) {
        console.error('Error al eliminar:', await res.text());
        alert('No se pudo eliminar.');
        return;
      }
      load();
    }

    // Al cargar la página
    load();
  </script>
</body>
</html>
