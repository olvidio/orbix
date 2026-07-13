---
id: "notas.acta.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Acta"
capacidad: "notas.acta.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.acta_select", "notas.pantalla.acta_ver"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/notas/acta_eliminar", "/src/notas/acta_nueva"]
estado_revision: "revisado"
---

# Flujo - Gestionar Acta

Gestión del ciclo de vida de actas: listado, alta, edición, eliminación, PDF e impresión.

## Objetivo De Usuario

Ciclo completo de actas: listar en `acta_select`, abrir `acta_ver`, crear (`acta_nueva`), modificar (`acta_modificar`) o eliminar (`acta_eliminar`), con PDF e impresión.

## Punto De Entrada

Menú **vest > actas... > actas** / **ESTUDIOS > Actas y certificados > Actas** (`acta_select.php`). También accesible desde `actividadestudios` al abrir acta de una asignatura CA.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_select`
- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Crear

Pasos:
1. En `acta_select`, pulsar **añadir acta** (`fnjs_nuevo`).
2. Se abre `acta_ver` en modo nuevo; rellenar asignatura, actividad, fechas y tribunal.
3. Guardar (`fnjs_guardar_acta` → `acta_nueva`).

Endpoints asociados:
- `/src/notas/acta_nueva`
- `/src/notas/acta_ver_form_data`

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/notas/acta_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta`
- `form.acta_pdf`
- `form.mod`
- `form.search`
- `form.sel`
- `html.acta`
- `html.acta_pdf`
- `html.btn_ok`
- `html.examinadores[]`
- `html.id_asignatura`
- `html.mod`
- `html.refresh`
- `post.acta`
- `post.refresh`
- `post.stack`
- `post.titulo`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_add_examinador`
- `fnjs_autocomplete_exam`
- `fnjs_cmb_acta`
- `fnjs_descargar_pdf`
- `fnjs_eliminar`
- `fnjs_eliminar_pdf`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_guardar_acta`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_nueva_convocatoria`
- `fnjs_nuevo`
- `fnjs_solo_uno`
- `fnjs_upload_pdf`

## Endpoints Del Flujo

- `/src/notas/acta_select_data`
- `/src/notas/acta_ver_form_data`
- `/src/notas/acta_nueva`
- `/src/notas/acta_modificar`
- `/src/notas/acta_eliminar`

## Errores Conocidos

- ``No se encuentra el acta``

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice (fragmento/dossier)
- **Pills2:** sin entrada de menú en el índice (fragmento/dossier)
