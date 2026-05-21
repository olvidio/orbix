---
id: "encargossacd.horario_update.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Gestionar Horario Update"
capacidad: "encargossacd.horario_update.gestionar"
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.horario_update"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/horario_update_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Horario Update

Propuesta generada automaticamente desde la capacidad `encargossacd.horario_update.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona EncargoHorario. Alta/edición/baja de horario de encargo (tabla encargo_horario).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.horario_update`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/encargossacd/horario_update_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
