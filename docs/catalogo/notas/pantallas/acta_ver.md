---
id: "notas.pantalla.acta_ver"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Acta Ver"
controller: "frontend/notas/controller/acta_ver.php"
vistas: ["frontend/notas/view/acta_ver.phtml"]
fragmentos_frontend: ["frontend/notas/controller/acta_pdf_delete.php", "frontend/notas/controller/acta_pdf_upload.php", "frontend/notas/controller/acta_ver.php"]
endpoints: ["/src/notas/acta_modificar", "/src/notas/acta_nueva", "/src/notas/acta_ver_form_data", "/src/notas/acta_ver_notas_listado_data", "/src/notas/acta_ver_add_persona_form_data", "/src/notas/acta_ver_add_persona", "/src/notas/asignaturas_search", "/src/notas/examinadores_search"]
capacidades: ["notas.acta.gestionar", "notas.acta_modificar.gestionar", "notas.acta_ver.gestionar", "notas.asignaturas_search.gestionar", "notas.examinadores_search.gestionar"]
campos: ["form.acta_pdf", "form.search", "html.acta", "html.acta_pdf", "html.examinadores[]", "html.id_asignatura", "html.refresh", "html.id_nom", "html.nota_num", "html.nota_max"]
acciones: ["fnjs_add_examinador", "fnjs_autocomplete_exam", "fnjs_cmb_acta", "fnjs_eliminar_pdf", "fnjs_enviar_formulario", "fnjs_guardar_acta", "fnjs_nueva_convocatoria", "fnjs_upload_pdf"]
estado_revision: "revisado"
---

# Acta Ver

Formulario de cabecera de acta (asignatura, actividad, fechas, libro, tribunal, PDF). Fragmento embebido desde `acta_select` o `actividadestudios/acta_notas`.

## Casos particulares (contexto UI)

- **Standalone** (`$notas` y `$Qnotas` vacíos, no modo nueva, acta con asignatura): muestra listado solo lectura (`acta_ver_notas_listado_data`) y, si `permiso===3` y sin PDF firmado, formulario añadir alumno (`acta_ver_add_persona_*`).
- **Embebido en actividad** (`$notas` / `$Qnotas` no vacío): no listado ni alta de alumno aquí (las notas van por el flujo de actividad/estudios).
- **Ámbito rstgr/r**: cabecera en solo lectura; no alta de alumno.

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
- `/src/notas/acta_ver_notas_listado_data`
- `/src/notas/acta_ver_add_persona_form_data`
- `/src/notas/acta_ver_add_persona`
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
