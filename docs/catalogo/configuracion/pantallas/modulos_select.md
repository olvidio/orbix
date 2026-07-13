---
id: "configuracion.pantalla.modulos_select"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "configuracion"
nombre: "Definir módulos"
controller: "frontend/configuracion/controller/modulos_select.php"
vistas: ["frontend/configuracion/view/modulos_select.phtml"]
fragmentos_frontend: ["frontend/configuracion/controller/modulos_form.php", "frontend/configuracion/controller/modulos_update.php"]
endpoints: ["/src/configuracion/modulos_select_data"]
capacidades: ["configuracion.modulos_select.gestionar"]
campos: ["html.mod", "html.refresh", "post.sel", "post.id_sel", "post.scroll_id"]
acciones: ["fnjs_actualizar", "fnjs_eliminar", "fnjs_enviar_formulario", "fnjs_modificar", "fnjs_nuevo", "fnjs_solo_uno"]
estado_revision: "revisado"
---

# Definir módulos

Listado de módulos del esquema Orbix: nombre, descripción, módulos requeridos y aplicaciones
requeridas. Permite alta, modificación y baja desde botones de fila y enlace «añadir módulo».

## Tipo

- Subtipo: `pantalla_principal`
- Controller: `frontend/configuracion/controller/modulos_select.php`

## Vistas Relacionadas

- `frontend/configuracion/view/modulos_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/configuracion/controller/modulos_form.php` — ficha alta/edición
- `frontend/configuracion/controller/modulos_update.php` — proxy AJAX guardar/eliminar

## Endpoints Usados

- `/src/configuracion/modulos_select_data` — payload de tabla (`a_cabeceras`, `a_botones`, `a_valores`, hash lista)

## Flujo en pantalla

1. Carga `modulos_select_data` y pinta tabla con selección (`sel[]` = `id_mod#`).
2. **Modificar** / **Eliminar** (requieren una fila seleccionada vía `fnjs_solo_uno`).
3. **Añadir módulo** → `modulos_form.php` con `mod=nuevo`.
4. Tras eliminar → refresco AJAX del listado (`refresh=1`).

## Manual De Usuario

1. Abrir desde menú Configuración > definir módulos.
2. Revisar el listado de módulos y sus dependencias (módulos/apps requeridos).
3. Para crear: enlace «añadir módulo».
4. Para editar o borrar: marcar una fila y pulsar el botón correspondiente.
5. Al eliminar, confirmar el diálogo; si falla (p. ej. dependencias), se muestra el error del backend.

## Ruta de menú

- **Legacy:** sistema > Configuración > definir módulos
- **Pills2:** sistema > Configuración > definir módulos; ADMIN GLOBAL > Configuración > definir módulos
