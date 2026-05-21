---
id: "ubis.centros.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Centros"
capacidad: "ubis.centros.gestionar"
pantallas_principales: ["ubis.pantalla.centros_que"]
fragmentos: ["ubis.pantalla.centros_form_labor", "ubis.pantalla.centros_form_num", "ubis.pantalla.centros_form_plazas"]
acciones: ["crear_actualizar"]
endpoints: ["/src/ubis/centros_update"]
estado_revision: "generado"
---

# Flujo - Gestionar Centros

Propuesta generada automaticamente desde la capacidad `ubis.centros.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona Centros. Actualiza datos de centro DL (labor / num / plazas según POST).

## Punto De Entrada

- `ubis.pantalla.centros_que`

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.centros_form_labor`
- `ubis.pantalla.centros_form_num`
- `ubis.pantalla.centros_form_plazas`

## Escenarios Inferidos

### Crear Actualizar

Pasos propuestos:
1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Endpoints asociados:
- `/src/ubis/centros_update`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_ubi`
- `form.n_buzon`
- `form.num_cartas`
- `form.num_habit_indiv`
- `form.num_pi`
- `form.plazas`
- `form.que`
- `form.tipo_ctr`
- `form.tipo_labor`
- `get.id_ubi`
- `html.buscar`
- `post.id_ubi`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar`
- `fnjs_modificar`
- `fnjs_update_div`
- `fnjs_ver`

## Endpoints Del Flujo

- `/src/ubis/centros_update`

## Errores Conocidos

- ``Hay un error, no se ha guardado.``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
