---
id: "personas.personas_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Gestionar Personas Editar"
capacidad: "personas.personas_editar.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.personas_editar"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/personas_editar_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Personas Editar

Propuesta generada automaticamente desde la capacidad `personas.personas_editar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PersonasEditar. Endpoint JSON: datos para la ficha personas_editar.phtml.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `personas.pantalla.personas_editar`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.apellido1`
- `post.id_nom`
- `post.nuevo`
- `post.obj_pau`
- `post.sel`
- `post.stack`
- `post.tabla`

Acciones JavaScript:
- `fnjs_act_ctr`

## Endpoints Del Flujo

- `/src/personas/personas_editar_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
