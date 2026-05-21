---
id: "actividadplazas.plazas_balance_que.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadplazas"
nombre: "Flujo - Gestionar Plazas Balance Que"
capacidad: "actividadplazas.plazas_balance_que.gestionar"
pantallas_principales: []
fragmentos: ["actividadplazas.pantalla.plazas_balance_que"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadplazas/plazas_balance_que_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Plazas Balance Que

Propuesta generada automaticamente desde la capacidad `actividadplazas.plazas_balance_que.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PlazasBalanceQue. Datos para la pantalla plazas_balance_que (opciones dl + id_tipo_activ).

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `actividadplazas.pantalla.plazas_balance_que`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl`
- `form.id_tipo_activ`
- `post.id_tipo_activ`
- `post.sactividad`
- `post.sasistentes`

Acciones JavaScript:
- `fnjs_comparativa`

## Endpoints Del Flujo

- `/src/actividadplazas/plazas_balance_que_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
