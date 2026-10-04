<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Ticket - HelpDesk TI</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .formulario { border: 1px solid #ccc; padding: 20px; max-width: 500px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; box-sizing: border-box; }
        button { padding: 10px 15px; background: #0056b3; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Registrar Nuevo Ticket</h1>
    <a href="<?= base_url('/dashboard') ?>">← Volver al Dashboard</a>
    <br><br>

    <div class="formulario">
        <!-- El form no hace nada por ahora (simulación) -->
        <form action="#" method="get" onsubmit="alert('Modo vista: Ticket simulado con éxito'); return false;">
            <div class="form-group">
                <label>Área Afectada</label>
                <select>
                    <option>Soporte TI</option>
                    <option>Redes y Conectividad</option>
                    <option>Sistemas ERP</option>
                </select>
            </div>
            <div class="form-group">
                <label>Descripción del Problema</label>
                <textarea rows="4" placeholder="Describe la incidencia..."></textarea>
            </div>
            <button type="submit">Enviar Ticket</button>
        </form>
    </div>
</body>
</html>