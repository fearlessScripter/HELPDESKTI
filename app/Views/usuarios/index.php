<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - HelpDesk TI</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar igual a la del Dashboard -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">HelpDesk TI</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Gestión de Accesos</span>
                <a href="<?= base_url('/dashboard') ?>" class="btn btn-sm btn-light text-primary fw-bold">Volver al Dashboard</a>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <h2 class="fw-bold">Directorio de Usuarios</h2>
                <p class="text-muted">Módulo para crear, editar y desactivar usuarios, además de asignarles el rol correspondiente.</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" onclick="alert('Simulación: Abriendo modal de nuevo usuario')">+ Nuevo Usuario</button>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre Completo</th>
                            <th>Correo Electrónico</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Carlos Ramos</td>
                            <td>admin@empresa.cl</td>
                            <td><span class="badge bg-dark">Administrador</span></td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary">Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Luis Técnico</td>
                            <td>tecnico@empresa.cl</td>
                            <td><span class="badge bg-info text-dark">Técnico</span></td>
                            <td><span class="badge bg-success">Activo</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary">Editar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Ana Usuario</td>
                            <td>usuario@empresa.cl</td>
                            <td><span class="badge bg-secondary">Solicitante</span></td>
                            <td><span class="badge bg-danger">Inactivo</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-secondary">Editar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>