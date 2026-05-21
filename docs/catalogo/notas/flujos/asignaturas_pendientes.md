---
id: "notas.asignaturas_pendientes.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "notas"
nombre: "Flujo - Gestionar Asignaturas Pendientes"
capacidad: "notas.asignaturas_pendientes.gestionar"
pantallas_principales: []
fragmentos: ["notas.pantalla.asignaturas_pendientes"]
acciones: ["obtener_datos"]
endpoints: ["/src/notas/asignaturas_pendientes_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Asignaturas Pendientes

Propuesta generada automaticamente desde la capacidad `notas.asignaturas_pendientes.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona AsignaturasPendientes. Datos para la pantalla asignaturas_pendientes (matriz alumnos × asignaturas). La UI (Lista, desplegable rstgr) se monta en el controlador frontend.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `notas.pantalla.asignaturas_pendientes`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl`
- `post.dl`

Acciones JavaScript:
- `fnjs_left_side_hide`

## Endpoints Del Flujo

- `/src/notas/asignaturas_pendientes_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
