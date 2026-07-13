---
id: "notas.pantalla.acta_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Acta Ver"
controller: "frontend/notas/controller/acta_ver.php"
vistas: ["frontend/notas/view/acta_ver.phtml"]
fragmentos_frontend: ["frontend/notas/controller/acta_pdf_delete.php", "frontend/notas/controller/acta_pdf_upload.php", "frontend/notas/controller/acta_ver.php"]
endpoints: ["/src/notas/acta_modificar", "/src/notas/acta_nueva", "/src/notas/acta_ver_form_data", "/src/notas/asignaturas_search", "/src/notas/examinadores_search"]
capacidades: ["notas.acta.gestionar", "notas.acta_modificar.gestionar", "notas.acta_ver.gestionar", "notas.asignaturas_search.gestionar", "notas.examinadores_search.gestionar"]
campos: ["form.acta_pdf", "form.search", "html.acta", "html.acta_pdf", "html.examinadores[]", "html.id_asignatura", "html.refresh"]
acciones: ["fnjs_add_examinador", "fnjs_autocomplete_exam", "fnjs_cmb_acta", "fnjs_eliminar_pdf", "fnjs_enviar_formulario", "fnjs_guardar_acta", "fnjs_nueva_convocatoria", "fnjs_upload_pdf"]
estado_revision: "revisado"
---

# Acta Ver

Formulario de cabecera de acta (asignatura, actividad, fechas, libro, tribunal, PDF). Fragmento embebido desde `acta_select` o `actividadestudios/acta_notas`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/acta_ver.php`

## Vistas Relacionadas

- `frontend/notas/view/acta_ver.phtml`

## Fragmentos Frontend Relacionados

- `frontend/notas/controller/acta_pdf_delete.php`
- `frontend/notas/controller/acta_pdf_upload.php`
- `frontend/notas/controller/acta_ver.php`

## Endpoints Usados

- `/src/notas/acta_modificar`
- `/src/notas/acta_nueva`
- `/src/notas/acta_ver_form_data`
- `/src/notas/asignaturas_search`
- `/src/notas/examinadores_search`

## Capacidades Relacionadas

- `notas.acta.gestionar`
- `notas.acta_modificar.gestionar`
- `notas.acta_ver.gestionar`
- `notas.asignaturas_search.gestionar`
- `notas.examinadores_search.gestionar`

## Campos Detectados

- `form.acta_pdf`
- `form.search`
- `html.acta`
- `html.acta_pdf`
- `html.examinadores[]`
- `html.id_asignatura`
- `html.refresh`

## Acciones Detectadas

- `fnjs_add_examinador`
- `fnjs_autocomplete_exam`
- `fnjs_cmb_acta`
- `fnjs_eliminar_pdf`
- `fnjs_enviar_formulario`
- `fnjs_guardar_acta`
- `fnjs_nueva_convocatoria`
- `fnjs_upload_pdf`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.
