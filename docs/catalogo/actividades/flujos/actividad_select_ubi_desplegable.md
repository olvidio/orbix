---
id: "actividades.actividad_select_ubi_desplegable.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Gestionar Actividad Select Ubi Desplegable"
capacidad: "actividades.actividad_select_ubi_desplegable.gestionar"
pantallas_principales: ["actividades.pantalla.actividad_select_ubi"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_select_ubi_desplegable"]
estado_revision: "generado"
---

# Flujo - Gestionar Actividad Select Ubi Desplegable

Propuesta generada automaticamente desde la capacidad `actividades.actividad_select_ubi_desplegable.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActividadSelectUbi. Endpoint backend que devuelve las opciones (value => label) de los desplegables de la pantalla "seleccionar lugar para una actividad".

## Punto De Entrada

- `actividades.pantalla.actividad_select_ubi`

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dl_org`
- `form.entrada`
- `form.extendida`
- `form.filtro_lugar`
- `form.frm_4_nombre_ubi`
- `form.id_ubi_1`
- `form.isfsv`
- `form.lst_lugar`
- `form.modo`
- `form.nombre_ubi`
- `form.salida`
- `form.tipo`
- `html.b_buscar`

Acciones JavaScript:
- `fnjs_buscar`
- `fnjs_cargar_desplegable`
- `fnjs_construir_desplegable`
- `fnjs_enviar_form`
- `fnjs_lugar`

## Endpoints Del Flujo

- `/src/actividades/actividad_select_ubi_desplegable`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
