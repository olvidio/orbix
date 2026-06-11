# Guía de Desarrollo Frontend para Orbix

## Convenciones de JavaScript
- **Prefijo de funciones**: Todas las funciones de JavaScript globales deben empezar por el prefijo `fnjs_` (ej. `fnjs_guardar`, `fnjs_actualizar`). Esto ayuda a identificarlas rápidamente como funciones propias del sistema Orbix.
- **Ubicación**: Las funciones comunes se encuentran en `scripts/index.js.php`. Las funciones específicas de una vista deben integrarse en el bloque `<script>` del archivo `.phtml` correspondiente.

## Llamadas AJAX Modernas (JSON)
Las nuevas funcionalidades y las recargas parciales de vista deben usar respuestas JSON (`ContestarJson`) para una gestión uniforme de errores.

### Helpers globales (`scripts/index.js.php`)

| Función | Uso |
|---------|-----|
| `fnjs_parse_rta(rta, errorPrefix?)` | Extrae y parsea `data` del envelope `{success, mensaje?, data}` |
| `fnjs_ajax_json({ url, data, onSuccess, onFail?, errorPrefix? })` | POST con `dataType: 'json'` y manejo de errores |
| `fnjs_ajax_json_html({ url, data, target, onDone? })` | Inyecta `data.html` en un selector |
| `fnjs_ajax_json_alert({ url, data, onDone? })` | Muestra `data.text` / `data.mensaje` en un alert |

`fnjs_update_div` / `fnjs_mostra_resposta` también entienden el envelope JSON y extraen `data.html` si el backend devuelve JSON.

### Helpers PHP (`frontend/shared/helpers/ajax_json_support.php`)

| Función | Uso |
|---------|-----|
| `ajax_json_response($error, $data)` | Respuesta genérica ContestarJson |
| `ajax_json_html($html, $error)` | `{ success: true, data: { html: "..." } }` |
| `ajax_json_render_phtml($ns, $template, $vars, $error)` | Renderiza `.phtml` y envía como JSON html |

### Patrón Estándar (`fnjs_guardar`)
```javascript
fnjs_guardar = function (formulario) {
    let url = '<?= src\shared\config\AppUrlConfig::getApiBaseUrl() ?>/src/modulo/update_endpoint';
    $.ajax({
        url: url,
        type: 'post',
        data: $(formulario).serialize(),
        dataType: 'json'
    })
    .done(function (respuesta) {
        if (respuesta.success) {
            // Acción tras éxito (ej. volver atrás)
            <?= $oPosicion->js_atras(1); ?>
        } else {
            // Mostrar mensaje de error enviado por el backend
            alert(respuesta.mensaje || 'Error desconocido');
        }
    })
    .fail(function (xhr, status, error) {
        alert('Error en la conexión con el servidor: ' + error);
    });
}
```

### Reglas:
- Especificar siempre `dataType: 'json'` (o usar `fnjs_ajax_json*`).
- El backend debe devolver `{ success, mensaje?, data }` vía `ContestarJson` o `ajax_json_*`.
- Fragmentos HTML parciales: `ajax_json_html()` / `data.html` en cliente con `fnjs_ajax_json_html`.
- Datos estructurados (tablas construidas en cliente): `/src/...` + `fnjs_parse_rta` (ver `activ_sacd.phtml`).
- No usar `dataType: 'html'` ni respuestas texto plano en nuevos endpoints.
- No usar el patrón antiguo de `$(formulario).one("submit", ...)`.

## Interactividad en Tablas
Para asegurar una experiencia consistente entre SlickGrid y las tablas HTML:

### Selección y Resaltado
- Usar el atributo `onclick="fnjs_clic_fila(this, event)"` en las filas (`<tr>`).
- La función `fnjs_clic_fila` (en `index.js.php`) automatiza:
    - El marcado/desmarcado de checkboxes (`input.sel`).
    - El resaltado visual de la fila (`.selected_row`).
    - La gestión de "selección única" si no hay checkboxes.

### Paso de Datos
- Las filas deben incluir el atributo `data-json` con los datos completos de la entidad:
  `<tr data-json='<?= htmlspecialchars(json_encode($fila), ENT_QUOTES, 'UTF-8') ?>' ...>`
- Al hacer clic en una fila, se dispara el evento `rowSelected` sobre la tabla. Las vistas pueden capturarlo para obtener los IDs necesarios (ej. para asignaciones):
```javascript
$('#contenedor-tabla').on('rowSelected', 'table', function(e, rowData) {
    variableGlobal = rowData;
});
```

## Persistencia de Estado (UI State)
- El estado de la UI (scroll, selección) se guarda automáticamente en `sessionStorage` antes de navegar.
- Se usa la URL de la página (`refe`) como clave para evitar colisiones.
- La restauración ocurre automáticamente al volver atrás si la tabla usa la clase `web\Lista`.
