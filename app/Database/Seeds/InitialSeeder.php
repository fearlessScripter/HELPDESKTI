<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Catálogo: roles
        $roles = [
            ['id_rol' => 1, 'nombre' => 'Administrador'],
            ['id_rol' => 2, 'nombre' => 'Técnico'],
            ['id_rol' => 3, 'nombre' => 'Solicitante'],
        ];
        $db->table('roles')->ignore(true)->insertBatch($roles);

        // 2. Catálogo: estados_ticket
        $estados = [
            ['id_estado' => 1, 'nombre' => 'Pendiente'],
            ['id_estado' => 2, 'nombre' => 'En Proceso'],
            ['id_estado' => 3, 'nombre' => 'Resuelto'],
            ['id_estado' => 4, 'nombre' => 'Cerrado'],
        ];
        $db->table('estados_ticket')->ignore(true)->insertBatch($estados);

        // 3. Catálogo: prioridades
        $prioridades = [
            ['id_prioridad' => 1, 'nombre' => 'Baja',  'tiempo_sla_minutos' => 1440], // 1 día
            ['id_prioridad' => 2, 'nombre' => 'Media', 'tiempo_sla_minutos' => 240],  // 4 h
            ['id_prioridad' => 3, 'nombre' => 'Alta',  'tiempo_sla_minutos' => 120],  // 2 h
        ];
        $db->table('prioridades')->ignore(true)->insertBatch($prioridades);

        // 4. Catálogo: areas_departamentos
        $areas = [
            ['id_area' => 1, 'nombre' => 'Soporte TI (Equipos y Hardware)', 'estado' => 'activo'],
            ['id_area' => 2, 'nombre' => 'Redes y Conectividad (Internet/WiFi)', 'estado' => 'activo'],
            ['id_area' => 3, 'nombre' => 'Sistemas ERP (Cuentas y Software)', 'estado' => 'activo'],
        ];
        $db->table('areas_departamentos')->ignore(true)->insertBatch($areas);

        // 5. Usuarios iniciales de prueba (password: "123456" con hash SHA-256)
        $passwordHash = hash('sha256', '123456');
        $now = date('Y-m-d H:i:s');

        $usuarios = [
            [
                'id_usuario'      => 1,
                'id_rol'          => 1, // Administrador
                'id_area'         => 1,
                'nombre_completo' => 'Carlos Administrador',
                'correo'          => 'admin@empresa.cl',
                'password_hash'   => $passwordHash,
                'estado'          => 'activo',
                'fecha_creacion'  => $now,
            ],
            [
                'id_usuario'      => 2,
                'id_rol'          => 2, // Técnico
                'id_area'         => 1,
                'nombre_completo' => 'Luis Técnico',
                'correo'          => 'tecnico@empresa.cl',
                'password_hash'   => $passwordHash,
                'estado'          => 'activo',
                'fecha_creacion'  => $now,
            ],
            [
                'id_usuario'      => 3,
                'id_rol'          => 3, // Solicitante
                'id_area'         => 3,
                'nombre_completo' => 'Ana Solicitante',
                'correo'          => 'usuario@empresa.cl',
                'password_hash'   => $passwordHash,
                'estado'          => 'activo',
                'fecha_creacion'  => $now,
            ],
        ];
        $db->table('usuarios')->ignore(true)->insertBatch($usuarios);

        // 6. Tickets de ejemplo para probar todos los estados y la interfaz
        $tickets = [
            [
                'id_ticket'             => 1,
                'codigo_ticket'         => 'TK-1001',
                'id_solicitante'        => 3, // Ana
                'id_tecnico_asignado'   => null,
                'id_area_afectada'      => 2, // Redes
                'nivel_impacto'         => 'medio',
                'nivel_urgencia'        => 'alta',
                'id_prioridad'          => 3, // Alta
                'id_estado'             => 1, // Pendiente
                'asunto'                => 'Sin conexión a internet en oficina 204',
                'descripcion'           => 'Desde esta mañana ningún equipo del departamento de contabilidad tiene acceso a internet por cable ni WiFi.',
                'solucion_aplicada'     => null,
                'fecha_creacion'        => date('Y-m-d H:i:s', strtotime('-3 hours')),
                'fecha_asignacion'      => null,
                'fecha_resolucion'      => null,
                'fecha_cierre'          => null,
                'fecha_vencimiento_sla' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
            [
                'id_ticket'             => 2,
                'codigo_ticket'         => 'TK-1002',
                'id_solicitante'        => 3, // Ana
                'id_tecnico_asignado'   => 2, // Luis Técnico
                'id_area_afectada'      => 1, // Soporte TI
                'nivel_impacto'         => 'bajo',
                'nivel_urgencia'        => 'media',
                'id_prioridad'          => 2, // Media
                'id_estado'             => 2, // En Proceso
                'asunto'                => 'Monitor parpadea y no enciende correctamente',
                'descripcion'           => 'El monitor secundario se apaga cada 5 minutos y muestra líneas horizontales.',
                'solucion_aplicada'     => null,
                'fecha_creacion'        => date('Y-m-d H:i:s', strtotime('-2 hours')),
                'fecha_asignacion'      => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'fecha_resolucion'      => null,
                'fecha_cierre'          => null,
                'fecha_vencimiento_sla' => date('Y-m-d H:i:s', strtotime('+2 hours')),
            ],
            [
                'id_ticket'             => 3,
                'codigo_ticket'         => 'TK-1003',
                'id_solicitante'        => 3, // Ana
                'id_tecnico_asignado'   => 2, // Luis Técnico
                'id_area_afectada'      => 3, // ERP
                'nivel_impacto'         => 'medio',
                'nivel_urgencia'        => 'baja',
                'id_prioridad'          => 1, // Baja
                'id_estado'             => 3, // Resuelto (esperando validación del solicitante)
                'asunto'                => 'Restablecer contraseña ERP módulo compras',
                'descripcion'           => 'Olvidé mi clave de acceso al ERP tras regresar de vacaciones.',
                'solucion_aplicada'     => 'Se generó contraseña temporal y se activó el doble factor de autenticación en su cuenta.',
                'fecha_creacion'        => date('Y-m-d H:i:s', strtotime('-10 hours')),
                'fecha_asignacion'      => date('Y-m-d H:i:s', strtotime('-9 hours')),
                'fecha_resolucion'      => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'fecha_cierre'          => null,
                'fecha_vencimiento_sla' => date('Y-m-d H:i:s', strtotime('+14 hours')),
            ],
        ];
        $db->table('tickets')->ignore(true)->insertBatch($tickets);

        // 7. Historial de los tickets
        $historial = [
            [
                'id_ticket'          => 1,
                'id_usuario_accion'  => 3,
                'accion'             => 'Ticket Creado',
                'descripcion'        => 'Ticket registrado en estado Pendiente por el solicitante.',
                'fecha_registro'     => date('Y-m-d H:i:s', strtotime('-3 hours')),
            ],
            [
                'id_ticket'          => 2,
                'id_usuario_accion'  => 3,
                'accion'             => 'Ticket Creado',
                'descripcion'        => 'Ticket registrado en estado Pendiente por el solicitante.',
                'fecha_registro'     => date('Y-m-d H:i:s', strtotime('-2 hours')),
            ],
            [
                'id_ticket'          => 2,
                'id_usuario_accion'  => 1,
                'accion'             => 'Ticket Asignado',
                'descripcion'        => 'Asignado al técnico Luis Técnico por el administrador.',
                'fecha_registro'     => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
            [
                'id_ticket'          => 3,
                'id_usuario_accion'  => 2,
                'accion'             => 'Solución Aplicada',
                'descripcion'        => 'El técnico marcó el ticket como Resuelto. Solución: Se generó contraseña temporal.',
                'fecha_registro'     => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
        ];
        $db->table('historial_tickets')->ignore(true)->insertBatch($historial);

        // 8. Artículos iniciales de base de conocimientos
        $baseConocimientos = [
            [
                'codigo_solucion'      => 'SOL-101',
                'titulo'               => 'Pantalla negra o sin señal en estación de trabajo',
                'contenido'            => "1. Verificar la conexión del cable de video (HDMI/DisplayPort).\n2. Probar desconectando la fuente de poder por 30 segundos.\n3. Reasentar las tarjetas de memoria RAM si persiste.",
                'id_autor'             => 2, // Luis Técnico
                'id_ticket_referencia' => null,
                'fecha_creacion'       => date('Y-m-d H:i:s', strtotime('-2 days')),
                'fecha_actualizacion'  => date('Y-m-d H:i:s', strtotime('-2 days')),
            ],
            [
                'codigo_solucion'      => 'SOL-102',
                'titulo'               => 'Reinicio y actualización de credenciales ERP',
                'contenido'            => "1. Ingresar a la consola de administración del ERP.\n2. Buscar el usuario afectado y seleccionar 'Reset Password'.\n3. Notificar la contraseña provisoria válida por 24 horas.",
                'id_autor'             => 1, // Carlos Admin
                'id_ticket_referencia' => 3,
                'fecha_creacion'       => date('Y-m-d H:i:s', strtotime('-1 day')),
                'fecha_actualizacion'  => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
        ];
        $db->table('base_conocimientos')->ignore(true)->insertBatch($baseConocimientos);

        // 9. Notificaciones iniciales
        $notificaciones = [
            [
                'id_usuario'           => 1, // Admin
                'id_ticket_referencia' => 1,
                'tipo_alerta'          => 'alerta',
                'mensaje'              => 'Nuevo ticket en cola pendiente: #TK-1001',
                'leida'                => 0,
                'fecha_creacion'       => date('Y-m-d H:i:s', strtotime('-3 hours')),
            ],
            [
                'id_usuario'           => 2, // Técnico
                'id_ticket_referencia' => 2,
                'tipo_alerta'          => 'asignacion',
                'mensaje'              => 'Se te ha asignado el ticket #TK-1002',
                'leida'                => 0,
                'fecha_creacion'       => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
            [
                'id_usuario'           => 3, // Solicitante
                'id_ticket_referencia' => 3,
                'tipo_alerta'          => 'resolucion',
                'mensaje'              => 'Tu ticket #TK-1003 ha sido resuelto. Por favor valida la solución.',
                'leida'                => 0,
                'fecha_creacion'       => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
        ];
        $db->table('notificaciones')->ignore(true)->insertBatch($notificaciones);
    }
}
