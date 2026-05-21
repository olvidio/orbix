---
id: "actividadplazas.plazas_balance.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Plazas Balance"
capacidad: "actividadplazas.plazas_balance.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.plazas_balance_dl"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/plazas_balance_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Plazas Balance

Propuesta generada automaticamente desde la capacidad `actividadplazas.plazas_balance.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlazasBalance. Datos del grid comparativo A vs B (plazas concedidas y libres entre dos dl para un tipo de actividad).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.plazas_balance_dl`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.colName`
- `form.data`
- `post.dl`
- `post.id_tipo_activ`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadplazas/plazas_balance_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
