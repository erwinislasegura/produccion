<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['NOMBRE_USUARIO'])) {
    header('Location: ../vista/iniciarSession.php');
    exit;
}

$rootPath = __DIR__ . '/../../';
include_once $rootPath . 'assest/config/validarUsuarioFruta.php';
include_once $rootPath . 'assest/controlador/InspectionMPController.php';

$controller = new InspectionMPController();
$empresaId = $_SESSION['ID_EMPRESA'] ?? null;
$plantaId = $_SESSION['ID_PLANTA'] ?? null;
$temporadaId = $_SESSION['ID_TEMPORADA'] ?? null;
$editingInspectionId = isset($_GET['inspection_id']) ? (int)$_GET['inspection_id'] : 0;
$editingInspection = null;
if ($editingInspectionId > 0) {
    $editingInspection = $controller->getInspectionForEdit($editingInspectionId);
}
$isEditingMode = $editingInspectionId > 0 && !empty($editingInspection);
$includeReceptionId = 0;
if ($editingInspection) {
    $includeReceptionId = (int)($editingInspection['header']['reception_id'] ?? $editingInspection['header']['ID_RECEPCION'] ?? 0);
}
$catalogs = $controller->getCatalogs($empresaId, $plantaId, $temporadaId, $includeReceptionId);
$isEditingLocked = $editingInspection && isset($editingInspection['header']['estado']) && (int)$editingInspection['header']['estado'] === 0;

