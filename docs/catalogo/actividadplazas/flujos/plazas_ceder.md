---
id: "actividadplazas.plazas_ceder.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Plazas Ceder"
capacidad: "actividadplazas.plazas_ceder.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.resumen_plazas"]
acciones: ["ejecutar"]
endpoints: ["/src/actividadplazas/plazas_ceder"]
estado_revision: "generado"
---

# Flujo - Gestionar Plazas Ceder

Propuesta generada automaticamente desde la capacidad `actividadplazas.plazas_ceder.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlazasCeder. Actualiza el array cedidas de ActividadPlazasDl para ceder (o quitar) plazas de mi_dele a otra dl en una actividad.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.resumen_plazas`

## Escenarios Inferidos

### Ejecutar

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

- `/src/actividadplazas/plazas_ceder`

## Errores Conocidos

- ``faltan parametros id_activ / region_dl``
- ``hay un error, no se ha guardado``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
