---
id: "personas.stgr_cambio.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "personas"
nombre: "Flujo - Gestionar Stgr Cambio"
capacidad: "personas.stgr_cambio.gestionar"
pantallas_principales: []
fragmentos: ["personas.pantalla.stgr_cambio"]
acciones: ["obtener_datos"]
endpoints: ["/src/personas/stgr_cambio_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Stgr Cambio

Propuesta generada automaticamente desde la capacidad `personas.stgr_cambio.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona StgrCambio. Endpoint JSON: datos para el formulario stgr_cambio.phtml.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `personas.pantalla.stgr_cambio`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.nivel_stgr`
- `html.guardar`
- `post.id_nom`
- `post.id_tabla`
- `post.sel`

Acciones JavaScript:
- `fnjs_guardar_stgr`

## Endpoints Del Flujo

- `/src/personas/stgr_cambio_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