if (isset($_POST['GUARDAR']) || isset($_POST['GUARDAR_CERRAR'])) {
    $payloadRaw = trim((string)($_POST['payload_json'] ?? ''));
    if ($payloadRaw === '') {
        $payloadRaw = 'null';
    }
    $payload = json_decode($payloadRaw, true);
    try {
        if (!is_array($payload)) {
            $decodeError = ($payloadRaw === 'null')
                ? 'payload vacío (no se construyó desde el formulario)'
                : (function_exists('json_last_error_msg') ? json_last_error_msg() : 'JSON inválido');
            throw new InvalidArgumentException('No se pudo construir el formulario para guardar (' . $decodeError . '). Seleccione recepción y vuelva a intentar.');
        }

        $payload['inspection_id'] = isset($_POST['inspection_id']) ? (int)$_POST['inspection_id'] : 0;
        $payload['close_inspection'] = isset($_POST['GUARDAR_CERRAR']) ? 1 : 0;
        if (!empty($payload['inspection_id'])) {
            $editingDataPost = $controller->getInspectionForEdit((int)$payload['inspection_id']);
            if ($editingDataPost && isset($editingDataPost['header']['estado']) && (int)$editingDataPost['header']['estado'] === 0) {
                throw new InvalidArgumentException('La inspección está cerrada y no permite edición.');
            }
        }

        $requiredFields = ['reception_id', 'producer_id', 'inspector_id', 'inspection_date', 'product', 'total_pallets', 'details'];
        $missing = [];
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field]) || $payload[$field] === '' || $payload[$field] === null) {
                $missing[] = $field;
            }
        }
        if (isset($payload['details']) && is_array($payload['details']) && count($payload['details']) === 0) {
            $missing[] = 'details(sin folios)';
        }
        if (!empty($missing)) {
            throw new InvalidArgumentException('Faltan datos obligatorios: ' . implode(', ', array_unique($missing)) . '.');
        }

        $saved = $controller->saveInspection(
            $payload,
            $IDUSUARIOS,
            $_FILES ?? [],
            [
                'empresa_id' => (int)$empresaId,
                'planta_id' => (int)$plantaId,
                'temporada_id' => (int)$temporadaId,
            ]
        );
        echo '<script>window.__savedInspection=' . json_encode($saved) . ';</script>';
    } catch (Throwable $e) {
        echo '<script>window.__savedInspectionError=' . json_encode($e->getMessage()) . ';</script>';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Inspección MP</title>
    <?php include_once $rootPath . 'assest/config/urlHead.php'; ?>
    <style>
        body.sistemRR { background:#f4f7fb; color:#2f3b4c; }
        .content-header { padding: 8px 0 0; margin-bottom: 4px; }
        .content-header .page-title { font-size:16px; font-weight:600; color:#1f2d3d; margin-bottom:0; }
        .inspection-shell { border:1px solid #d8e1ec; border-radius:10px; box-shadow:0 2px 8px rgba(17,32,51,.04); overflow:hidden; }
        .inspection-shell .box-header { background:#eff3f8; border-bottom:1px solid #dbe3ee; padding:10px 14px; }
        .inspection-shell .box-title { font-size:14px; font-weight:600; color:#2d4057; }
        .inspection-toolbar { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; }
        .inspection-toolbar__meta { color:#6f8197; font-size:12px; }
        .inspection-form-shell { background:#fff; }
        .inspection-form-shell .box-body { padding:12px 14px; }
        .inspection-form-shell .box-footer { background:#f7f9fc; border-top:1px solid #e1e8f2; padding:10px 14px; }
        .inspection-compact .form-group { margin-bottom: .55rem; }
        .inspection-compact .form-control { height: 30px; padding: 4px 8px; font-size: 12px; border-radius: 4px; }
        .inspection-compact label { margin-bottom: .2rem; font-size: 12px; font-weight: 600; }
        .inspection-compact .select2-container .select2-selection--single { height: 30px; border-radius: 6px; }
        .inspection-compact .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 30px; font-size: 12px; }
        .inspection-compact .select2-container--default .select2-selection--single .select2-selection__arrow { height: 30px; }
        .inspection-grid-card { border: 1px solid #dce5ef; border-radius: 10px; padding: 12px; background: linear-gradient(180deg,#fff 0%,#fbfcfe 100%); margin-bottom: 10px; box-shadow:0 1px 4px rgba(25,42,65,.04); }
        .section-title { font-size: 13px; font-weight: 700; color: #2d4057; margin-bottom: 8px; border-left: 3px solid #5a67d8; padding-left: 8px; }
        .inspection-chip { display:inline-block; padding:1px 7px; border-radius: 12px; background:#f3f4f6; color:#4b5563; font-size:10px; margin-left:6px; font-weight:600; }
        .readonly-field { background-color: #f4f6f9 !important; border-color: #d2d6de !important; }
        .producer-card { border: 1px solid #dde5ef; border-radius: 8px; background: #f6f8fb; padding: 10px; }
        .producer-grid { display: grid; grid-template-columns: repeat(4,minmax(130px,1fr)); gap: 6px 10px; }
        .producer-item { background: transparent; border: 0; padding: 1px 0; min-height: auto; }
        .producer-item small { display:block; color:#6b7d93; font-size:10px; text-transform: uppercase; font-weight: 700; letter-spacing: .15px; margin-bottom: 1px; }
        .producer-item b { font-size:12px; color:#2b3948; word-break: break-word; font-weight: 600; }
        #folios_defects_table th, #folios_defects_table td { padding: .3rem; vertical-align: middle; font-size: 12px; }
        #folios_defects_table .form-control, #folios_defects_table .btn { height: 28px; padding: 2px 6px; font-size: 11px; }
        #folios_defects_table thead th { background:#fafafa; color:#374151; border-color:#e5e7eb; }
        #folios_defects_table tbody tr:nth-child(even) { background:#fcfcfd; }
        .photo-count-badge { min-width: 16px; text-align: center; display: inline-block; background: #6b7280; color: #fff; border-radius: 20px; padding: 0 5px; font-size: 10px; margin-left: 4px; }
        .photo-slot-card { border:1px solid #e5e7eb; border-radius:6px; background:#fff; padding:8px; margin-bottom:8px; }
        .photo-slot-title { font-size:11px; font-weight:700; color:#4b5563; margin-bottom:5px; }
        .modal-photo-header, .modal-quality-header { background: #eff3f8; border-bottom:1px solid #dbe3ee; color:#2d4057; }
        .modal-photo-body { max-height: 62vh; overflow-y: auto; background:#f8fafc; }
        .btn-photo-square { border-radius: 4px !important; padding: 3px 7px !important; border-color:#d1d5db !important; color:#374151 !important; background:#fff !important; }
        .photo-line-remove { height: 30px !important; }
        .folio-detail-body { max-height: 68vh; overflow-y: auto; background:#f8fafc; }
        .folio-detail-grid { display: grid; grid-template-columns: repeat(2,minmax(220px,1fr)); gap: 10px; }
        .folio-detail-card { border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px; background: #fff; }
        .folio-detail-card h6 { font-size: 12px; font-weight: 700; margin-bottom: 8px; color: #1f2937; }
        .defect-grid { display: grid; grid-template-columns: repeat(2,minmax(130px,1fr)); gap: 8px; }
        .quality-summary .alert { border:0; border-radius:9px; box-shadow:0 1px 4px rgba(25,42,65,.04); }
        .modal-content { border:0; border-radius:10px; box-shadow:0 10px 30px rgba(20,35,55,.18); overflow:hidden; }
        .modal-footer { background:#f7f9fc; border-top:1px solid #e1e8f2; }
        @media (max-width: 992px) {
            .producer-grid { grid-template-columns: repeat(2,minmax(130px,1fr)); }
            .folio-detail-grid { grid-template-columns: 1fr; }
            .defect-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="hold-transition light-skin fixed sidebar-mini theme-primary sistemRR">
<div class="wrapper">
    <?php include_once $rootPath . 'assest/config/menuCalidad.php'; ?>
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="mr-auto"><h3 class="page-title">Dashboard · Inspección MP</h3></div>
                    <?php include_once $rootPath . 'assest/config/verIndicadorEconomico.php'; ?>
                </div>
            </div>
            <section class="content">
                <div class="box inspection-shell">
                    <div class="box-header with-border">
                        <div class="inspection-toolbar">
                            <h4 class="box-title mb-0">Formulario Inspección MP</h4>
                            <span class="inspection-toolbar__meta">Cabecera · recepción · productor · defectos · fotografías</span>
                        </div>
                    </div>
                    <form data-form-layout="oneline-1" method="post" id="inspection-form" class="inspection-compact form-one-line inspection-form-shell" enctype="multipart/form-data">
                        <input type="hidden" name="payload_json" id="payload_json" />
                        <input type="hidden" name="inspection_id" id="inspection_id" value="<?php echo $editingInspectionId > 0 ? (int)$editingInspectionId : ''; ?>" />
                        <input type="hidden" name="save_mode" id="save_mode" value="guardar" />
                        <div class="box-body">
                            <div class="inspection-grid-card">
                                <div class="row">
                                    <div class="col-12"><div class="section-title">1) Cabecera de inspección</div></div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Recepción MP <span class="inspection-chip">Origen</span></label>
                                            <select class="form-control select2" id="reception_id" <?php echo $isEditingMode ? 'disabled' : ''; ?>></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Inspector</label>
                                            <select class="form-control select2" id="inspector_id" <?php echo $isEditingMode ? 'disabled' : ''; ?>></select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Fecha inspección</label>
                                        <input type="date" class="form-control" id="inspection_date" value="<?php echo date('Y-m-d'); ?>" <?php echo $isEditingMode ? 'readonly' : ''; ?>>
                                    </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Manejo general</label>
                                        <select class="form-control" id="is_organic" <?php echo $isEditingMode ? 'disabled' : ''; ?>>
                                            <option value="0">CONV</option>
                                            <option value="1">ORG</option>
                                        </select>
                                    </div>
                                    </div>
                                    <div class="col-md-3">
                                    <div class="form-group">
                                        <label>N° pallets total</label>
                                        <input type="number" min="1" class="form-control readonly-field" id="total_pallets" value="1" readonly>
                                    </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>CSG</label>
                                            <input type="text" class="form-control readonly-field" id="csg" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>N° inspección generado</label>
                                            <input type="text" class="form-control readonly-field" id="inspection_number_generated" value="" placeholder="Se asigna al guardar" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" id="producer_id">
                            <input type="hidden" id="product_id">
                            <input type="hidden" id="variety_id" value="">
                            <span id="rx_producer" class="d-none"></span>
                            <span id="rx_varieties" class="d-none"></span>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="inspection-grid-card" style="height:100%;">
                                        <div class="d-flex justify-content-between align-items-center mb-8">
                                            <div class="section-title mb-0">2) Datos de la recepción</div>
                                        </div>
                                        <div class="producer-card">
                                            <div class="producer-grid">
                                                <div class="producer-item"><small>N° Recepción</small><b id="rx_number">-</b></div>
                                                <div class="producer-item"><small>Fecha</small><b id="rx_date">-</b></div>
                                                <div class="producer-item"><small>Hora</small><b id="rx_hour">-</b></div>
                                                <div class="producer-item"><small>N° Guía</small><b id="rx_guide">-</b></div>
                                                <div class="producer-item"><small>Kilos netos</small><b id="rx_net">0</b></div>
                                                <div class="producer-item"><small>Envases</small><b id="rx_packages">0</b></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="inspection-grid-card" style="height:100%;">
                                        <div class="d-flex justify-content-between align-items-center mb-8">
                                            <div class="section-title mb-0">3) Productor</div>
                                            <span class="inspection-chip">Referencia</span>
                                        </div>
                                        <div class="producer-card">
                                            <div id="producer_details" class="producer-grid">
                                                <div class="producer-item">
                                                    <small>Productor</small>
                                                    <b>Sin datos de productor para la recepción seleccionada.</b>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="section-title">4) Detalle de inspección (defectos)</div>
                                    <div class="mb-10">
                                        <label>Detalle por tandas (folios de recepción)</label>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered" id="folios_defects_table">
                                                <thead></thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-muted text-center">Seleccione una recepción con folios para completar los defectos.</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-10 quality-summary">
                                <div class="col-md-3"><div class="alert alert-info py-10 mb-10">Total defectos mayores: <b id="total_major">0.00</b></div></div>
                                <div class="col-md-3"><div class="alert alert-warning py-10 mb-10">Total defectos menores: <b id="total_minor">0.00</b></div></div>
                                <div class="col-md-3"><div class="alert alert-danger py-10 mb-10">Total defectos críticos: <b id="total_critical">0.00</b></div></div>
                                <div class="col-md-3"><div class="alert alert-success py-10 mb-10">Score final: <b id="final_score">100.00</b> · Categoría: <b id="final_category">A</b></div></div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-success" name="GUARDAR" value="GUARDAR" <?php echo $isEditingLocked ? 'disabled' : ''; ?>>Guardar</button>
                            <button type="submit" class="btn btn-danger" name="GUARDAR_CERRAR" value="GUARDAR_CERRAR" <?php echo $isEditingLocked ? 'disabled' : ''; ?>>Guardar y Cerrar</button>
                            <a class="btn btn-secondary" href="import.php">Importar Excel</a>
                            <?php if ($isEditingLocked) : ?>
                                <span class="text-danger ml-10"><i class="ti-lock"></i> Inspección cerrada: edición bloqueada.</span>
                            <?php endif; ?>
                        </div>
                    </form>

                    <div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header modal-photo-header">
                                    <h5 class="modal-title">Fotografías por folio (máximo 5) · <span id="photo_modal_folio">-</span></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body modal-photo-body">
                                    <p class="text-muted mb-8" style="font-size:12px;">
                                        Cargue hasta <b>5 fotos</b> por folio. Cada foto puede llevar nombre y comentario.
                                    </p>
                                    <div id="photo_modal_dynamic"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="btn_add_photo_line">Agregar línea</button>
                                    <button type="button" class="btn btn-dark btn-sm" id="btn_save_photos">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="folioDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-xl" role="document">
                            <div class="modal-content">
                                <div class="modal-header modal-quality-header">
                                    <h5 class="modal-title">Detalle de inspección del folio · <span id="folio_detail_modal_folio">-</span></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body folio-detail-body">
                                    <div id="folio_detail_modal_dynamic"></div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-dark btn-sm" id="btn_save_folio_detail">Guardar detalle</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php include_once $rootPath . 'assest/config/footer.php'; ?>
    <?php include_once $rootPath . 'assest/config/menuExtraFruta.php'; ?>
</div>
<?php include_once $rootPath . 'assest/config/urlBase.php'; ?>
<script>
const catalogs = <?php echo json_encode($catalogs); ?>;
const editingInspection = <?php echo json_encode($editingInspection); ?>;
const defects = catalogs.defects || [];
const receptionsMap = new Map((catalogs.receptions || []).map(r => [String(r.id), r]));
const foliosByReception = catalogs.folios_by_reception || {};
const defaultInspectionDate = (document.getElementById('inspection_date') && document.getElementById('inspection_date').value) || '';

function fillSelect(id, rows, placeholder = 'Seleccione', getLabel = (row) => row.name) {
    const el = document.getElementById(id);
    el.innerHTML = `<option value="">${placeholder}</option>`;
    (rows || []).forEach((row) => {
        const o = document.createElement('option');
        o.value = row.id;
        o.textContent = getLabel(row);
        el.appendChild(o);
    });
}

function fillReceptionSelect() {
    fillSelect(
        'reception_id',
        catalogs.receptions || [],
        'Seleccione recepción',
        (r) => `${r.reception_number || r.id} · ${r.reception_date || 'Sin fecha'} · ${r.producer_name || 'Sin productor'}`
    );

    if (editingInspection && editingInspection.header) {
        const header = editingInspection.header;
        const editReceptionId = String(header.reception_id || header.ID_RECEPCION || '').trim();
        if (editReceptionId && !Array.from(document.getElementById('reception_id').options).some((opt) => String(opt.value) === editReceptionId)) {
            const option = document.createElement('option');
            option.value = editReceptionId;
            option.textContent = `${header.reception_number || editReceptionId} · ${header.inspection_date || 'Sin fecha'} · ${header.producer_name || 'Recepción edición'}`;
            document.getElementById('reception_id').appendChild(option);
        }
    }
}

function normalizedType(typeRaw) {
    const t = String(typeRaw || '').toLowerCase();
    if (t.includes('crit')) return 'critical';
    if (t.includes('may') || t === 'major') return 'major';
    return 'minor';
}

function defectTypeLabel(typeRaw) {
    const type = normalizedType(typeRaw);
    if (type === 'critical') return 'Crítico';
    if (type === 'major') return 'Mayor';
    return 'Menor';
}

function defectTypeBadgeClass(typeRaw) {
    const type = normalizedType(typeRaw);
    if (type === 'critical') return 'badge-danger';
    if (type === 'major') return 'badge-warning';
    return 'badge-info';
}

function normalizedScope(scopeRaw) {
    const s = String(scopeRaw || '').toLowerCase();
    if (s.includes('cond')) return 'condition';
    return 'quality';
}

function escapeHtml(value) {
    return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function buildPhotoSlots(rowIndex) {
    const slots = [];
    for (let i = 0; i < 5; i += 1) {
        const slot = i + 1;
        slots.push(`
            <div class="photo-slot-card photo-line ${i === 0 ? '' : 'd-none'}" data-slot="${i}">
                <div class="d-flex justify-content-between align-items-center mb-5">
                    <div class="photo-slot-title">Fotografía ${slot}</div>
                    ${i === 0 ? '' : '<button type="button" class="btn btn-danger btn-xs photo-line-remove">Eliminar</button>'}
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Nombre</label>
                        <input type="text" class="form-control photo-name" data-slot="${i}" placeholder="Ej: Daño en caja">
                    </div>
                    <div class="col-md-5">
                        <label>Comentario</label>
                        <input type="text" class="form-control photo-comment" data-slot="${i}" placeholder="Detalle observado">
                    </div>
                    <div class="col-md-3">
                        <label>Archivo</label>
                        <input type="file" class="form-control photo-file" data-slot="${i}" name="photo_${rowIndex}_${i}" accept=\"image/*\">
                    </div>
                </div>
            </div>
        `);
    }
    return slots.join('');
}

function renderProducerDetails(rx) {
    const detailsNode = document.getElementById('producer_details');
    const producerMap = catalogs.producer_details_map || {};
    const producer = rx && rx.producer_id ? producerMap[String(rx.producer_id)] : null;
    if (!producer) {
        detailsNode.innerHTML = '<div class="producer-item"><small>Productor</small><b>Sin datos para la recepción seleccionada.</b></div>';
        return;
    }
    const fields = [
        { label: 'CSG', keys: ['CSG_PRODUCTOR', 'CSG'] },
        { label: 'Nombre', keys: ['NOMBRE_PRODUCTOR', 'NOMBRE', 'RAZON_SOCIAL_PRODUCTOR'] },
        { label: 'RUT', keys: ['RUT_PRODUCTOR', 'RUT'] },
        { label: 'Giro', keys: ['GIRO_PRODUCTOR', 'GIRO'] },
        { label: 'Comuna', keys: ['COMUNA_PRODUCTOR', 'COMUNA'] },
        { label: 'Ciudad', keys: ['CIUDAD_PRODUCTOR', 'CIUDAD'] },
        { label: 'Comuna Provincia', keys: ['COMUNA_PROVINCIA_PRODUCTOR', 'COMUNA_PROVINCIA'] },
        { label: 'GGN', keys: ['GGN_PRODUCTOR', 'GGN'] }
    ];
    const html = fields
        .map((field) => {
            const value = field.keys.map((key) => producer[key]).find((v) => v !== undefined && v !== null && String(v).trim() !== '');
            return `<div class="producer-item"><small>${escapeHtml(field.label)}</small><b>${escapeHtml(value ?? '-')}</b></div>`;
        })
        .join('');
    detailsNode.innerHTML = html || '<div class="producer-item"><small>Productor</small><b>Sin información adicional.</b></div>';
}

function buildDefectGroups() {
    const groups = { condition: [], quality: [] };
    defects.forEach((defect) => {
        const scope = normalizedScope(defect.defect_scope);
        groups[scope].push(defect);
    });
    return groups;
}

function buildVarietyOptions(selectedVarietyName = '') {
    const normalizedSelected = normalizeText(selectedVarietyName);
    const options = (catalogs.varieties || []).map((v) => {
        const selected = normalizeText(v.name) === normalizedSelected ? 'selected' : '';
        return `<option value="${v.id}" ${selected}>${escapeHtml(v.name)}</option>`;
    });
    const hasSelected = options.some((html) => html.indexOf('selected') !== -1);
    if (!hasSelected && selectedVarietyName) {
        options.unshift(`<option value="" selected>${escapeHtml(selectedVarietyName)}</option>`);
    }
    return options.join('');
}

function normalizeText(value) {
    return String(value || '')
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim();
}

function renderFoliosDefectsTable() {
    const rid = document.getElementById('reception_id').value;
    const table = document.getElementById('folios_defects_table');
    const thead = table.querySelector('thead');
    const tbody = table.querySelector('tbody');
    const folios = foliosByReception[String(rid)] || [];
    const groups = buildDefectGroups();
    const conditionDefects = groups.condition;
    const qualityDefects = groups.quality;

    if (!folios.length) {
        thead.innerHTML = '';
        const message = rid
            ? 'Sin folios activos para la recepción seleccionada.'
            : 'Seleccione una recepción para cargar folios y datos dependientes.';
        tbody.innerHTML = `<tr><td class="text-muted text-center">${message}</td></tr>`;
        document.getElementById('total_pallets').value = 1;
        return;
    }

    const baseHeader = `
        <tr class="bg-light">
            <th style="min-width: 180px;">Folio final</th>
            <th style="min-width: 100px;">Pallet</th>
            <th style="min-width: 100px;">Muestra</th>
            <th style="min-width: 150px;">Variedad</th>
            <th style="min-width: 110px;">Suma Mayor</th>
            <th style="min-width: 140px;">Acción</th>
        </tr>
    `;
    thead.innerHTML = baseHeader;

    tbody.innerHTML = folios.map((folio, rowIndex) => {
        const folioFinal = folio.folio_final || (folio.alias ? `${folio.alias} · ${folio.folio}` : `Folio ${folio.folio}`);
        const varietyOptions = buildVarietyOptions(folio.variety_name || '');

        const detailSection = `
            <div class="folio-detail-grid">
                <div class="folio-detail-card">
                    <h6>Datos base del folio</h6>
                    <div class="form-group mb-8"><label>Folio final</label><input type="text" class="form-control readonly-field" value="${escapeHtml(folioFinal)}" readonly></div>
                    <div class="form-group mb-8"><label>Pallet</label><input type="text" class="form-control row-pallet" value="${rowIndex + 1}"></div>
                    <div class="form-group mb-8">
                        <label>Manejo</label>
                        <select class="form-control row-handling">
                            <option value="CONV">CONV</option>
                            <option value="ORG">ORG</option>
                        </select>
                    </div>
                    <div class="form-group mb-8"><label>Muestra</label><input type="text" class="form-control row-sample readonly-field" value="${escapeHtml(folio.sample_size || 0)}" readonly></div>
                    <div class="form-group mb-8">
                        <label>Variedad</label>
                        <select class="form-control row-variety readonly-field" disabled>${varietyOptions}</select>
                    </div>
                </div>
                <div class="folio-detail-card">
                    <h6>Mediciones</h6>
                    <div class="form-group mb-8"><label>Temperatura</label><input type="number" min="0" step="0.01" class="form-control row-temperature" value="0"></div>
                    <div class="form-group mb-8"><label>Brix</label><input type="number" min="0" step="0.01" class="form-control row-brix" value="0"></div>
                    <div class="form-group mb-8"><label>Baxlo</label><input type="number" min="0" step="0.01" class="form-control row-baxlo" value="0"></div>
                    <div class="form-group mb-8"><label>Baxlo promedio</label><input type="number" min="0" step="0.01" class="form-control row-baxlo-avg" value="0"></div>
                    <div class="form-group mb-0"><label>Suma Mayor (referencial)</label><input type="number" min="0" step="0.01" class="form-control row-major-sum-modal" value="0" readonly></div>
                </div>
                <div class="folio-detail-card">
                    <h6>Defectos de condición</h6>
                    <div class="defect-grid">
                        ${conditionDefects.length ? conditionDefects.map((defect) => `
                            <div>
                                <label>${escapeHtml(defect.name)} <span class="badge ${defectTypeBadgeClass(defect.defect_type)}">${defectTypeLabel(defect.defect_type)}</span></label>
                                <input type="number" min="0" step="0.01" class="form-control defect-value defect-value-grid" data-row="${rowIndex}" data-defect-id="${defect.id}" data-defect-type="${normalizedType(defect.defect_type)}" value="0">
                            </div>
                        `).join('') : '<div class="text-muted">Sin defectos de condición.</div>'}
                    </div>
                </div>
                <div class="folio-detail-card">
                    <h6>Defectos de calidad y fotografías</h6>
                    <div class="defect-grid mb-10">
                        ${qualityDefects.length ? qualityDefects.map((defect) => `
                            <div>
                                <label>${escapeHtml(defect.name)} <span class="badge ${defectTypeBadgeClass(defect.defect_type)}">${defectTypeLabel(defect.defect_type)}</span></label>
                                <input type="number" min="0" step="0.01" class="form-control defect-value defect-value-grid" data-row="${rowIndex}" data-defect-id="${defect.id}" data-defect-type="${normalizedType(defect.defect_type)}" value="0">
                            </div>
                        `).join('') : '<div class="text-muted">Sin defectos de calidad.</div>'}
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-xs btn-photo-modal btn-photo-square" data-row="${rowIndex}" data-folio="${escapeHtml(folioFinal)}">
                        📷 Fotografías <span class="photo-count-badge" id="photo_count_${rowIndex}">0</span>
                    </button>
                    <input type="hidden" class="row-photo-json" value="[]">
                    <input type="hidden" class="row-photo-lines" value="1">
                    <div id="photo_payload_host_${rowIndex}">
                        <div class="row-photo-payload d-none" id="photo_payload_${rowIndex}">
                            ${buildPhotoSlots(rowIndex)}
                        </div>
                    </div>
                </div>
            </div>
        `;

        return `
            <tr>
                <td>${escapeHtml(folioFinal)}<input type="hidden" class="row-folio-id" value="${escapeHtml(folio.folio_id || '')}"></td>
                <td>${rowIndex + 1}</td>
                <td>${escapeHtml(folio.sample_size || 0)}</td>
                <td>${escapeHtml(folio.variety_name || '-')}</td>
                <td><input type="number" min="0" step="0.01" class="form-control row-major-sum" value="0" readonly></td>
                <td>
                    <button type="button" class="btn btn-primary btn-sm btn-folio-detail-modal" data-row="${rowIndex}" data-folio="${escapeHtml(folioFinal)}">
                        Ver / editar detalle
                    </button>
                    <div id="row_detail_home_${rowIndex}">
                        <div class="row-detail-payload d-none" id="row_detail_payload_${rowIndex}">
                            ${detailSection}
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    document.getElementById('total_pallets').value = folios.length;

    document.querySelectorAll('.defect-value-grid').forEach((input) => {
        input.addEventListener('input', calculateScore);
        input.addEventListener('input', calculateMajorByRow);
    });
    document.querySelectorAll('.btn-photo-modal').forEach((button) => {
        button.addEventListener('click', () => openPhotoModal(parseInt(button.dataset.row, 10)));
    });
    document.querySelectorAll('.btn-folio-detail-modal').forEach((button) => {
        button.addEventListener('click', () => openFolioDetailModal(parseInt(button.dataset.row, 10)));
    });
    calculateMajorByRow();
    calculateScore();
}

let activePhotoRow = null;
let activePhotoFolio = '-';
let activeFolioDetailRow = null;

function getPhotoLinesVisibleCount(rowIndex) {
    const row = document.querySelectorAll('#folios_defects_table tbody tr')[rowIndex];
    const input = row ? row.querySelector('.row-photo-lines') : null;
    return Math.max(1, parseInt((input && input.value) || '1', 10));
}

function setPhotoLinesVisibleCount(rowIndex, count) {
    const row = document.querySelectorAll('#folios_defects_table tbody tr')[rowIndex];
    const input = row ? row.querySelector('.row-photo-lines') : null;
    const normalized = Math.min(5, Math.max(1, parseInt(count || 1, 10)));
    if (input) {
        input.value = String(normalized);
    }
    const payload = document.getElementById(`photo_payload_${rowIndex}`);
    if (!payload) return;
    payload.querySelectorAll('.photo-line').forEach((line, idx) => {
        if (idx < normalized) line.classList.remove('d-none');
        else line.classList.add('d-none');
    });
}

function syncRowPhotoMeta(rowIndex) {
    const payload = document.getElementById(`photo_payload_${rowIndex}`);
    if (!payload) return;
    const records = [];
    payload.querySelectorAll('.photo-line').forEach((item, slotIndex) => {
        if (item.classList.contains('d-none')) return;
        const nameInput = item.querySelector('.photo-name');
        const commentInput = item.querySelector('.photo-comment');
        const name = (nameInput && String(nameInput.value || '').trim()) || '';
        const comment = (commentInput && String(commentInput.value || '').trim()) || '';
        const fileInput = item.querySelector('.photo-file');
        const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
        if (name || comment || hasFile) {
            records.push({
                slot: slotIndex,
                name,
                comment,
                file_input: `photo_${rowIndex}_${slotIndex}`
            });
        }
    });
    const row = document.querySelectorAll('#folios_defects_table tbody tr')[rowIndex];
    const hiddenField = row ? row.querySelector('.row-photo-json') : null;
    if (hiddenField) {
        hiddenField.value = JSON.stringify(records.slice(0, 5));
    }
    const countNode = document.getElementById(`photo_count_${rowIndex}`);
    if (countNode) {
        countNode.textContent = String(records.length);
    }
}

function openPhotoModal(rowIndex) {
    const payload = document.getElementById(`photo_payload_${rowIndex}`);
    if (!payload) return;
    const button = document.querySelector(`.btn-photo-modal[data-row="${rowIndex}"]`);
    activePhotoFolio = (button && button.dataset && button.dataset.folio) ? button.dataset.folio : '-';
    document.getElementById('photo_modal_folio').textContent = activePhotoFolio;
    const dynamicHost = document.getElementById('photo_modal_dynamic');
    dynamicHost.innerHTML = '';
    payload.classList.remove('d-none');
    dynamicHost.appendChild(payload);
    activePhotoRow = rowIndex;
    setPhotoLinesVisibleCount(rowIndex, getPhotoLinesVisibleCount(rowIndex));
    if (window.jQuery) {
        window.jQuery('#photoModal').modal('show');
    } else {
        document.getElementById('photoModal').style.display = 'block';
    }
}

document.getElementById('btn_add_photo_line').addEventListener('click', function () {
    if (activePhotoRow === null) return;
    const current = getPhotoLinesVisibleCount(activePhotoRow);
    if (current < 5) {
        setPhotoLinesVisibleCount(activePhotoRow, current + 1);
    }
});

document.getElementById('btn_save_photos').addEventListener('click', function () {
    if (activePhotoRow === null) return;
    syncRowPhotoMeta(activePhotoRow);
    if (window.jQuery) {
        window.jQuery('#photoModal').modal('hide');
    } else {
        document.getElementById('photoModal').style.display = 'none';
    }
});

document.getElementById('photo_modal_dynamic').addEventListener('click', function (event) {
    const button = event.target.closest('.photo-line-remove');
    if (!button || activePhotoRow === null) return;
    const line = button.closest('.photo-line');
    if (!line) return;
    line.querySelectorAll('input').forEach((input) => {
        if (input.type === 'file') input.value = '';
        else input.value = '';
    });
    line.classList.add('d-none');
    const visibleLines = document.querySelectorAll('#photo_modal_dynamic .photo-line:not(.d-none)').length || 1;
    setPhotoLinesVisibleCount(activePhotoRow, visibleLines);
    syncRowPhotoMeta(activePhotoRow);
});

function closePhotoModalCleanup() {
    if (activePhotoRow === null) return;
    const payload = document.getElementById(`photo_payload_${activePhotoRow}`);
    const home = document.getElementById(`photo_payload_host_${activePhotoRow}`);
    if (payload && home) {
        payload.classList.add('d-none');
        home.appendChild(payload);
        syncRowPhotoMeta(activePhotoRow);
    }
    activePhotoRow = null;
    activePhotoFolio = '-';
    document.getElementById('photo_modal_folio').textContent = '-';
}

if (window.jQuery) {
    window.jQuery('#photoModal').on('hidden.bs.modal', closePhotoModalCleanup);
} else {
    const modalCloseButton = document.querySelector('#photoModal [data-dismiss=\"modal\"]');
    if (modalCloseButton) {
        modalCloseButton.addEventListener('click', function () {
            document.getElementById('photoModal').style.display = 'none';
            closePhotoModalCleanup();
        });
    }
}

function openFolioDetailModal(rowIndex) {
    const payload = document.getElementById(`row_detail_payload_${rowIndex}`);
    if (!payload) return;
    const button = document.querySelector(`.btn-folio-detail-modal[data-row="${rowIndex}"]`);
    const folioLabel = (button && button.dataset && button.dataset.folio) ? button.dataset.folio : '-';
    document.getElementById('folio_detail_modal_folio').textContent = folioLabel;
    const dynamicHost = document.getElementById('folio_detail_modal_dynamic');
    dynamicHost.innerHTML = '';
    payload.classList.remove('d-none');
    dynamicHost.appendChild(payload);
    activeFolioDetailRow = rowIndex;
    if (window.jQuery) {
        window.jQuery('#folioDetailModal').modal('show');
    } else {
        document.getElementById('folioDetailModal').style.display = 'block';
    }
}

function closeFolioDetailModalCleanup() {
    if (activeFolioDetailRow === null) return;
    const payload = document.getElementById(`row_detail_payload_${activeFolioDetailRow}`);
    const home = document.getElementById(`row_detail_home_${activeFolioDetailRow}`);
    if (payload && home) {
        payload.classList.add('d-none');
        home.appendChild(payload);
    }
    activeFolioDetailRow = null;
    document.getElementById('folio_detail_modal_folio').textContent = '-';
}

document.getElementById('btn_save_folio_detail').addEventListener('click', function () {
    if (window.jQuery) {
        window.jQuery('#folioDetailModal').modal('hide');
    } else {
        document.getElementById('folioDetailModal').style.display = 'none';
        closeFolioDetailModalCleanup();
    }
    calculateMajorByRow();
    calculateScore();
});

if (window.jQuery) {
    window.jQuery('#folioDetailModal').on('hidden.bs.modal', closeFolioDetailModalCleanup);
} else {
    const detailModalCloseButton = document.querySelector('#folioDetailModal [data-dismiss=\"modal\"]');
    if (detailModalCloseButton) {
        detailModalCloseButton.addEventListener('click', function () {
            document.getElementById('folioDetailModal').style.display = 'none';
            closeFolioDetailModalCleanup();
        });
    }
}

function updateReceptionInfo() {
    const rid = String(document.getElementById('reception_id').value || '').trim();
    const rx = receptionsMap.get(rid) || (catalogs.receptions || []).find((r) => String(r.id) === rid) || null;

    document.getElementById('rx_number').textContent = rx ? (rx.reception_number || rx.id || '-') : '-';
    document.getElementById('rx_date').textContent = rx ? (rx.reception_date || '-') : '-';
    document.getElementById('rx_hour').textContent = rx ? (rx.reception_hour || '-') : '-';
    document.getElementById('rx_guide').textContent = rx ? (rx.guide_number || '-') : '-';
    document.getElementById('rx_net').textContent = rx ? (rx.net_kilos || 0) : 0;
    document.getElementById('rx_packages').textContent = rx ? (rx.package_count || 0) : 0;
    document.getElementById('rx_producer').textContent = rx ? (rx.producer_name || 'Sin datos') : 'Sin datos';
    document.getElementById('rx_varieties').textContent = rx ? (rx.varieties_label || 'Sin datos') : 'Sin datos';

    document.getElementById('producer_id').value = (rx && rx.producer_id) ? String(rx.producer_id) : '';
    document.getElementById('inspection_date').value = (rx && rx.reception_date) ? rx.reception_date : defaultInspectionDate;
    document.getElementById('csg').value = rx ? (rx.reception_number || rx.id || '') : '';
    document.getElementById('inspection_number_generated').value = '';

    renderProducerDetails(rx);

    renderFoliosDefectsTable();

    document.getElementById('product_id').value = '';
    document.getElementById('variety_id').value = '';
}

function applyEditingInspection() {
    if (!editingInspection || !editingInspection.header) {
        return;
    }
    const header = editingInspection.header;
    const details = editingInspection.details || [];
    const receptionId = String(header.reception_id || header.ID_RECEPCION || '').trim();

    document.getElementById('inspection_id').value = String(header.id || '');
    document.getElementById('reception_id').value = receptionId;
    if (window.jQuery) {
        window.jQuery('#reception_id').trigger('change');
    } else {
        updateReceptionInfo();
    }

    setTimeout(() => {
        document.getElementById('inspector_id').value = String(header.inspector_id || '');
        document.getElementById('inspection_date').value = String(header.inspection_date || defaultInspectionDate);
        document.getElementById('is_organic').value = String(parseInt(header.is_organic || 0, 10));
        if (header.inspection_number) {
            document.getElementById('inspection_number_generated').value = String(header.inspection_number);
        }

        const rowNodes = Array.from(document.querySelectorAll('#folios_defects_table tbody tr')).filter((row) => row.querySelector('.row-pallet'));
        const detailByFolio = new Map(details.map((detail) => [String(detail.source_folio_id || ''), detail]));

        rowNodes.forEach((row, idx) => {
            const folioId = String((row.querySelector('.row-folio-id') && row.querySelector('.row-folio-id').value) || '');
            const detail = detailByFolio.get(folioId) || details[idx] || null;
            if (!detail) {
                return;
            }
            if (row.querySelector('.row-pallet')) row.querySelector('.row-pallet').value = detail.pallet_number || (idx + 1);
            if (row.querySelector('.row-sample')) row.querySelector('.row-sample').value = detail.sample || '';
            if (row.querySelector('.row-handling')) row.querySelector('.row-handling').value = detail.handling_type || 'CONV';
            if (row.querySelector('.row-variety') && detail.variety_id) row.querySelector('.row-variety').value = String(detail.variety_id);
            if (row.querySelector('.row-temperature')) row.querySelector('.row-temperature').value = parseFloat((detail.measurement || {}).temperature || 0);
            if (row.querySelector('.row-brix')) row.querySelector('.row-brix').value = parseFloat((detail.measurement || {}).brix || 0);
            if (row.querySelector('.row-baxlo')) row.querySelector('.row-baxlo').value = parseFloat((detail.measurement || {}).baxlo || 0);
            if (row.querySelector('.row-baxlo-avg')) row.querySelector('.row-baxlo-avg').value = parseFloat((detail.measurement || {}).average || 0);

            row.querySelectorAll('.defect-value-grid').forEach((input) => {
                const defectId = String(input.dataset.defectId || '');
                if (detail.defects && Object.prototype.hasOwnProperty.call(detail.defects, defectId)) {
                    input.value = parseFloat(detail.defects[defectId] || 0);
                }
            });

            if (detail.calibers) {
                row.querySelectorAll('.row-caliber').forEach((input) => {
                    const caliberId = String(input.dataset.caliberId || '');
                    if (Object.prototype.hasOwnProperty.call(detail.calibers, caliberId)) {
                        input.value = parseFloat(detail.calibers[caliberId] || 0);
                    }
                });
            }

            if (Array.isArray(detail.photos)) {
                const photoPayload = document.getElementById(`photo_payload_${idx}`);
                if (photoPayload) {
                    detail.photos.slice(0, 5).forEach((photo, photoIdx) => {
                        const line = photoPayload.querySelector(`.photo-line[data-slot="${photoIdx}"]`);
                        if (!line) return;
                        line.classList.remove('d-none');
                        const nameInput = line.querySelector('.photo-name');
                        const commentInput = line.querySelector('.photo-comment');
                        if (nameInput) nameInput.value = photo.name || '';
                        if (commentInput) commentInput.value = photo.comment || '';
                    });
                    setPhotoLinesVisibleCount(idx, Math.max(1, detail.photos.length));
                    syncRowPhotoMeta(idx);
                }
            }
        });

        calculateMajorByRow();
        calculateScore();
    }, 120);
}

function calculateScore() {
    let totalCritical = 0;
    let totalMajor = 0;
    let totalMinor = 0;

    document.querySelectorAll('.defect-value').forEach((input) => {
        const value = parseFloat(input.value || 0);
        const type = input.dataset.defectType;
        if (type === 'critical') totalCritical += value;
        else if (type === 'major') totalMajor += value;
        else totalMinor += value;
    });

    const score = Math.max(0, 100 - (totalCritical * 3) - (totalMajor * 1.5) - (totalMinor * 0.75));
    let category = 'C';
    if (score >= 90) category = 'A';
    else if (score >= 75) category = 'B';

    document.getElementById('total_critical').textContent = totalCritical.toFixed(2);
    document.getElementById('total_major').textContent = totalMajor.toFixed(2);
    document.getElementById('total_minor').textContent = totalMinor.toFixed(2);
    document.getElementById('final_score').textContent = score.toFixed(2);
    document.getElementById('final_category').textContent = category;
}

function calculateMajorByRow() {
    document.querySelectorAll('#folios_defects_table tbody tr').forEach((row) => {
        let rowMajor = 0;
        row.querySelectorAll('.defect-value-grid[data-defect-type="major"]').forEach((input) => {
            rowMajor += parseFloat(input.value || 0);
        });
        const majorField = row.querySelector('.row-major-sum');
        if (majorField) {
            majorField.value = rowMajor.toFixed(2);
        }
        const majorFieldModal = row.querySelector('.row-major-sum-modal');
        if (majorFieldModal) {
            majorFieldModal.value = rowMajor.toFixed(2);
        }
    });
}

fillReceptionSelect();
fillSelect('inspector_id', catalogs.inspectors || []);
if ((catalogs.inspectors || []).length === 1) {
    document.getElementById('inspector_id').value = String(catalogs.inspectors[0].id);
}
document.getElementById('reception_id').addEventListener('change', updateReceptionInfo);
document.getElementById('reception_id').addEventListener('input', updateReceptionInfo);
if (window.jQuery) {
    window.jQuery('#reception_id').on('change select2:select select2:clear', updateReceptionInfo);
}
document.getElementById('reception_id').value = '';
updateReceptionInfo();
applyEditingInspection();

document.getElementById('inspection-form').addEventListener('submit', function (event) {
    const submitter = event.submitter || null;
    const closeRequested = !!(submitter && submitter.name === 'GUARDAR_CERRAR');
    if (!document.getElementById('reception_id').value) {
        Swal.fire({ icon: 'warning', title: 'Recepción requerida', text: 'Debe seleccionar una recepción para guardar la inspección.' });
        event.preventDefault();
        return false;
    }
    if (!document.getElementById('producer_id').value) {
        Swal.fire({ icon: 'warning', title: 'Productor requerido', text: 'No fue posible resolver el productor de la recepción seleccionada.' });
        event.preventDefault();
        return false;
    }
    if (!document.getElementById('inspector_id').value) {
        Swal.fire({ icon: 'warning', title: 'Inspector requerido', text: 'Debe seleccionar un inspector antes de guardar.' });
        event.preventDefault();
        return false;
    }

    const caliberMap = new Map((catalogs.calibers || []).map(c => [normalizeText(c.name), String(c.id)]));
    const details = [];
    const invalidVarietyRows = [];
    document.querySelectorAll('#folios_defects_table tbody tr').forEach((row, idx) => {
        if (!row.querySelector('.row-pallet')) {
            return;
        }
        const rowDefects = {};
        row.querySelectorAll('.defect-value-grid').forEach((input) => {
            rowDefects[String(input.dataset.defectId)] = parseFloat(input.value || 0);
        });

        const rowCalibers = {};
        row.querySelectorAll('.row-caliber').forEach((input) => {
            const caliberId = caliberMap.get(normalizeText(input.dataset.caliberName || ''));
            if (caliberId) {
                rowCalibers[caliberId] = parseFloat(input.value || 0);
            }
        });

        const rowFolioId = row.querySelector('.row-folio-id');
        const folioId = (rowFolioId && rowFolioId.value) || null;
        const rowVariety = row.querySelector('.row-variety');
        const rowPhotoJson = row.querySelector('.row-photo-json');
        const varietyId = (rowVariety && rowVariety.value) || document.getElementById('variety_id').value;
        if (!varietyId && !folioId) {
            invalidVarietyRows.push(idx + 1);
            return;
        }
        const photosJson = (rowPhotoJson && rowPhotoJson.value) || '[]';
        let photos = [];
        try {
            photos = JSON.parse(photosJson);
        } catch (e) {
            photos = [];
        }
        details.push({
            folio_id: folioId,
            pallet_number: (row.querySelector('.row-pallet') && row.querySelector('.row-pallet').value) || String(idx + 1),
            handling_type: (row.querySelector('.row-handling') && row.querySelector('.row-handling').value) || 'CONV',
            sample: (row.querySelector('.row-sample') && row.querySelector('.row-sample').value) || '',
            variety_id: varietyId || null,
            defects: rowDefects,
            calibers: rowCalibers,
            photos: photos,
            measurement: {
                weight: parseFloat((row.querySelector('.row-weight') && row.querySelector('.row-weight').value) || 0),
                temperature: parseFloat((row.querySelector('.row-temperature') && row.querySelector('.row-temperature').value) || 0),
                brix: parseFloat((row.querySelector('.row-brix') && row.querySelector('.row-brix').value) || 0),
                baxlo: parseFloat((row.querySelector('.row-baxlo') && row.querySelector('.row-baxlo').value) || 0),
                average: parseFloat((row.querySelector('.row-baxlo-avg') && row.querySelector('.row-baxlo-avg').value) || 0)
            }
        });
    });

    const payload = {
        reception_id: document.getElementById('reception_id').value || null,
        csg: (document.getElementById('csg').value || document.getElementById('rx_number').textContent || ''),
        producer_id: document.getElementById('producer_id').value,
        inspector_id: document.getElementById('inspector_id').value,
        inspection_date: document.getElementById('inspection_date').value,
        product: document.getElementById('rx_varieties').textContent || '',
        product_id: document.getElementById('product_id').value || null,
        inspection_id: parseInt(document.getElementById('inspection_id').value || '0', 10),
        total_pallets: parseInt(document.getElementById('total_pallets').value || details.length || 1, 10),
        is_organic: parseInt(document.getElementById('is_organic').value || '0', 10),
        close_inspection: closeRequested ? 1 : 0,
        details: details
    };

    if (invalidVarietyRows.length > 0) {
        Swal.fire({ icon: 'warning', title: 'Variedad requerida', text: 'Faltan variedades válidas en filas: ' + invalidVarietyRows.join(', ') });
        event.preventDefault();
        return false;
    }

    if (!payload.details.length) {
        Swal.fire({ icon: 'warning', title: 'Sin detalle', text: 'La recepción seleccionada no tiene folios para guardar.' });
        event.preventDefault();
        return false;
    }

    document.getElementById('payload_json').value = JSON.stringify(payload);
});

if (window.__savedInspection) {
    const numberLabel = window.__savedInspection.inspection_number ? (' · N° ' + window.__savedInspection.inspection_number) : '';
    if (window.__savedInspection.inspection_number) {
        document.getElementById('inspection_number_generated').value = window.__savedInspection.inspection_number;
    }
    const estadoLabel = String(window.__savedInspection.estado) === '0' ? 'cerrada' : 'abierta';
    Swal.fire({ icon: 'success', title: 'Guardado', text: 'Inspección #' + window.__savedInspection.inspection_id + numberLabel + ' guardada (' + estadoLabel + ').' });
}
if (window.__savedInspectionError) {
    Swal.fire({ icon: 'error', title: 'Error', text: window.__savedInspectionError });
}
</script>
</body>
</html>
