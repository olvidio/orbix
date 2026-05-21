---
id: "actividadplazas.resumen_plazas.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Resumen Plazas"
capacidad: "actividadplazas.resumen_plazas.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/resumen_plazas_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Resumen Plazas

Propuesta generada automaticamente desde la capacidad `actividadplazas.resumen_plazas.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ResumenPlazas. Datos del resumen de plazas por actividad (calendario/cedidas/conseguidas/disponibles/ocupadas por dl) + opciones del desplegable para "ceder" y flags publicado/otra_dl.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.resumen_plazas`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_activ`
- `form.num_plazas`
- `form.region_dl`
- `html.btn_ok`
- `html.num_plazas`
- `html.refresh`
- `post.id_activ`
- `post.nom_activ`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/actividadplazas/resumen_plazas_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
