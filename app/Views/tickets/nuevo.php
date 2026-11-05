<?= $this->extend('layout/principal') ?>
<?php
$this->setVar('menuActivo', 'tickets');
$this->setVar('topbar', ['pageTitle' => 'Nuevo Ticket']);
?>

<?= $this->section('titulo') ?>Nuevo Ticket - HelpDesk TI<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

    <!-- ================================================= -->
    <!-- CONTENIDO -->
    <!-- ================================================= -->
    <div class="content">
        <div class="container-fluid p-0">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 mt-2">
                        <div class="card-header bg-white pt-4 pb-3 border-bottom-0 text-center">
                            <h3 class="fw-bold mb-1">Registrar Nuevo Ticket</h3>
                            <p class="text-muted mb-0">Por favor, detalla tu problema para que el equipo técnico pueda ayudarte.</p>
                        </div>
                        <div class="card-body p-4">
                            <form action="<?= base_url('/tickets/crear') ?>" method="post">
                                <?= csrf_field() ?>
                                <!-- identifica este formulario: aunque se envíe varias veces, crea un solo ticket -->
                                <input type="hidden" name="token_envio" value="<?= esc($tokenEnvio ?? '') ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold" for="id_area_afectada">¿Con qué servicio o sistema tienes el problema? <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_area_afectada" name="id_area_afectada" required>
                                        <option value="" selected disabled>Selecciona el servicio o sistema...</option>
                                        <?php if (!empty($areas)): ?>
                                            <?php foreach ($areas as $area): ?>
                                                <option value="<?= esc($area['id_area']) ?>"><?= esc($area['nombre']) ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="1">Soporte TI (Equipos y Hardware)</option>
                                            <option value="2">Redes y Conectividad (Internet/WiFi)</option>
                                            <option value="3">Sistemas ERP (Cuentas y Software)</option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold" for="asunto">Asunto breve <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Ej: No me enciende el computador o no tengo internet" required maxlength="200" aria-describedby="ayudaAsunto">
                                    <div class="form-text" id="ayudaAsunto">Máximo 200 caracteres. Los detalles van en la descripción.</div>
                                </div>

                                <!--
                                    El usuario describe su situación; el sistema calcula la prioridad (TicketModel::calcularPrioridad).
                                    Los valores internos no cambian: dos opciones pueden compartir el mismo valor.
                                -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold" for="nivel_impacto">¿A quiénes está afectando este problema? <span class="text-danger">*</span></label>
                                    <select class="form-select" id="nivel_impacto" name="nivel_impacto" required aria-describedby="ayudaImpacto">
                                        <option value="" selected disabled>Selecciona una opción...</option>
                                        <option value="bajo">Solo a mí</option>
                                        <option value="medio">A varias personas de mi área</option>
                                        <option value="alto">A toda mi área o a varias áreas</option>
                                        <option value="alto">A gran parte de la institución</option>
                                    </select>
                                    <div class="form-text" id="ayudaImpacto">Indica cuántas personas o qué grupo de trabajo tienen el mismo problema.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold" for="nivel_urgencia">¿Cómo afecta este problema a tu trabajo? <span class="text-danger">*</span></label>
                                    <select class="form-select" id="nivel_urgencia" name="nivel_urgencia" required aria-describedby="ayudaUrgencia">
                                        <option value="" selected disabled>Selecciona una opción...</option>
                                        <option value="baja">Puedo seguir trabajando con normalidad</option>
                                        <option value="media">Puedo trabajar, pero con limitaciones o de otra forma</option>
                                        <option value="alta">No puedo realizar una tarea importante y no tengo otra forma de hacerla</option>
                                        <option value="alta">No puedo continuar con mi trabajo</option>
                                    </select>
                                    <div class="form-text" id="ayudaUrgencia">Considera si puedes seguir trabajando y si tienes otra forma de realizar la tarea.</div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold" for="descripcion">Descripción detallada del problema <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="5" placeholder="Describe qué estabas haciendo y qué error apareció en pantalla..." required></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                        <i class="bi bi-send me-1"></i> Enviar Ticket de Soporte
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
