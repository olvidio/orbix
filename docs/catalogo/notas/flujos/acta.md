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
estado_revision: "generado"
---

# Flujo - Gestionar Acta

Propuesta generada automaticamente desde la capacidad `notas.acta.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Acta. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.acta_select`
- `notas.pantalla.acta_ver`

## Escenarios Inferidos

### Crear

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

- `/src/notas/acta_eliminar`
- `/src/notas/acta_nueva`

## Errores Conocidos

- ``No se encuentra el acta``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
