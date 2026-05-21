---
id: "actividadestudios.acta_notas_definitivas_grabar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar Acta Notas Definitivas Grabar"
capacidad: "actividadestudios.acta_notas_definitivas_grabar.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.acta_notas"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadestudios/acta_notas_definitivas_grabar"]
estado_revision: "generado"
---

# Flujo - Gestionar Acta Notas Definitivas Grabar

Propuesta generada automaticamente desde la capacidad `actividadestudios.acta_notas_definitivas_grabar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActaNotasDefinitivasGrabar. Convierte las matriculas/notas borrador en PersonaNota definitivas (rama que=3 del legacy apps/actividadestudios/controller/acta_notas_update.php).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.acta_notas`

## Escenarios Inferidos

### Ejecutar

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

- `/src/actividadestudios/acta_notas_definitivas_grabar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
