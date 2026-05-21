---
id: "dbextern.sincro_crear_todos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Gestionar Sincro Crear Todos"
capacidad: "dbextern.sincro_crear_todos.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas"]
acciones: ["ejecutar"]
endpoints: ["/src/dbextern/sincro_crear_todos"]
estado_revision: "generado"
---

# Flujo - Gestionar Sincro Crear Todos

Propuesta generada automaticamente desde la capacidad `dbextern.sincro_crear_todos.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CrearPersonaDesdeListasUseCase, CrearTodosDesdeListasUseCase. Crea una persona en Orbix desde la BDU y la vincula.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `dbextern.pantalla.ver_listas`

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

## Endpoints Del Flujo

- `/src/dbextern/sincro_crear_todos`

## Errores Conocidos

- ``hay un error, no se ha guardado``
- ``no se encontró la persona en la BDU``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
