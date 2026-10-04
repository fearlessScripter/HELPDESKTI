<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cola de Tickets - HelpDesk TI</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background-color: #f4f4f4; }
        .btn-asignar { background: #28a745; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Cola Operativa: Tickets Sin Asignar</h1>
    <a href="<?= base_url('/dashboard') ?>">← Volver al Menú Principal</a>
    
    <table>
        <thead>
            <tr>
                <th>ID Ticket</th>
                <th>Solicitante</th>
                <th>Área</th>
                <th>Prioridad (SLA)</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#TK-1042</td>
                <td>Alejandro Silva</td>
                <td>Redes y Conectividad</td>
                <td style="color: red; font-weight: bold;">Alta (2h)</td>
                <td><a href="#" class="btn-asignar" onclick="alert('Simulación: Abriendo modal para asignar técnico'); return false;">Asignar Técnico</a></td>
            </tr>
            <tr>
                <td>#TK-1043</td>
                <td>Beatriz Fuentes</td>
                <td>Sistemas ERP</td>
                <td style="color: orange; font-weight: bold;">Media (4h)</td>
                <td><a href="#" class="btn-asignar" onclick="alert('Simulación: Abriendo modal para asignar técnico'); return false;">Asignar Técnico</a></td>
            </tr>
        </tbody>
    </table>
</body>
</html>