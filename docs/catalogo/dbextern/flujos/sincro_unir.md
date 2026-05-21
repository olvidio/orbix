---
id: "dbextern.sincro_unir.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Sincro Unir"
capacidad: "dbextern.sincro_unir.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas", "dbextern.pantalla.ver_orbix"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_unir"]
estado_revision: "generado"
---

# Flujo - Gestionar Sincro Unir

Propuesta generada automaticamente desde la capacidad `dbextern.sincro_unir.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UnirPersonaUseCase. Vincula una persona de BDU con una persona de Orbix.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_listas`
- `dbextern.pantalla.ver_orbix`

## Escenarios Inferidos

### Ejecutar

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
- `fnjs_unir_bdu`

## Endpoints Del Flujo

- `/src/dbextern/sincro_unir`

## Errores Conocidos

- ``hay un error, no se ha guardado``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
