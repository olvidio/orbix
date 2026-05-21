---
id: "actividadestudios.acta_notas_matricula.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Acta Notas Matricula"
capacidad: "actividadestudios.acta_notas_matricula.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.acta_notas"]
acciones: ["guardar"]
endpoints: ["/src/actividadestudios/acta_notas_matricula_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Acta Notas Matricula

Propuesta generada automaticamente desde la capacidad `actividadestudios.acta_notas_matricula.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActaNotasMatricula. Guarda el borrador de notas sobre cada matricula (rama que=1 del legacy apps/actividadestudios/controller/acta_notas_update.php).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.acta_notas`

## Escenarios Inferidos

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.acta_nota`
- `form.form_preceptor`
- `form.id_nom`
- `form.nota_max`
- `form.nota_num`
- `html.form_preceptor[]`
- `html.id_nom[]`
- `html.que`
- `post.id_activ`
- `post.id_asignatura`
- `post.id_nivel`
- `post.id_pau`
- `post.opcional`
- `post.primary_key_s`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_enviar_formulario`
- `fnjs_guardar_nota`
- `fnjs_guardar_tessera`
- `fnjs_imprimir`
- `fnjs_left_side_hide`
- `fnjs_nota`

## Endpoints Del Flujo

- `/src/actividadestudios/acta_notas_matricula_guardar`

## Errores Conocidos

- ``Hay una nota mayor que el máximo``
- ``hay un error, no se ha guardado``
- ``no se puede definir cursada con preceptor``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
