<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>CRUD Canjes — API REST</title>
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
    Gestión de Canjes de Puntos
  </h1>
  <p class="subtitulo">CRUD con API REST — Control total desde un solo lugar</p>
</div>
  <div class="container">
    <div class="d-flex align-items-center mb-4">
      <a href="Modulo_API_REST.php" class="btn btn-light me-3">←</a>
    </div>

    <button
      class="btn btn-success mb-3"
      data-bs-toggle="modal"
      data-bs-target="#m"
      onclick="openForm()"
    >Registrar canje</button>

    <div class="table-responsive bg-white p-3 shadow-sm rounded">
      <div class="d-flex align-items-center justify-content-between mb-2 px-2">
  <h5 class="text-primary fw-bold mb-0">
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#3498db"
         class="bi bi-table" viewBox="0 0 16 16">
      <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v2H0V2zm0 3h16v2H0V5zm0 3h16v2H0V8zm0 
               3h16v2H0v-2z"/>
    </svg>
    Lista de Canjes de Puntos realizados
  </h5>
</div>

      <table id="tbl" class="table table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Premio</th>
            <th>Puntos usados</th>
            <th>Puntos restantes</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  <!-- Modal Redimir / Editar -->
  <div class="modal fade" id="m">
    <div class="modal-dialog">
      <form id="f" class="modal-content">
        <div class="modal-header">
          <h5 id="ht" class="modal-title">Canjear Puntos</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="i">

          <!-- Cliente actual -->
          <div id="current-client-wrap" class="mb-3" style="display:none;">
            <p><strong>Cliente actual:</strong> <span id="current-client"></span></p>
          </div>
          <div class="mb-3">
            <label class="form-label">Seleccionar Cliente</label>
            <select id="c" class="form-select" required>
              <option value="" disabled selected>Selecciona uno...</option>
            </select>
          </div>

          <!-- Premio actual -->
          <div id="current-prize-wrap" class="mb-3" style="display:none;">
            <p><strong>Premio actual:</strong> <span id="current-prize"></span></p>
          </div>
          <div class="mb-3">
            <label class="form-label">Seleccionar Premio</label>
            <select id="p" class="form-select" required>
              <option value="" disabled selected>Selecciona uno...</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Puntos a usar</label>
            <input id="u" class="form-control" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Puntos Restantes</label>
            <input id="r" class="form-control" readonly>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Guardar</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const API = 'api.php/canje_puntos';
    let clientes = [], premios = [];

    async function load() {
      const [resR, resC, resP] = await Promise.all([
        fetch(API),
        fetch('api.php/clientes'),
        fetch('api.php/premios')
      ]);
      const [reds, cls, prs] = await Promise.all([resR.json(), resC.json(), resP.json()]);
      clientes = cls;
      premios   = prs;
      pintarTabla(reds);
    }

    function pintarTabla(reds) {
      const tbody = document.querySelector('#tbl tbody');
      tbody.innerHTML = '';
      reds.forEach(r => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${r.id_canje}</td>
          <td>${r.nombre} ${r.apellidos}</td>
          <td>${r.premio}</td>
          <td>${r.puntos_usados}</td>
          <td>${r.puntos_restantes}</td>
          <td>${r.fecha}</td>
          <td>
            <button class="btn btn-sm btn-warning editar-btn">Editar</button>
            <button class="btn btn-sm btn-danger" onclick="del(${r.id_canje})">Borrar</button>
          </td>`;
        tbody.appendChild(tr);
        tr.querySelector('.editar-btn').addEventListener('click', () => {
          openForm(r);
          new bootstrap.Modal(document.getElementById('m')).show();
        });
      });
    }

    function poblarSelect(idSelect, items, valField, textFn, dataAttr) {
      const sel = document.getElementById(idSelect);
      sel.innerHTML = '<option value="" disabled selected>Selecciona uno...</option>';
      items.forEach(o => {
        const dataValue = dataAttr === 'pts' ? o.puntos_actuales : o.puntos_requeridos;
        sel.insertAdjacentHTML('beforeend',
          `<option value="${o[valField]}" data-${dataAttr}="${dataValue}">${textFn(o)}</option>`);
      });
    }

    function openForm(r = {}) {
      const isEdit = Boolean(r.id_canje_puntos);
      document.getElementById('i').value = r.id_canje_puntos || '';
      document.getElementById('ht').textContent =
        isEdit ? 'Editar Redención' : 'Redimir Puntos';

      // Mostrar/ocultar info actual
      document.getElementById('current-client-wrap').style.display = isEdit ? 'block' : 'none';
      document.getElementById('current-client').textContent = isEdit ? `${r.nombre} ${r.apellidos}` : '';
      document.getElementById('current-prize-wrap').style.display  = isEdit ? 'block' : 'none';
      document.getElementById('current-prize').textContent  = isEdit ? r.premio : '';

      // Poblar selects y asignar valor
      poblarSelect('c', clientes, 'id_cliente', o => `${o.nombre} ${o.apellidos}`, 'pts');
      poblarSelect('p', premios,   'id_premio',  o => o.nombre,                    'req');
      if (isEdit) {
        document.getElementById('c').value = r.id_cliente;
        document.getElementById('p').value = r.id_premio;
      }

      // Inicializar campos
      if (isEdit) {
        document.getElementById('u').value = r.puntos_usados;
        document.getElementById('r').value = r.puntos_restantes;
      } else {
        document.getElementById('u').value = '';
        document.getElementById('r').value = '';
      }
    }

    // Recalcular siempre que cambie el premio o cliente
    document.getElementById('c').addEventListener('change', update);
    document.getElementById('p').addEventListener('change', update);

    function update() {
      const optC = document.querySelector('#c option:checked'),
            optP = document.querySelector('#p option:checked');
      const ptsC = optC ? +optC.dataset.pts : 0,
            reqP = optP ? +optP.dataset.req : 0;
      document.getElementById('u').value = reqP;
      document.getElementById('r').value = Math.max(ptsC - reqP, 0);
    }

    document.getElementById('f').addEventListener('submit', async e => {
      e.preventDefault();
      const id = document.getElementById('i').value;
      const payload = {
        id_cliente:    +document.getElementById('c').value,
        id_premio:     +document.getElementById('p').value,
        puntos_usados: +document.getElementById('u').value
      };
      const res = await fetch(id ? `${API}/${id}` : API, {
        method:  id ? 'PUT' : 'POST',
        headers: {'Content-Type':'application/json'},
        body:    JSON.stringify(payload)
      });
      if (!res.ok) {
        console.error(await res.text());
        alert('Error al guardar.');
        return;
      }
      bootstrap.Modal.getInstance(document.getElementById('m')).hide();
      load();
    });

    async function del(id) {
      if (!confirm('¿Eliminar esta redención?')) return;
      const res = await fetch(`${API}/${id}`, { method:'DELETE' });
      if (!res.ok) {
        console.error(await res.text());
        alert('No se pudo eliminar.');
        return;
      }
      load();
    }

    load();
  </script>
</body>
</html>
