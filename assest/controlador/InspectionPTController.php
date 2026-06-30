<?php
include_once __DIR__ . '/../config/BDCONFIG.php';

class InspectionPTController
{
    private $conexion;

    public function __construct()
    {
        $cfg = new BDCONFIG();
        $this->conexion = new PDO(
            'mysql:host=' . $cfg->__GET('HOST') . ';dbname=' . $cfg->__GET('DBNAME'),
            $cfg->__GET('USER'),
            $cfg->__GET('PASS')
        );
        $this->conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getCatalogs($empresaId = null, $plantaId = null, $temporadaId = null, $includeReceptionId = null)
    {
        $receptions = [];
        if ($empresaId && $plantaId && $temporadaId) {
            $excludeInspected = '';
            $useSourceType = $this->hasColumn('inspections', 'source_type');
            if ($this->tableExists('inspections') && $this->hasColumn('inspections', 'reception_id')) {
                $excludeInspected = " AND p.ID_PROCESO NOT IN (
                        SELECT reception_id
                        FROM inspections
                        WHERE reception_id IS NOT NULL" . ($useSourceType ? " AND source_type = 'PT'" : "") . (!empty($includeReceptionId) ? " AND reception_id <> :include_reception_id" : "") . "
                    ) ";
            }

            $stmt = $this->conexion->prepare(
                "SELECT p.ID_PROCESO id,
                        p.NUMERO_PROCESO reception_number,
                        p.FECHA_PROCESO reception_date,
                        '' reception_hour,
                        '' guide_number,
                        IFNULL(p.KILOS_NETO_ENTRADA,0) net_kilos,
                        0 gross_kilos,
                        0 package_count,
                        p.ID_PRODUCTOR producer_id,
                        IFNULL(pr.NOMBRE_PRODUCTOR, 'Sin datos') producer_name,
                        CAST(p.ID_VESPECIES AS CHAR) variety_ids,
                        IFNULL(v.NOMBRE_VESPECIES, 'Sin variedad') varieties_label,
                        p.FECHA_PROCESO AS FECHA,
                        WEEK(p.FECHA_PROCESO,3) AS SEMANA,
                        WEEKOFYEAR(p.FECHA_PROCESO) AS SEMANAISO
                 FROM fruta_proceso p
                 LEFT JOIN fruta_productor pr ON pr.ID_PRODUCTOR = p.ID_PRODUCTOR
                 LEFT JOIN fruta_vespecies v ON v.ID_VESPECIES = p.ID_VESPECIES
                 WHERE p.ESTADO_REGISTRO = 1
                   AND p.ESTADO = 1
                   AND p.ID_EMPRESA = :empresa
                   AND p.ID_PLANTA = :planta
                   AND p.ID_TEMPORADA = :temporada
                   {$excludeInspected}
                 ORDER BY p.ID_PROCESO DESC"
            );
            $params = [
                ':empresa' => (int)$empresaId,
                ':planta' => (int)$plantaId,
                ':temporada' => (int)$temporadaId,
            ];
            if (!empty($includeReceptionId) && strpos($excludeInspected, ':include_reception_id') !== false) {
                $params[':include_reception_id'] = (int)$includeReceptionId;
            }
            $stmt->execute($params);
            $receptions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($this->tableExists('calidad_defectos_calidad') && $this->tableExists('calidad_defectos_condicion')) {
            $defectsQuery = "SELECT defect_id AS id, name, defect_type, 'quality' AS defect_scope FROM calidad_defectos_calidad WHERE is_active = 1 AND defect_id IS NOT NULL
                            UNION ALL
                            SELECT defect_id AS id, name, defect_type, 'condition' AS defect_scope FROM calidad_defectos_condicion WHERE is_active = 1 AND defect_id IS NOT NULL
                            ORDER BY defect_scope, defect_type, name";
        } else {
            $defectScopeColumn = $this->hasColumn('defects', 'defect_scope');
            $defectsQuery = "SELECT id, name, defect_type" . ($defectScopeColumn ? ", defect_scope" : "") . " FROM defects WHERE is_active = 1 ORDER BY " . ($defectScopeColumn ? "defect_scope, " : "") . "defect_type, name";
        }

        $foliosByReception = [];
        if ($empresaId && $plantaId && $temporadaId && $this->tableExists('fruta_exiexportacion')) {
            $folioStmt = $this->conexion->prepare(
                "SELECT exi.ID_PROCESO reception_id,
                        exi.ID_EXIEXPORTACION folio_id,
                        IFNULL(exi.FOLIO_EXIEXPORTACION, exi.ID_EXIEXPORTACION) folio,
                        IFNULL(exi.FOLIO_AUXILIAR_EXIEXPORTACION, '') alias_folio,
                        IFNULL(exi.FOLIO_AUXILIAR_EXIEXPORTACION, '') final_folio,
                        IFNULL(exi.CANTIDAD_ENVASE_EXIEXPORTACION, 0) sample_size,
                        IFNULL(v.NOMBRE_VESPECIES, 'Sin variedad') variety_name
                 FROM fruta_exiexportacion exi
                 LEFT JOIN fruta_vespecies v ON v.ID_VESPECIES = exi.ID_VESPECIES
                 WHERE exi.ESTADO_REGISTRO = 1
                   AND exi.ESTADO = 1
                   AND exi.ID_EMPRESA = :empresa
                   AND exi.ID_PLANTA = :planta
                   AND exi.ID_TEMPORADA = :temporada
                   AND exi.ID_PROCESO IS NOT NULL
                 ORDER BY exi.ID_EXIEXPORTACION ASC"
            );
            $folioStmt->execute([
                ':empresa' => (int)$empresaId,
                ':planta' => (int)$plantaId,
                ':temporada' => (int)$temporadaId,
            ]);
            $seenFolios = [];
            foreach ($folioStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $key = (string)$row['reception_id'];
                if (!isset($foliosByReception[$key])) {
                    $foliosByReception[$key] = [];
                }
                $folioKey = $key . '-' . (string)$row['folio_id'];
                if (isset($seenFolios[$folioKey])) {
                    continue;
                }
                $seenFolios[$folioKey] = true;
                $foliosByReception[$key][] = [
                    'folio_id' => $row['folio_id'],
                    'folio' => $row['folio'],
                    'alias' => $row['alias_folio'],
                    'folio_final' => $row['final_folio'],
                    'sample_size' => $row['sample_size'],
                    'variety_name' => $row['variety_name'],
                ];
            }
        }

        $producerDetailsMap = [];
        $producerStmt = $this->conexion->query("SELECT * FROM fruta_productor WHERE ESTADO_REGISTRO = 1");
        foreach ($producerStmt->fetchAll(PDO::FETCH_ASSOC) as $producerRow) {
            $producerDetailsMap[(string)$producerRow['ID_PRODUCTOR']] = $producerRow;
        }

        return [
            'producers' => $this->conexion->query("SELECT ID_PRODUCTOR id, NOMBRE_PRODUCTOR name FROM fruta_productor WHERE ESTADO_REGISTRO = 1 ORDER BY NOMBRE_PRODUCTOR")->fetchAll(PDO::FETCH_ASSOC),
            'inspectors' => $this->conexion->query("SELECT ID_INPECTOR id, NOMBRE_INPECTOR name FROM fruta_inpector WHERE ESTADO_REGISTRO = 1 ORDER BY NOMBRE_INPECTOR")->fetchAll(PDO::FETCH_ASSOC),
            'defects' => $this->conexion->query($defectsQuery)->fetchAll(PDO::FETCH_ASSOC),
            'calibers' => $this->conexion->query("SELECT id, name FROM calibers WHERE is_active = 1 ORDER BY sort_order, name")->fetchAll(PDO::FETCH_ASSOC),
            'products' => $this->conexion->query("SELECT ID_VESPECIES id, NOMBRE_VESPECIES name FROM fruta_vespecies WHERE ESTADO_REGISTRO = 1 ORDER BY NOMBRE_VESPECIES")->fetchAll(PDO::FETCH_ASSOC),
            'varieties' => $this->conexion->query("SELECT id, name, id AS product_id FROM varieties WHERE is_active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC),
            'receptions' => $receptions,
            'folios_by_reception' => $foliosByReception,
            'producer_details_map' => $producerDetailsMap
        ];
    }

    public function saveInspection(array $payload, $userId, array $uploadedFiles = [], array $sessionContext = [])
    {
        $this->validatePayload($payload);
        if (!$this->recordExistsByColumn('fruta_productor', 'ID_PRODUCTOR', (int)$payload['producer_id'])) {
            throw new InvalidArgumentException('Productor inválido.');
        }
        if (!$this->recordExistsByColumn('fruta_inpector', 'ID_INPECTOR', (int)$payload['inspector_id'])) {
            throw new InvalidArgumentException('Inspector inválido.');
        }

        $this->conexion->beginTransaction();
        try {
            $inspectionId = !empty($payload['inspection_id']) ? (int)$payload['inspection_id'] : 0;
            $isUpdate = $inspectionId > 0;
            $useReception = $this->hasColumn('inspections', 'reception_id');
            $useSourceType = $this->hasColumn('inspections', 'source_type');
            $useInspectionNumber = $this->hasColumn('inspections', 'inspection_number');
            $empresaColumn = $this->resolveExistingColumn('inspections', ['empresa_id', 'ID_EMPRESA']);
            $plantaColumn = $this->resolveExistingColumn('inspections', ['planta_id', 'ID_PLANTA']);
            $temporadaColumn = $this->resolveExistingColumn('inspections', ['temporada_id', 'ID_TEMPORADA']);
            $inspectionNumber = null;
            $estadoColumn = $this->resolveExistingColumn('inspections', ['estado', 'ESTADO']);
            $estadoRegistroColumn = $this->resolveExistingColumn('inspections', ['estado_registro', 'ESTADO_REGISTRO']);
            $isCloseRequested = !empty($payload['close_inspection']);
            $estadoValue = $isCloseRequested ? 0 : 1;
            $inspectionNumber = null;
            if ($isUpdate) {
                $currentStmt = $this->conexion->prepare("SELECT id" . ($useInspectionNumber ? ", inspection_number" : "") . " FROM inspections WHERE id = :id LIMIT 1");
                $currentStmt->execute([':id' => $inspectionId]);
                $current = $currentStmt->fetch(PDO::FETCH_ASSOC);
                if (!$current) {
                    throw new InvalidArgumentException('La inspección a editar no existe.');
                }
                if ($useInspectionNumber) {
                    $inspectionNumber = $current['inspection_number'] ?? null;
                }
            }
            if (!$inspectionNumber && !empty($payload['reception_id']) && $useInspectionNumber) {
                $inspectionNumber = $this->generateInspectionNumber((int)$payload['reception_id']);
            }

            $params = [
                ':producer_id' => (int)$payload['producer_id'],
                ':inspector_id' => (int)$payload['inspector_id'],
                ':inspection_date' => $payload['inspection_date'],
                ':product' => trim($payload['product']),
                ':is_organic' => !empty($payload['is_organic']) ? 1 : 0,
                ':total_pallets' => (int)$payload['total_pallets'],
                ':updated_by' => (int)$userId,
            ];
            if ($useReception) {
                $params[':reception_id'] = !empty($payload['reception_id']) ? (int)$payload['reception_id'] : null;
            }
            if ($useInspectionNumber) {
                $params[':inspection_number'] = $inspectionNumber;
            }
            if ($useSourceType) {
                $params[':source_type'] = 'PT';
            }
            if ($estadoColumn) {
                $params[':estado'] = $estadoValue;
            }
            if ($estadoRegistroColumn) {
                $params[':estado_registro'] = 1;
            }
            if ($empresaColumn) {
                $params[':empresa_id'] = (int)($sessionContext['empresa_id'] ?? 0);
            }
            if ($plantaColumn) {
                $params[':planta_id'] = (int)($sessionContext['planta_id'] ?? 0);
            }
            if ($temporadaColumn) {
                $params[':temporada_id'] = (int)($sessionContext['temporada_id'] ?? 0);
            }

            if ($isUpdate) {
                $updateSql = "UPDATE inspections SET producer_id = :producer_id, inspector_id = :inspector_id, inspection_date = :inspection_date, product = :product, is_organic = :is_organic, total_pallets = :total_pallets, updated_by = :updated_by"
                    . ($useReception ? ", reception_id = :reception_id" : "")
                    . ($useInspectionNumber ? ", inspection_number = :inspection_number" : "")
                    . ($useSourceType ? ", source_type = :source_type" : "")
                    . ($estadoColumn ? ", `{$estadoColumn}` = :estado" : "")
                    . ($estadoRegistroColumn ? ", `{$estadoRegistroColumn}` = :estado_registro" : "")
                    . ($empresaColumn ? ", `{$empresaColumn}` = :empresa_id" : "")
                    . ($plantaColumn ? ", `{$plantaColumn}` = :planta_id" : "")
                    . ($temporadaColumn ? ", `{$temporadaColumn}` = :temporada_id" : "")
                    . " WHERE id = :id";
                $params[':id'] = $inspectionId;
                $stmt = $this->conexion->prepare($updateSql);
                $stmt->execute($params);

                if ($this->tableExists('inspection_results')) {
                    $deleteResults = $this->conexion->prepare("DELETE FROM inspection_results WHERE inspection_id = :inspection_id");
                    $deleteResults->execute([':inspection_id' => $inspectionId]);
                }
                if ($this->tableExists('inspection_details')) {
                    if ($this->tableExists('inspection_defect_values')) {
                        $this->conexion->prepare("DELETE idv FROM inspection_defect_values idv INNER JOIN inspection_details d ON d.id = idv.inspection_detail_id WHERE d.inspection_id = :inspection_id")
                            ->execute([':inspection_id' => $inspectionId]);
                    }
                    if ($this->tableExists('inspection_caliber_values')) {
                        $this->conexion->prepare("DELETE icv FROM inspection_caliber_values icv INNER JOIN inspection_details d ON d.id = icv.inspection_detail_id WHERE d.inspection_id = :inspection_id")
                            ->execute([':inspection_id' => $inspectionId]);
                    }
                    if ($this->tableExists('inspection_measurements')) {
                        $this->conexion->prepare("DELETE im FROM inspection_measurements im INNER JOIN inspection_details d ON d.id = im.inspection_detail_id WHERE d.inspection_id = :inspection_id")
                            ->execute([':inspection_id' => $inspectionId]);
                    }
                    if ($this->tableExists('calidad_inspection_detail_photos')) {
                        $this->conexion->prepare("DELETE cip FROM calidad_inspection_detail_photos cip INNER JOIN inspection_details d ON d.id = cip.inspection_detail_id WHERE d.inspection_id = :inspection_id")
                            ->execute([':inspection_id' => $inspectionId]);
                    }
                    $this->conexion->prepare("DELETE FROM inspection_details WHERE inspection_id = :inspection_id")
                        ->execute([':inspection_id' => $inspectionId]);
                }
            } else {
                $insertSql = "INSERT INTO inspections (producer_id, inspector_id, inspection_date, product, is_organic, total_pallets, created_by, updated_by"
                    . ($useReception ? ", reception_id" : "")
                    . ($useInspectionNumber ? ", inspection_number" : "")
                    . ($useSourceType ? ", source_type" : "")
                    . ($estadoColumn ? ", `{$estadoColumn}`" : "")
                    . ($estadoRegistroColumn ? ", `{$estadoRegistroColumn}`" : "")
                    . ($empresaColumn ? ", `{$empresaColumn}`" : "")
                    . ($plantaColumn ? ", `{$plantaColumn}`" : "")
                    . ($temporadaColumn ? ", `{$temporadaColumn}`" : "")
                    . ")
                    VALUES (:producer_id, :inspector_id, :inspection_date, :product, :is_organic, :total_pallets, :created_by, :updated_by"
                    . ($useReception ? ", :reception_id" : "")
                    . ($useInspectionNumber ? ", :inspection_number" : "")
                    . ($useSourceType ? ", :source_type" : "")
                    . ($estadoColumn ? ", :estado" : "")
                    . ($estadoRegistroColumn ? ", :estado_registro" : "")
                    . ($empresaColumn ? ", :empresa_id" : "")
                    . ($plantaColumn ? ", :planta_id" : "")
                    . ($temporadaColumn ? ", :temporada_id" : "")
                    . ")";
                $params[':created_by'] = (int)$userId;
                $stmt = $this->conexion->prepare($insertSql);
                $stmt->execute($params);
                $inspectionId = (int)$this->conexion->lastInsertId();
            }

            $major = 0.0;
            $minor = 0.0;

            $useHandlingType = $this->hasColumn('inspection_details', 'handling_type');
            $useSourceFolio = $this->hasColumn('inspection_details', 'source_folio_id');

            foreach ($payload['details'] as $detailIndex => $detail) {
                $sourceFolioId = (int)($detail['folio_id'] ?? 0);
                $folioSource = $sourceFolioId > 0 ? $this->getFolioSourceData($sourceFolioId) : null;
                $varietyId = 0;
                if ($folioSource && !empty($folioSource['variety_id_source'])) {
                    $varietyId = (int)$folioSource['variety_id_source'];
                } else {
                    $varietyId = (int)($detail['variety_id'] ?? 0);
                }
                if ($varietyId <= 0 || !$this->recordExistsByColumn('fruta_vespecies', 'ID_VESPECIES', $varietyId)) {
                    throw new InvalidArgumentException('Variedad inválida en detalle #' . ($detailIndex + 1) . '. Verifique las variedades asociadas a los folios.');
                }
                $detailSql = "INSERT INTO inspection_details (inspection_id, pallet_number, sample, variety_id"
                    . ($useHandlingType ? ", handling_type" : "")
                    . ($useSourceFolio ? ", source_folio_id" : "")
                    . ")
                    VALUES (:inspection_id, :pallet_number, :sample, :variety_id"
                    . ($useHandlingType ? ", :handling_type" : "")
                    . ($useSourceFolio ? ", :source_folio_id" : "")
                    . ")";
                $detailStmt = $this->conexion->prepare($detailSql);
                $detailParams = [
                    ':inspection_id' => $inspectionId,
                    ':pallet_number' => trim($detail['pallet_number']),
                    ':sample' => trim((string)($folioSource['sample_size'] ?? $detail['sample'] ?? '')),
                    ':variety_id' => $varietyId
                ];
                if ($useHandlingType) {
                    $detailParams[':handling_type'] = strtoupper((string)($detail['handling_type'] ?? 'CONV')) === 'ORG' ? 'ORG' : 'CONV';
                }
                if ($useSourceFolio) {
                    $detailParams[':source_folio_id'] = $sourceFolioId > 0 ? $sourceFolioId : null;
                }
                $detailStmt->execute($detailParams);
                $detailId = (int)$this->conexion->lastInsertId();

                if (!empty($detail['defects'])) {
                    $defStmt = $this->conexion->prepare("INSERT INTO inspection_defect_values (inspection_detail_id, defect_id, value) VALUES (:detail_id, :defect_id, :value)");
                    foreach ($detail['defects'] as $defectId => $value) {
                        if (!$this->recordExistsByColumn('defects', 'id', (int)$defectId)) {
                            throw new InvalidArgumentException('Defecto inválido en detalle #' . ($detailIndex + 1) . '.');
                        }
                        $numeric = (float)$value;
                        $defStmt->execute([':detail_id' => $detailId, ':defect_id' => (int)$defectId, ':value' => $numeric]);
                        $dtype = $this->getDefectType((int)$defectId);
                        if ($dtype === 'major') {
                            $major += $numeric;
                        } else {
                            $minor += $numeric;
                        }
                    }
                }

                if (!empty($detail['calibers'])) {
                    $calStmt = $this->conexion->prepare("INSERT INTO inspection_caliber_values (inspection_detail_id, caliber_id, value) VALUES (:detail_id, :caliber_id, :value)");
                    foreach ($detail['calibers'] as $caliberId => $value) {
                        if (!$this->recordExistsByColumn('calibers', 'id', (int)$caliberId)) {
                            throw new InvalidArgumentException('Calibre inválido en detalle #' . ($detailIndex + 1) . '.');
                        }
                        $calStmt->execute([':detail_id' => $detailId, ':caliber_id' => (int)$caliberId, ':value' => (float)$value]);
                    }
                }

                if (!empty($detail['measurement'])) {
                    $m = $detail['measurement'];
                    $mStmt = $this->conexion->prepare(
                        "INSERT INTO inspection_measurements (inspection_detail_id, weight, temperature, brix, baxlo, average)
                        VALUES (:detail_id, :weight, :temperature, :brix, :baxlo, :average)"
                    );
                    $mStmt->execute([
                        ':detail_id' => $detailId,
                        ':weight' => (float)($m['weight'] ?? 0),
                        ':temperature' => (float)($m['temperature'] ?? 0),
                        ':brix' => (float)($m['brix'] ?? 0),
                        ':baxlo' => (float)($m['baxlo'] ?? 0),
                        ':average' => (float)($m['average'] ?? 0),
                    ]);
                }

                if (!empty($detail['photos']) && $this->tableExists('calidad_inspection_detail_photos')) {
                    $this->saveDetailPhotos($inspectionId, $detailId, $detailIndex, $detail['photos'], $uploadedFiles, (int)$userId);
                }
            }

            $score = max(0, 100 - ($major * 1.5) - ($minor * 0.75));
            $categoryId = $this->resolveCategoryId($score);

            $resultStmt = $this->conexion->prepare(
                "INSERT INTO inspection_results (inspection_id, major_defects_sum, minor_defects_sum, score, category_id)
                VALUES (:inspection_id, :major, :minor, :score, :category_id)"
            );
            $resultStmt->execute([
                ':inspection_id' => $inspectionId,
                ':major' => $major,
                ':minor' => $minor,
                ':score' => $score,
                ':category_id' => $categoryId,
            ]);

            $this->conexion->commit();

            return [
                'inspection_id' => $inspectionId,
                'inspection_number' => $inspectionNumber,
                'score' => $score,
                'category_id' => $categoryId,
                'estado' => $estadoValue,
                'updated' => $isUpdate ? 1 : 0
            ];
        } catch (Exception $e) {
            $this->conexion->rollBack();
            throw $e;
        }
    }

    private function generateInspectionNumber($receptionId)
    {
        if (!$this->tableExists('fruta_proceso')) {
            return null;
        }
        try {
            $rxStmt = $this->conexion->prepare(
                "SELECT ID_EMPRESA, ID_PLANTA FROM fruta_proceso WHERE ID_PROCESO = :id LIMIT 1"
            );
            $rxStmt->execute([':id' => (int)$receptionId]);
            $rx = $rxStmt->fetch(PDO::FETCH_ASSOC);
            if (!$rx) {
                return null;
            }

            $prefix = (string)((int)$rx['ID_EMPRESA']) . (string)((int)$rx['ID_PLANTA']);
            $like = $prefix . '%';

            $maxStmt = $this->conexion->prepare(
                "SELECT MAX(CAST(RIGHT(COALESCE(inspection_number,''), 6) AS UNSIGNED)) AS max_correlative
                 FROM inspections
                 WHERE inspection_number LIKE :prefix"
            );
            $maxStmt->execute([':prefix' => $like]);
            $maxRow = $maxStmt->fetch(PDO::FETCH_ASSOC);
            $maxValue = (int)($maxRow['max_correlative'] ?? 0);
            $next = $maxValue + 1;

            return $prefix . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
        } catch (Throwable $e) {
            return null;
        }
    }

    private function saveDetailPhotos($inspectionId, $detailId, $detailIndex, array $photosMeta, array $uploadedFiles, $userId)
    {
        $baseDir = __DIR__ . '/../../calidad/inspection-mp/uploads';
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0775, true);
        }
        $inspectionDir = $baseDir . '/inspection_' . (int)$inspectionId;
        if (!is_dir($inspectionDir)) {
            mkdir($inspectionDir, 0775, true);
        }

        $stmt = $this->conexion->prepare(
            "INSERT INTO calidad_inspection_detail_photos (inspection_detail_id, photo_name, photo_comment, file_path, original_filename, created_by)
             VALUES (:inspection_detail_id, :photo_name, :photo_comment, :file_path, :original_filename, :created_by)"
        );

        $saved = 0;
        foreach ($photosMeta as $slot => $meta) {
            if ($saved >= 5) {
                break;
            }
            $inputName = $meta['file_input'] ?? ('photo_' . $detailIndex . '_' . $slot);
            if (empty($uploadedFiles[$inputName]) || (int)$uploadedFiles[$inputName]['error'] !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmpName = $uploadedFiles[$inputName]['tmp_name'];
            $originalName = basename((string)$uploadedFiles[$inputName]['name']);
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
                continue;
            }

            $safeBase = preg_replace('/[^a-zA-Z0-9_\-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
            $finalName = sprintf('detail_%d_%s_%d.%s', (int)$detailId, $safeBase, time(), $ext);
            $absolutePath = $inspectionDir . '/' . $finalName;
            if (!move_uploaded_file($tmpName, $absolutePath)) {
                continue;
            }

            $relativePath = 'calidad/inspection-mp/uploads/inspection_' . (int)$inspectionId . '/' . $finalName;
            $stmt->execute([
                ':inspection_detail_id' => (int)$detailId,
                ':photo_name' => trim((string)($meta['name'] ?? '')),
                ':photo_comment' => trim((string)($meta['comment'] ?? '')),
                ':file_path' => $relativePath,
                ':original_filename' => $originalName,
                ':created_by' => (int)$userId,
            ]);
            $saved++;
        }
    }

    public function importFromExcel($tmpFile, $userId)
    {
        require_once __DIR__ . '/../../api/phpoffice/vendor/autoload.php';
        $sheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($tmpFile)->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        if (count($rows) < 2) {
            throw new InvalidArgumentException('Archivo sin datos.');
        }
        $headerRow = $rows[1];
        $headerByCol = [];
        foreach ($headerRow as $col => $headerName) {
            $headerByCol[$col] = trim((string)$headerName);
        }

        $producersMap = [];
        foreach ($this->conexion->query("SELECT ID_PRODUCTOR id, NOMBRE_PRODUCTOR name FROM fruta_productor WHERE ESTADO_REGISTRO = 1")->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $producersMap[$this->normalizeHeader($row['name'])] = (int)$row['id'];
        }
        $inspectorsMap = [];
        foreach ($this->conexion->query("SELECT ID_INPECTOR id, NOMBRE_INPECTOR name FROM fruta_inpector WHERE ESTADO_REGISTRO = 1")->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $inspectorsMap[$this->normalizeHeader($row['name'])] = (int)$row['id'];
        }
        $varietiesMap = [];
        foreach ($this->conexion->query("SELECT ID_VESPECIES id, NOMBRE_VESPECIES name FROM fruta_vespecies WHERE ESTADO_REGISTRO = 1")->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $varietiesMap[$this->normalizeHeader($row['name'])] = (int)$row['id'];
        }

        $defectsQuery = "SELECT id, name FROM defects WHERE is_active = 1";
        if ($this->tableExists('calidad_defectos_calidad') && $this->tableExists('calidad_defectos_condicion')) {
            $defectsQuery = "SELECT defect_id AS id, name FROM calidad_defectos_calidad WHERE is_active = 1
                            UNION ALL
                            SELECT defect_id AS id, name FROM calidad_defectos_condicion WHERE is_active = 1";
        }
        $defectColumnMap = [];
        foreach ($this->conexion->query($defectsQuery)->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $defectColumnMap[$this->normalizeHeader($row['name'])] = (int)$row['id'];
        }

        $caliberColumnMap = [];
        foreach ($this->conexion->query("SELECT id, name FROM calibers WHERE is_active = 1")->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $caliberColumnMap[$this->normalizeHeader($row['name'])] = (int)$row['id'];
        }

        $inspectionGroups = [];
        foreach (array_slice($rows, 1) as $excelRow) {
            if (empty(array_filter($excelRow, fn($v) => $v !== null && trim((string)$v) !== ''))) {
                continue;
            }

            $rowData = [];
            foreach ($excelRow as $col => $value) {
                $rowData[$headerByCol[$col] ?? $col] = $value;
            }

            $producerName = (string)$this->getExcelValue($rowData, ['Productor', 'producer', 'producer_name']);
            $inspectorName = (string)$this->getExcelValue($rowData, ['_L_Inspector', 'Inspector', 'inspector', 'inspector_name']);
            $inspectionDateRaw = $this->getExcelValue($rowData, ['Date_Inspection', 'inspection_date', 'fecha_inspeccion']);
            $productName = (string)$this->getExcelValue($rowData, ['_L_Name_Product_Services', 'product', 'producto']);
            $organicRaw = (string)$this->getExcelValue($rowData, ['_L_Organic_Display', 'is_organic', 'organic']);
            $csgValue = trim((string)$this->getExcelValue($rowData, ['CSG', 'csg']));

            $producerId = $producersMap[$this->normalizeHeader($producerName)] ?? 0;
            if ($producerId <= 0) {
                throw new InvalidArgumentException('Productor no encontrado en maestro: ' . $producerName);
            }
            $inspectorId = $inspectorsMap[$this->normalizeHeader($inspectorName)] ?? 0;
            if ($inspectorId <= 0) {
                throw new InvalidArgumentException('Inspector no encontrado en maestro: ' . $inspectorName);
            }

            $inspectionDate = $this->parseExcelDate($inspectionDateRaw);
            $isOrganic = in_array(strtoupper(trim($organicRaw)), ['ORG', 'ORGANIC', 'SI', 'YES', '1'], true) ? 1 : 0;

            $groupKey = implode('|', [
                $csgValue,
                $producerId,
                $inspectorId,
                $inspectionDate,
                $this->normalizeHeader($productName),
                $isOrganic
            ]);
            if (!isset($inspectionGroups[$groupKey])) {
                $inspectionGroups[$groupKey] = [
                    'reception_id' => null,
                    'producer_id' => $producerId,
                    'inspector_id' => $inspectorId,
                    'inspection_date' => $inspectionDate,
                    'product' => $productName ?: 'MP Importada',
                    'is_organic' => $isOrganic,
                    'total_pallets' => (int)$this->parseDecimal($this->getExcelValue($rowData, ['_L_Number_of_Pallets_Total', 'total_pallets'])),
                    'details' => []
                ];
            }

            $varietyName = (string)$this->getExcelValue($rowData, ['Variety', 'variety', 'variedad']);
            $varietyId = (int)$this->getExcelValue($rowData, ['variety_id']);
            if ($varietyId <= 0) {
                $varietyId = $varietiesMap[$this->normalizeHeader($varietyName)] ?? 0;
            }
            if ($varietyId <= 0) {
                throw new InvalidArgumentException('Variedad no encontrada en maestro: ' . $varietyName);
            }

            $detailDefects = [];
            foreach ($rowData as $columnName => $value) {
                $normalizedColumn = $this->normalizeHeader($columnName);
                if (isset($defectColumnMap[$normalizedColumn])) {
                    $detailDefects[(string)$defectColumnMap[$normalizedColumn]] = $this->parseDecimal($value);
                }
            }
            $detailCalibers = [];
            foreach ($rowData as $columnName => $value) {
                $normalizedColumn = $this->normalizeHeader($columnName);
                if (isset($caliberColumnMap[$normalizedColumn])) {
                    $detailCalibers[(string)$caliberColumnMap[$normalizedColumn]] = $this->parseDecimal($value);
                }
            }

            $inspectionGroups[$groupKey]['details'][] = [
                'pallet_number' => (string)$this->getExcelValue($rowData, ['_P_Pallet_Number', 'pallet_number', 'pallet']),
                'sample' => (string)$this->getExcelValue($rowData, ['MUESTRA', 'sample']),
                'variety_id' => $varietyId,
                'defects' => $detailDefects,
                'calibers' => $detailCalibers,
                'measurement' => [
                    'weight' => $this->parseDecimal($this->getExcelValue($rowData, ['PCT_10Pieces_Weight', 'weight'])),
                    'temperature' => $this->parseDecimal($this->getExcelValue($rowData, ['Temperature', 'temperature'])),
                    'brix' => $this->parseDecimal($this->getExcelValue($rowData, ['Brix', 'brix'])),
                    'baxlo' => $this->parseDecimal($this->getExcelValue($rowData, ['Baxlo', 'baxlo'])),
                    'average' => $this->parseDecimal($this->getExcelValue($rowData, ['Baxlo_Avg', 'average'])),
                ]
            ];
        }

        if (empty($inspectionGroups)) {
            throw new InvalidArgumentException('No se encontraron filas válidas para importar.');
        }

        $savedInspectionIds = [];
        foreach ($inspectionGroups as &$payload) {
            if ((int)$payload['total_pallets'] <= 0) {
                $payload['total_pallets'] = count($payload['details']);
            }
            $saved = $this->saveInspection($payload, $userId);
            $savedInspectionIds[] = $saved['inspection_id'];
        }

        return [
            'imported' => count($savedInspectionIds),
            'inspection_ids' => $savedInspectionIds
        ];
    }

    public function exportToExcel($inspectionId)
    {
        require_once __DIR__ . '/../../api/phpoffice/vendor/autoload.php';
        $stmt = $this->conexion->prepare(
            "SELECT i.id, i.producer_id, i.inspector_id, i.inspection_date, i.product, i.is_organic,
                    d.pallet_number, d.sample, d.variety_id,
                    m.weight, m.temperature, m.brix, m.baxlo, m.average
             FROM inspections i
             INNER JOIN inspection_details d ON d.inspection_id = i.id
             LEFT JOIN inspection_measurements m ON m.inspection_detail_id = d.id
             WHERE i.id = :id"
        );
        $stmt->execute([':id' => (int)$inspectionId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            throw new RuntimeException('Inspección no encontrada.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['producer_id','inspector_id','inspection_date','product','is_organic','pallet_number','sample','variety_id','weight','temperature','brix','baxlo','average'];
        $sheet->fromArray($headers, null, 'A1');
        $line = 2;
        foreach ($rows as $row) {
            $sheet->fromArray(array_values($row), null, 'A' . $line++);
        }

        return $spreadsheet;
    }

    public function getInspectionGrouped($empresaId = null, $plantaId = null, $temporadaId = null)
    {
        $filters = [];
        $params = [];

        $empresaColumn = $this->resolveExistingColumn('inspections', ['empresa_id', 'ID_EMPRESA']);
        $plantaColumn = $this->resolveExistingColumn('inspections', ['planta_id', 'ID_PLANTA']);
        $temporadaColumn = $this->resolveExistingColumn('inspections', ['temporada_id', 'ID_TEMPORADA']);
        $estadoColumn = $this->resolveExistingColumn('inspections', ['estado', 'ESTADO']);
        $estadoRegistroColumn = $this->resolveExistingColumn('inspections', ['estado_registro', 'ESTADO_REGISTRO']);

        if ($empresaColumn && !empty($empresaId)) {
            $filters[] = "i.`{$empresaColumn}` = :empresa";
            $params[':empresa'] = (int)$empresaId;
        }
        if ($plantaColumn && !empty($plantaId)) {
            $filters[] = "i.`{$plantaColumn}` = :planta";
            $params[':planta'] = (int)$plantaId;
        }
        if ($temporadaColumn && !empty($temporadaId)) {
            $filters[] = "i.`{$temporadaColumn}` = :temporada";
            $params[':temporada'] = (int)$temporadaId;
        }
        if ($estadoRegistroColumn) {
            $filters[] = "i.`{$estadoRegistroColumn}` = 1";
        }
        if ($this->hasColumn('inspections', 'source_type')) {
            $filters[] = "i.source_type = 'PT'";
        }

        $whereSql = '';
        if (!empty($filters)) {
            $whereSql = ' WHERE ' . implode(' AND ', $filters);
        }

        $inspectionNumberExpr = $this->hasColumn('inspections', 'inspection_number')
            ? "COALESCE(i.inspection_number, CONCAT('INS-', i.id))"
            : "CONCAT('INS-', i.id)";
        $receptionJoin = $this->hasColumn('inspections', 'reception_id')
            ? "LEFT JOIN fruta_proceso rx ON rx.ID_PROCESO = i.reception_id"
            : "";
        $receptionNumberExpr = $this->hasColumn('inspections', 'reception_id')
            ? "IFNULL(rx.NUMERO_PROCESO, 'Sin proceso')"
            : "'Sin proceso'";
        $guideNumberExpr = $this->hasColumn('inspections', 'reception_id')
            ? "IFNULL(rx.FECHA_PROCESO, 'Sin fecha')"
            : "'Sin guía'";
        $resultJoin = $this->tableExists('inspection_results')
            ? "LEFT JOIN inspection_results ir ON ir.inspection_id = i.id"
            : "";
        $scoreExpr = $this->tableExists('inspection_results')
            ? "IFNULL(ir.score, 0)"
            : "0";

        $sql = "SELECT i.id,
                       {$inspectionNumberExpr} AS inspection_number,
                       i.inspection_date,
                       IFNULL(p.NOMBRE_PRODUCTOR, 'Sin datos') AS producer_name,
                       IFNULL(inp.NOMBRE_INPECTOR, 'Sin datos') AS inspector_name,
                       IFNULL(i.product, 'Sin datos') AS product_name,
                       IFNULL(i.total_pallets, 0) AS total_pallets,
                       {$scoreExpr} AS score,
                       " . ($estadoColumn ? "IFNULL(i.`{$estadoColumn}`, 1)" : "1") . " AS estado,
                       {$receptionNumberExpr} AS reception_number,
                       {$guideNumberExpr} AS guide_number,
                       COUNT(DISTINCT d.id) AS detail_rows
                FROM inspections i
                LEFT JOIN fruta_productor p ON p.ID_PRODUCTOR = i.producer_id
                LEFT JOIN fruta_inpector inp ON inp.ID_INPECTOR = i.inspector_id
                {$resultJoin}
                LEFT JOIN inspection_details d ON d.inspection_id = i.id
                {$receptionJoin}
                {$whereSql}
                GROUP BY i.id, inspection_number, i.inspection_date, producer_name, inspector_name, product_name, total_pallets, score, reception_number, guide_number
                ORDER BY i.id DESC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateInspectionState($inspectionId, $estado, $userId = null)
    {
        $estadoColumn = $this->resolveExistingColumn('inspections', ['estado', 'ESTADO']);
        if (!$estadoColumn) {
            throw new RuntimeException('La tabla inspections no tiene columna de estado.');
        }
        $updatedByColumn = $this->resolveExistingColumn('inspections', ['updated_by', 'UPDATED_BY']);

        $sql = "UPDATE inspections SET `{$estadoColumn}` = :estado";
        if ($updatedByColumn && $userId !== null) {
            $sql .= ", `{$updatedByColumn}` = :updated_by";
        }
        $sql .= " WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $params = [':estado' => (int)$estado, ':id' => (int)$inspectionId];
        if ($updatedByColumn && $userId !== null) {
            $params[':updated_by'] = (int)$userId;
        }
        $stmt->execute($params);
        return $stmt->rowCount() > 0;
    }

    public function deleteInspection($inspectionId, $userId = null)
    {
        $estadoRegistroColumn = $this->resolveExistingColumn('inspections', ['estado_registro', 'ESTADO_REGISTRO']);
        if ($estadoRegistroColumn) {
            $updatedByColumn = $this->resolveExistingColumn('inspections', ['updated_by', 'UPDATED_BY']);
            $sql = "UPDATE inspections SET `{$estadoRegistroColumn}` = 0";
            if ($updatedByColumn && $userId !== null) {
                $sql .= ", `{$updatedByColumn}` = :updated_by";
            }
            $sql .= " WHERE id = :id";
            $stmt = $this->conexion->prepare($sql);
            $params = [':id' => (int)$inspectionId];
            if ($updatedByColumn && $userId !== null) {
                $params[':updated_by'] = (int)$userId;
            }
            $stmt->execute($params);
            return $stmt->rowCount() > 0;
        }

        $estadoColumn = $this->resolveExistingColumn('inspections', ['estado', 'ESTADO']);
        if ($estadoColumn) {
            return $this->updateInspectionState($inspectionId, 0, $userId);
        }

        throw new RuntimeException('No existe una columna para eliminación lógica en inspections.');
    }

    public function getInspectionSummaryById($inspectionId)
    {
        $estadoColumn = $this->resolveExistingColumn('inspections', ['estado', 'ESTADO']);
        $inspectionNumberExpr = $this->hasColumn('inspections', 'inspection_number')
            ? "COALESCE(i.inspection_number, CONCAT('INS-', i.id))"
            : "CONCAT('INS-', i.id)";
        $receptionJoin = $this->hasColumn('inspections', 'reception_id')
            ? "LEFT JOIN fruta_proceso rx ON rx.ID_PROCESO = i.reception_id"
            : "";
        $receptionNumberExpr = $this->hasColumn('inspections', 'reception_id')
            ? "IFNULL(rx.NUMERO_PROCESO, 'Sin proceso')"
            : "'Sin proceso'";
        $sourceFilter = $this->hasColumn('inspections', 'source_type') ? " AND i.source_type = 'PT'" : "";

        $sql = "SELECT i.id,
                       {$inspectionNumberExpr} AS inspection_number,
                       i.inspection_date,
                       IFNULL(p.NOMBRE_PRODUCTOR, 'Sin datos') AS producer_name,
                       IFNULL(inp.NOMBRE_INPECTOR, 'Sin datos') AS inspector_name,
                       {$receptionNumberExpr} AS reception_number,
                       " . ($estadoColumn ? "IFNULL(i.`{$estadoColumn}`, 1)" : "1") . " AS estado
                FROM inspections i
                LEFT JOIN fruta_productor p ON p.ID_PRODUCTOR = i.producer_id
                LEFT JOIN fruta_inpector inp ON inp.ID_INPECTOR = i.inspector_id
                {$receptionJoin}
                WHERE i.id = :id {$sourceFilter}
                LIMIT 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([':id' => (int)$inspectionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getInspectionForEdit($inspectionId)
    {
        $estadoRegistroColumn = $this->resolveExistingColumn('inspections', ['estado_registro', 'ESTADO_REGISTRO']);
        $whereActive = $estadoRegistroColumn ? " AND i.`{$estadoRegistroColumn}` = 1" : "";
        $whereSource = $this->hasColumn('inspections', 'source_type') ? " AND i.source_type = 'PT'" : "";

        $headerStmt = $this->conexion->prepare(
            "SELECT i.*
             FROM inspections i
             WHERE i.id = :id {$whereActive} {$whereSource}
             LIMIT 1"
        );
        $headerStmt->execute([':id' => (int)$inspectionId]);
        $header = $headerStmt->fetch(PDO::FETCH_ASSOC);
        if (!$header) {
            return null;
        }

        $detailsStmt = $this->conexion->prepare(
            "SELECT d.id,
                    d.pallet_number,
                    d.sample,
                    d.variety_id,
                    " . ($this->hasColumn('inspection_details', 'source_folio_id') ? "d.source_folio_id" : "NULL AS source_folio_id") . ",
                    " . ($this->hasColumn('inspection_details', 'handling_type') ? "d.handling_type" : "'CONV' AS handling_type") . "
             FROM inspection_details d
             WHERE d.inspection_id = :inspection_id
             ORDER BY d.id ASC"
        );
        $detailsStmt->execute([':inspection_id' => (int)$inspectionId]);
        $details = $detailsStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($details as &$detail) {
            $detailId = (int)$detail['id'];
            $defectValues = [];
            if ($this->tableExists('inspection_defect_values')) {
                $stmt = $this->conexion->prepare("SELECT defect_id, value FROM inspection_defect_values WHERE inspection_detail_id = :id");
                $stmt->execute([':id' => $detailId]);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $dv) {
                    $defectValues[(string)$dv['defect_id']] = (float)$dv['value'];
                }
            }

            $caliberValues = [];
            if ($this->tableExists('inspection_caliber_values')) {
                $stmt = $this->conexion->prepare("SELECT caliber_id, value FROM inspection_caliber_values WHERE inspection_detail_id = :id");
                $stmt->execute([':id' => $detailId]);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $cv) {
                    $caliberValues[(string)$cv['caliber_id']] = (float)$cv['value'];
                }
            }

            $measurement = ['weight' => 0, 'temperature' => 0, 'brix' => 0, 'baxlo' => 0, 'average' => 0];
            if ($this->tableExists('inspection_measurements')) {
                $stmt = $this->conexion->prepare("SELECT weight, temperature, brix, baxlo, average FROM inspection_measurements WHERE inspection_detail_id = :id LIMIT 1");
                $stmt->execute([':id' => $detailId]);
                $m = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($m) {
                    $measurement = [
                        'weight' => (float)($m['weight'] ?? 0),
                        'temperature' => (float)($m['temperature'] ?? 0),
                        'brix' => (float)($m['brix'] ?? 0),
                        'baxlo' => (float)($m['baxlo'] ?? 0),
                        'average' => (float)($m['average'] ?? 0),
                    ];
                }
            }

            $photos = [];
            if ($this->tableExists('calidad_inspection_detail_photos')) {
                $stmt = $this->conexion->prepare("SELECT photo_name, photo_comment FROM calidad_inspection_detail_photos WHERE inspection_detail_id = :id ORDER BY id ASC");
                $stmt->execute([':id' => $detailId]);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $index => $photoRow) {
                    $photos[] = [
                        'slot' => $index,
                        'name' => (string)($photoRow['photo_name'] ?? ''),
                        'comment' => (string)($photoRow['photo_comment'] ?? ''),
                        'file_input' => 'photo_' . $index
                    ];
                }
            }

            $detail['defects'] = $defectValues;
            $detail['calibers'] = $caliberValues;
            $detail['measurement'] = $measurement;
            $detail['photos'] = $photos;
        }
        unset($detail);

        return ['header' => $header, 'details' => $details];
    }

    private function validatePayload(array $payload)
    {
        $required = ['reception_id', 'producer_id', 'inspector_id', 'inspection_date', 'product', 'total_pallets', 'details'];
        foreach ($required as $field) {
            if (!isset($payload[$field]) || $payload[$field] === '') {
                throw new InvalidArgumentException("Campo obligatorio: {$field}");
            }
        }
        if (!is_array($payload['details']) || count($payload['details']) === 0) {
            throw new InvalidArgumentException('Debe ingresar al menos un pallet.');
        }
        foreach ($payload['details'] as $i => $detail) {
            if (!is_array($detail)) {
                throw new InvalidArgumentException('Detalle inválido en línea #' . ($i + 1) . '.');
            }
            if (trim((string)($detail['pallet_number'] ?? '')) === '') {
                throw new InvalidArgumentException('Debe ingresar pallet en detalle #' . ($i + 1) . '.');
            }
        }
    }

    private function getDefectType($defectId)
    {
        $stmt = $this->conexion->prepare("SELECT defect_type FROM defects WHERE id = :id");
        $stmt->execute([':id' => (int)$defectId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['defect_type'] : 'minor';
    }


    private function tableExists($table)
    {
        $stmt = $this->conexion->prepare("SHOW TABLES LIKE :table");
        $stmt->execute([':table' => $table]);
        return (bool)$stmt->fetch(PDO::FETCH_NUM);
    }

    private function hasColumn($table, $column)
    {
        $stmt = $this->conexion->prepare("SHOW COLUMNS FROM `{$table}` LIKE :column");
        $stmt->execute([':column' => $column]);
        return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function resolveCategoryId($score)
    {
        $stmt = $this->conexion->prepare("SELECT id FROM categories WHERE :score BETWEEN min_score AND max_score AND is_active = 1 ORDER BY id LIMIT 1");
        $stmt->execute([':score' => $score]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : null;
    }

    private function recordExistsByColumn($table, $column, $id)
    {
        $stmt = $this->conexion->prepare("SELECT 1 FROM `{$table}` WHERE `{$column}` = :id LIMIT 1");
        $stmt->execute([':id' => (int)$id]);
        return (bool)$stmt->fetchColumn();
    }

    private function findVarietyIdByName($name)
    {
        $normalizedName = trim((string)$name);
        if ($normalizedName === '') {
            return 0;
        }
        $stmt = $this->conexion->prepare("SELECT id FROM varieties WHERE is_active = 1 AND LOWER(name) = LOWER(:name) LIMIT 1");
        $stmt->execute([':name' => $normalizedName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : 0;
    }

    private function resolveVarietyIdForInspection($sourceVarietyId, $sourceVarietyName = '')
    {
        $candidateId = (int)$sourceVarietyId;
        if ($candidateId > 0 && $this->recordExistsByColumn('varieties', 'id', $candidateId)) {
            return $candidateId;
        }

        if ($candidateId > 0 && $this->tableExists('fruta_vespecies')) {
            $stmt = $this->conexion->prepare("SELECT NOMBRE_VESPECIES name FROM fruta_vespecies WHERE ID_VESPECIES = :id LIMIT 1");
            $stmt->execute([':id' => $candidateId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!empty($row['name'])) {
                $mapped = $this->findVarietyIdByName($row['name']);
                if ($mapped > 0) {
                    return $mapped;
                }
                $created = $this->ensureVarietyExists($row['name']);
                if ($created > 0) {
                    return $created;
                }
            }
        }

        if ($sourceVarietyName !== '') {
            $byName = $this->findVarietyIdByName($sourceVarietyName);
            if ($byName > 0) {
                return $byName;
            }
            return $this->ensureVarietyExists($sourceVarietyName);
        }
        return 0;
    }

    private function ensureVarietyExists($name)
    {
        $normalizedName = trim((string)$name);
        if ($normalizedName === '') {
            return 0;
        }
        $existing = $this->findVarietyIdByName($normalizedName);
        if ($existing > 0) {
            return $existing;
        }
        $stmt = $this->conexion->prepare("INSERT INTO varieties (name, is_active) VALUES (:name, 1)");
        $stmt->execute([':name' => $normalizedName]);
        return (int)$this->conexion->lastInsertId();
    }

    private function resolveExistingColumn($table, array $columns)
    {
        foreach ($columns as $column) {
            if ($this->hasColumn($table, $column)) {
                return $column;
            }
        }
        return null;
    }

    private function getFolioSourceData($folioId)
    {
        if (!$this->tableExists('fruta_exiexportacion')) {
            return null;
        }
        $stmt = $this->conexion->prepare(
            "SELECT exi.ID_EXIEXPORTACION folio_id,
                    IFNULL(exi.CANTIDAD_ENVASE_EXIEXPORTACION, 0) sample_size,
                    exi.ID_VESPECIES variety_id_source,
                    IFNULL(v.NOMBRE_VESPECIES, '') variety_name
             FROM fruta_exiexportacion exi
             LEFT JOIN fruta_vespecies v ON v.ID_VESPECIES = exi.ID_VESPECIES
             WHERE exi.ID_EXIEXPORTACION = :folio
             LIMIT 1"
        );
        $stmt->execute([':folio' => (int)$folioId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function normalizeHeader($text)
    {
        $value = strtolower(trim((string)$text));
        $value = str_replace(['á','é','í','ó','ú','ñ'], ['a','e','i','o','u','n'], $value);
        $value = str_replace(['.', ',', '-', '/', '\\', '(', ')'], ' ', $value);
        $value = preg_replace('/\s+/', '_', $value);
        return trim($value, '_');
    }

    private function getExcelValue(array $row, array $aliases)
    {
        foreach ($aliases as $alias) {
            $needle = $this->normalizeHeader($alias);
            foreach ($row as $column => $value) {
                if ($this->normalizeHeader($column) === $needle) {
                    return $value;
                }
            }
        }
        return null;
    }

    private function parseDecimal($value)
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_numeric($value)) {
            return (float)$value;
        }
        $text = trim((string)$value);
        $text = str_replace(['%', ' '], '', $text);
        if (substr_count($text, ',') > 0 && substr_count($text, '.') > 0) {
            $text = str_replace('.', '', $text);
            $text = str_replace(',', '.', $text);
        } else {
            $text = str_replace(',', '.', $text);
        }
        return is_numeric($text) ? (float)$text : 0.0;
    }

    private function parseExcelDate($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        $text = trim((string)$value);
        if ($text === '') {
            return date('Y-m-d');
        }
        $time = strtotime($text);
        return $time ? date('Y-m-d', $time) : date('Y-m-d');
    }
}
