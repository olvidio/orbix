---
id: "casas.calendario_ubi_resumen.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Calendario Ubi Resumen"
capacidad: "casas.calendario_ubi_resumen.gestionar"
pantallas_principales: []
fragmentos: ["casas.pantalla.calendario_ubi_resumen", "casas.pantalla.calendario_ubi_resumen_body"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/calendario_ubi_resumen_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Calendario Ubi Resumen

Propuesta generada automaticamente desde la capacidad `casas.calendario_ubi_resumen.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CalendarioUbiResumen. Datos del estudio económico de una casa (calendario_ubi_resumen).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.calendario_ubi_resumen`
- `casas.pantalla.calendario_ubi_resumen_body`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.G`
- `form.id_ubi`
- `form.inc_cantidad`
- `form.inc_t`
- `form.seccion`
- `form.year`
- `html.G`
- `html.id_ubi`
- `html.inc_t`
- `html.seccion`
- `html.year`
- `post.G`
- `post.id_ubi`
- `post.inc_t`
- `post.seccion`

Acciones JavaScript:
- `button:grabar tarifas`
- `button:resumen sf`
- `button:resumen sv`
- `fnjs_guardar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/casas/calendario_ubi_resumen_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
