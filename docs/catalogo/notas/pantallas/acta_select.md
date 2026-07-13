---
id: "notas.pantalla.acta_select"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Acta Select"
controller: "frontend/notas/controller/acta_select.php"
vistas: ["frontend/notas/view/acta_select.phtml"]
fragmentos_frontend: ["frontend/notas/controller/acta_imprimir.php", "frontend/notas/controller/acta_select.php", "frontend/notas/controller/acta_ver.php"]
endpoints: ["/src/notas/acta_eliminar", "/src/notas/acta_select_data"]
capacidades: ["notas.acta.gestionar", "notas.acta_select.gestionar"]
campos: ["form.acta", "form.mod", "form.sel", "html.acta", "html.btn_ok", "html.mod", "html.refresh", "post.acta", "post.refresh", "post.stack", "post.titulo"]
acciones: ["fnjs_actualizar", "fnjs_descargar_pdf", "fnjs_eliminar", "fnjs_enviar", "fnjs_enviar_formulario", "fnjs_imprimir", "fnjs_left_side_hide", "fnjs_modificar", "fnjs_nuevo", "fnjs_solo_uno"]
estado_revision: "revisado"
---

# Acta Select

Listado de actas del curso o búsqueda por número, con acciones ver/modificar/eliminar/imprimir/descargar PDF según permisos de oficina `est`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/acta_select.php`

## Vistas Relacionadas

- `frontend/notas/view/acta_select.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/acta_imprimir.php`
- `frontend/notas/controller/acta_select.php`
- `frontend/notas/controller/acta_ver.php`

## Endpoints Usados

- `/src/notas/acta_eliminar`
- `/src/notas/acta_select_data`

## Capacidades Relacionadas

- `notas.acta.gestionar`
- `notas.acta_select.gestionar`

## Campos Detectados

- `form.acta`
- `form.mod`
- `form.sel`
- `html.acta`
- `html.btn_ok`
- `html.mod`
- `html.refresh`
- `post.acta`
- `post.refresh`
- `post.stack`
- `post.titulo`

## Acciones Detectadas

- `fnjs_actualizar`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Ruta de menú

- **Legacy:** vest > actas... > actas; stgr > actas > actas
- **Pills2:** ESTUDIOS > Actas y certificados > Actas; vest > actas... > actas; stgr > actas > actas

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
