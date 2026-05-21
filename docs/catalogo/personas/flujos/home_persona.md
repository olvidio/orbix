---
id: "personas.home_persona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Gestionar Home Persona"
capacidad: "personas.home_persona.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.home_persona"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/home_persona_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Home Persona

Propuesta generada automaticamente desde la capacidad `personas.home_persona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona HomePersona. Endpoint JSON: datos para la pantalla home_persona.phtml.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `personas.pantalla.home_persona`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_nom`
- `post.id_tabla`
- `post.obj_pau`
- `post.sel`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/personas/home_persona_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
