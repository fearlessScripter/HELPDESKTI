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
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold" for="id_area_afectada">Área Afectada <span class="text-danger">*</span></label>
                                    <select class="form-select" id="id_area_afectada" name="id_area_afectada" required>
                                        <option value="" selected disabled>Selecciona el área del problema...</option>
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
                                    <input type="text" class="form-control" id="asunto" name="asunto" placeholder="Ej: No me enciende el computador o no tengo internet" required>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold" for="nivel_impacto">Nivel de Impacto (Alcance del problema) <span class="text-danger">*</span></label>
                                        <select class="form-select" id="nivel_impacto" name="nivel_impacto" required>
                                            <option value="" selected disabled>Selecciona el impacto...</option>
                                            <option value="bajo">Bajo (Afecta solo a mí)</option>
                                            <option value="medio">Medio (Afecta a mi departamento)</option>
                                            <option value="alto">Alto (Afecta a toda la empresa)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold" for="nivel_urgencia">Nivel de Urgencia (Rapidez necesaria) <span class="text-danger">*</span></label>
                                        <select class="form-select" id="nivel_urgencia" name="nivel_urgencia" required>
                                            <option value="" selected disabled>Selecciona la urgencia...</option>
                                            <option value="baja">Baja (Puedo seguir trabajando)</option>
                                            <option value="media">Media (Dificulta mi trabajo)</option>
                                            <option value="alta">Alta (Trabajo totalmente detenido)</option>
                                        </select>
                                    </div>
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
