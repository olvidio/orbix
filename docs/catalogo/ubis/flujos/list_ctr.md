---
id: "ubis.list_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar List Ctr"
capacidad: "ubis.list_ctr.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.list_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/list_ctr_data"]
estado_revision: "generado"
---

# Flujo - Gestionar List Ctr

Propuesta generada automaticamente desde la capacidad `ubis.list_ctr.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ListCtr. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.list_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.loc`
- `form.que_lista`
- `form.sel`
- `post.loc`
- `post.que_lista`
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_cerrar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_limpiar`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`
- `fnjs_ver_dl`

## Endpoints Del Flujo

- `/src/ubis/list_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
