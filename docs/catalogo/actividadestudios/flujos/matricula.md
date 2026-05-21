---
id: "actividadestudios.matricula.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Matricula"
capacidad: "actividadestudios.matricula.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.form_matriculas_de_una_persona", "actividadestudios.pantalla.matriculas_lista", "actividadestudios.pantalla.matriculas_pendientes"]
acciones: ["crear", "eliminar"]
endpoints: ["/src/actividadestudios/matricula_eliminar", "/src/actividadestudios/matricula_nueva"]
estado_revision: "generado"
---

# Flujo - Gestionar Matricula

Propuesta generada automaticamente desde la capacidad `actividadestudios.matricula.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Matricula. Crea una matricula. Elimina una o varias matriculas.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.form_matriculas_de_una_persona`
- `actividadestudios.pantalla.matriculas_lista`
- `actividadestudios.pantalla.matriculas_pendientes`

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
- `/src/actividadestudios/matricula_eliminar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_nom`
- `form.periodo`
- `form.year`
- `html.id_asignatura`
- `html.mod`
- `html.pau`
- `html.preceptor`
- `html.refresh`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.mod`
- `post.periodo`
- `post.sel`
- `post.stack`
- `post.year`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_borrar`
- `fnjs_buscar`
- `fnjs_cmb_opcional`
- `fnjs_cmb_preceptor`
- `fnjs_construir_desplegable`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_left_side_hide`
- `fnjs_solo_uno`
- `fnjs_update_div`
- `fnjs_ver_ca`

## Endpoints Del Flujo

- `/src/actividadestudios/matricula_eliminar`
- `/src/actividadestudios/matricula_nueva`

## Errores Conocidos

- ``falta id_activ o id_nom``
- ``hay un error, no se ha borrado``
- ``hay un error, no se ha guardado``
- ``no encuentro asignatura para ese nivel``
- ``no encuentro la matricula``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
