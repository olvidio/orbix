---
id: "ubis.centros_form_labor.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Centros Form Labor"
capacidad: "ubis.centros_form_labor.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.centros_form_labor"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/centros_form_labor"]
estado_revision: "generado"
---

# Flujo - Gestionar Centros Form Labor

Propuesta generada automaticamente desde la capacidad `ubis.centros_form_labor.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Centros. Datos comunes para los formularios de centro dl (labor / num / plazas). Los tres formularios muestran sobre un mismo centro un subconjunto de campos distinto según el modo indicado.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_labor`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.tipo_ctr`
- `form.tipo_labor`
- `get.id_ubi`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/centros_form_labor`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
