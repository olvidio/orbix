---
id: "asistentes.asistente_mover.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "asistentes"
nombre: "Flujo - Gestionar Asistente Mover"
capacidad: "asistentes.asistente_mover.gestionar"
pantallas_principales: []
fragmentos: ["asistentes.pantalla.asistente_mover"]
acciones: ["obtener_datos"]
endpoints: ["/src/asistentes/asistente_mover_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Asistente Mover

Propuesta generada automaticamente desde la capacidad `asistentes.asistente_mover.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona AsistenteMover. JSON para {.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `asistentes.pantalla.asistente_mover`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.guardar`
- `html.observ`

Acciones JavaScript:
- `fnjs_mover_cerrar`
- `fnjs_mover_guardar`

## Endpoints Del Flujo

- `/src/asistentes/asistente_mover_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
