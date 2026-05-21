---
id: "dbextern.ver_listas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Ver Listas"
capacidad: "dbextern.ver_listas.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/ver_listas_datos"]
estado_revision: "generado"
---

# Flujo - Gestionar Ver Listas

Propuesta generada automaticamente desde la capacidad `dbextern.ver_listas.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona VerListas. Obtiene la lista de personas BDU sin unir y los posibles matches Orbix.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_listas`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl`
- `form.id`
- `form.id_nom_listas`
- `form.id_orbix`
- `form.region`
- `form.tipo_persona`
- `html.mov`
- `post.dl`
- `post.id`
- `post.mov`
- `post.region`
- `post.tipo_persona`

Acciones JavaScript:
- `button:<`
- `fnjs_crear`
- `fnjs_crear_todos`
- `fnjs_enviar_formulario`
- `fnjs_submit`
- `fnjs_unir`

## Endpoints Del Flujo

- `/src/dbextern/ver_listas_datos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
