---
id: "cartaspresentacion.pantalla.cartas_presentacion"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "cartaspresentacion"
nombre: "Cartas Presentacion"
controller: "frontend/cartaspresentacion/controller/cartas_presentacion.php"
vistas: ["frontend/cartaspresentacion/view/cartas_presentacion.phtml"]
fragmentos_frontend: ["frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php", "frontend/cartaspresentacion/controller/cartas_presentacion_form.php"]
endpoints: ["/src/cartaspresentacion/cartas_presentacion_shell_data", "/src/cartaspresentacion/ubis_lista_data", "/src/cartaspresentacion/poblaciones_data", "/src/cartaspresentacion/carta_presentacion_form_data", "/src/cartaspresentacion/carta_presentacion_update", "/src/cartaspresentacion/carta_presentacion_eliminar"]
capacidades: ["cartaspresentacion.cartas_presentacion_shell.gestionar"]
campos: ["html.buscar", "html.tipo_lista", "html.poblacion_sel"]
acciones: ["fnjs_cerrar", "fnjs_construir_desplegable", "fnjs_eliminar_cp", "fnjs_guardar_cp", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_poblacion", "fnjs_ver", "fnjs_ver_ubi"]
estado_revision: "revisado"
---

# Cartas Presentacion

Pantalla principal del módulo: selección dl/regiones + población, listado AJAX de centros con estado
de carta de presentación y modal de modificación.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/cartaspresentacion/controller/cartas_presentacion.php`

## Vistas Relacionadas

- `frontend/cartaspresentacion/view/cartas_presentacion.phtml`

## Fragmentos Frontend Relacionados

- `frontend/cartaspresentacion/controller/cartas_presentacion_ubis_lista.php` (`fnjs_ver`)
- `frontend/cartaspresentacion/controller/cartas_presentacion_form.php` (`fnjs_modificar`)

## Endpoints Usados

- `/src/cartaspresentacion/cartas_presentacion_shell_data` (bootstrap al cargar)
- `/src/cartaspresentacion/ubis_lista_data` vía `cartas_presentacion_ubis_lista.php` (`fnjs_ver`)
- `/src/cartaspresentacion/poblaciones_data` (`fnjs_poblacion`)
- `/src/cartaspresentacion/carta_presentacion_form_data` vía `cartas_presentacion_form.php` (`fnjs_modificar`)
- `/src/cartaspresentacion/carta_presentacion_update` (`fnjs_guardar_cp`)
- `/src/cartaspresentacion/carta_presentacion_eliminar` (`fnjs_eliminar_cp`)

## Capacidades Relacionadas

- `cartaspresentacion.cartas_presentacion_shell.gestionar`

## Campos Detectados

- `html.tipo_lista` — desplegable «según dl» (`get_dl` / `get_r`)
- `html.poblacion_sel` — desplegable de población (solo en `get_dl`)
- `html.buscar` — botón buscar

## Acciones Detectadas

- `fnjs_poblacion` — recarga desplegable de población al cambiar `tipo_lista`
- `fnjs_ver` — busca centros y pinta listado en `#ficha2`
- `fnjs_modificar` — abre modal con formulario de carta
- `fnjs_guardar_cp` — guarda carta y refresca listado
- `fnjs_eliminar_cp` — elimina carta tras confirmación
- `fnjs_ver_ubi` — abre ficha del centro en `#ficha2`
- `fnjs_cerrar` — cierra modal `#div_modificar`

## Manual De Usuario

Flujo habitual en **modificar**:

1. Elegir «según dl» (mi delegación o regiones) y, si aplica, la población.
2. Pulsar **buscar**: aparece la tabla de centros con columnas director / centro / carta / dirección.
3. Pulsar **director** en una fila para abrir el modal y rellenar nombre, teléfono, e-mail, zona y
   observaciones; **guardar** crea o actualiza la carta.
4. Si la carta ya existe, la columna «carta de presentación» muestra **sí, quitar** para eliminarla.
5. Pulsar el nombre del centro abre su ficha (`home_ubis`).

## Ruta de menú

- **Legacy:** scdl > direcciones > cartas presentacion > modificar
- **Pills2:** scdl > direcciones > cartas presentacion > modificar
