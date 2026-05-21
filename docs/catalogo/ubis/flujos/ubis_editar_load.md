---
id: "ubis.ubis_editar_load.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Ubis Editar Load"
capacidad: "ubis.ubis_editar_load.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_editar"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_editar_load_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ubis Editar Load

Propuesta generada automaticamente desde la capacidad `ubis.ubis_editar_load.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UbisEditarLoad. Carga ficha ubis (centro/casa) para frontend/ubis/controller/ubis_editar.php sin repositorios en el frontend.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_editar`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.active`
- `html.cdc`
- `html.n_buzon`
- `html.nombre_ubi`
- `html.num_cartas`
- `html.num_cartas_mensuales`
- `html.num_habit_indiv`
- `html.num_pi`
- `html.num_sacd`
- `html.observ`
- `html.plazas`
- `html.plazas_min`
- `html.que`
- `html.sf`
- `html.status`
- `html.sv`
- `html.tipo_ubi`
- `post.dl`
- `post.id_ubi`
- `post.nombre_ubi`
- `post.nuevo`
- `post.obj_pau`
- `post.region`
- `post.tipo_ubi`

Acciones JavaScript:
- `fnjs_eliminar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/ubis_editar_load_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
