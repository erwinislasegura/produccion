# Revisión de seguridad por módulo (Smartberry)

Fecha: 2026-02-23
Tipo: revisión estática de código (PHP)

## Alcance
- Módulos funcionales revisados: `fruta`, `material`, `productor`, `exportadora`, `estadistica`.
- Capa transversal revisada: `assest/controlador`, `assest/config`, `assest/ajax`.

## Metodología breve
1. Búsqueda de entradas no validadas (`$_GET`, `$_POST`, `$_REQUEST`).
2. Detección de SQL construido por concatenación.
3. Revisión de autenticación, sesión y carga de archivos.
4. Clasificación por severidad (Alta, Media, Baja).

## Hallazgos transversales (impactan a todos los módulos)

### 1) Riesgo alto: SQL Injection por concatenación en múltiples ADO
**Evidencia**
- `USUARIO_ADO` concatena variables en consultas de lectura y login.
- `RECEPCIONMP_ADO` concatena parámetros en búsquedas y borrados.

**Ejemplos**
- `SELECT ... WHERE ID_USUARIO = '".$ID."'`.
- `LIKE '%".$NOMBRE."%'`.
- `DELETE ... WHERE ID_RECEPCION=".$id`.

**Riesgo**
- Exfiltración/alteración de datos, bypass de controles y daño de integridad.

**Recomendación**
- Migrar todas las consultas a `prepare + bindParam/bindValue`.
- Prohibir concatenación directa en SQL (regla de CI/linter).

### 2) Riesgo alto: autenticación vulnerable (inyección + comparación insegura)
**Evidencia**
- En `iniciarSession` y `iniciarSession2` se interpolan usuario/clave en SQL.
- Se permite autenticación con hash SHA2 o contraseña en texto plano.
- Hay una condición `OR` que debilita la lógica de autenticación.

**Riesgo**
- Login bypass, ataques de credential stuffing más efectivos y compromiso de cuentas.

**Recomendación**
- Usar `password_hash()` y `password_verify()`.
- Eliminar fallback de contraseña en texto plano.
- Parametrizar completamente consultas de autenticación.

### 3) Riesgo alto: ausencia de controles CSRF en operaciones críticas
**Evidencia**
- En formularios de mantenimiento se procesan acciones `GUARDAR/EDITAR/ELIMINAR/HABILITAR` por `$_REQUEST` sin token anti-CSRF.

**Riesgo**
- Cambios de estado no autorizados si el usuario autenticado visita un enlace/sitio malicioso.

**Recomendación**
- Implementar token CSRF por sesión y validarlo en POST.
- Restringir operaciones de mutación a `POST` (no `GET/REQUEST`).

### 4) Riesgo medio-alto: carga de archivos con validación insuficiente
**Evidencia**
- Validación de tipo/tamaño en `SUBIR.php` tiene lógica defectuosa (`&&`), permitiendo combinaciones no deseadas.
- Se asignan permisos `0777` al archivo subido.

**Riesgo**
- Carga de archivos no esperados, abuso de almacenamiento y superficie para ejecución/lectura indebida.

**Recomendación**
- Validar MIME real con `finfo_file` y extensión permitida.
- Corregir condición de validación (`tipo inválido OR tamaño excedido`).
- Evitar `0777` (usar permisos mínimos necesarios).

### 5) Riesgo medio: configuración sensible y hardening
**Evidencia**
- Configuración DB usa `root` y password vacía en código.

**Riesgo**
- Escalamiento lateral si el entorno queda expuesto.

**Recomendación**
- Mover credenciales a variables de entorno/secret manager.
- Usar usuario DB dedicado con privilegios mínimos.

## Evaluación por módulo

## Módulo `fruta`
- **Estado:** Riesgo Alto.
- **Motivo:** alto volumen de uso de `$_REQUEST/$_GET/$_POST` en vistas, dependiente de controladores con SQL concatenado y operaciones sin CSRF.
- **Prioridad:**
  1. Token CSRF + POST obligatorio en mantenedores.
  2. Parametrización en controladores asociados a fruta.
  3. Validación centralizada de entrada.

## Módulo `material`
- **Estado:** Riesgo Alto.
- **Motivo:** patrón de mantenedores similar a fruta (mutaciones por `$_REQUEST`) y dependencia de capa ADO compartida.
- **Prioridad:** igual que `fruta`.

## Módulo `productor`
- **Estado:** Riesgo Medio.
- **Motivo:** menor superficie detectada en vistas, pero hereda riesgos de autenticación/ADO/configuración compartida.
- **Prioridad:** reforzar autenticación y saneamiento en endpoints compartidos.

## Módulo `exportadora`
- **Estado:** Riesgo Alto.
- **Motivo:** múltiples acciones de alta/edición/baja/habilitación usando `$_REQUEST`; posibles redirecciones construidas desde request en algunos flujos.
- **Prioridad:**
  1. CSRF + validación estricta de parámetros de ruta/redirección.
  2. Reducción de lógica crítica en vistas (pasarla a controladores seguros).

## Módulo `estadistica`
- **Estado:** Riesgo Medio.
- **Motivo:** menor cantidad de mutaciones, pero aún con entradas directas y dependencia de autenticación/DB compartida.
- **Prioridad:** endurecimiento de validación y reutilización del middleware de seguridad común.

## Plan de mitigación recomendado (30/60/90 días)
- **30 días:**
  - Corregir login (`password_hash/password_verify`, consultas parametrizadas).
  - Implementar CSRF global.
  - Corregir `SUBIR.php` (validación + permisos).
- **60 días:**
  - Eliminar concatenación SQL en ADO críticos (usuarios, recepción, despacho, notas, etc.).
  - Agregar validadores de entrada por tipo (int, fecha, enum, texto).
- **90 días:**
  - Pruebas SAST/DAST en CI.
  - Logging de seguridad y alertas (intentos de login, errores SQL, cambios críticos).
  - Revisión de privilegios DB por módulo.

## Comandos usados para la revisión
- `rg -n '\$_(GET|POST|REQUEST|FILES|COOKIE)' ...`
- `rg -n '(SELECT|INSERT|UPDATE|DELETE).*(\$|\{)' ...`
- `rg -n '\$_REQUEST\[|\$_GET\[|PHP_SELF' ...`
- revisión manual de archivos clave en `assest/controlador` y `assest/config`.
