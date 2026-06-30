# Listado de mejoras de seguridad aplicadas

## 1) Parametrización de consultas en autenticación y búsquedas de usuarios
**Archivo:** `assest/controlador/USUARIO_ADO.php`

Mejoras aplicadas:
- `verUsuario($ID)`: reemplazo de SQL concatenado por `:id` con `bindParam`.
- `BuscarUsuarioNombre($NOMBRE)`: reemplazo de `LIKE` concatenado por `:nombre` con binding.
- `ObtenerNombreCompleto($ID)`: reemplazo de `ID` concatenado por `:id` con binding.
- `iniciarSession($NOMBRE, $CONTRASENA)`: eliminación de interpolación de credenciales y consulta parametrizada.
- `iniciarSession2($NOMBRE, $CONTRASENA)`: mismo hardening y corrección de lógica con paréntesis en el `OR`.
- `iniciarSessionNIntentos($NOMBRE)`: reemplazo de nombre concatenado por parámetro `:nombre`.

Impacto:
- Reduce riesgo de SQL Injection en funciones críticas de autenticación y consulta de usuarios.

## 2) Endurecimiento de validación de URLs internas
**Archivo:** `assest/config/validarDatosUrl.php`

Mejoras aplicadas:
- Uso de operadores null-safe (`??`) para evitar notices por índices no definidos.
- Sanitización con `htmlspecialchars` antes de construir redirecciones en script.
- Validación explícita de parámetros `id` y `a` antes de permitir querystring operativa.

Impacto:
- Menor superficie para inyección en salida HTML/JS y comportamiento más robusto ante entradas incompletas.

## 3) Saneamiento de redirecciones accionadas por request
**Archivo:** `assest/config/datosUrl.php`

Mejoras aplicadas:
- Centralización en función `redirigirOperacionMantenedor()` para evitar duplicación insegura.
- Conversión de `ID` a entero estricto.
- Restricción de `URL` con `basename()` para evitar rutas arbitrarias.
- Construcción de URL con `sprintf` + `rawurlencode`.
- Escape de salida con `htmlspecialchars`.

Impacto:
- Disminuye riesgo de manipulación de rutas/redirecciones y mejora consistencia de navegación segura.

## 4) Corrección de validación de subida de archivos y permisos
**Archivo:** `assest/config/SUBIR.php`

Mejoras aplicadas:
- Corrección de lógica de validación de tipo/tamaño: ahora falla si **tipo inválido o tamaño inválido**.
- Validación de MIME permitidos mediante arreglo y `in_array(..., true)`.
- Ajuste de `strpos` con comparación estricta (`!== false`) para evitar falsos negativos.
- Cambio de permisos de archivo subido de `0777` a `0644`.

Impacto:
- Reduce riesgo por cargas no válidas y baja exposición por permisos excesivos en archivos subidos.
