---
id: "pasarela.contribucion_reserva_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Contribucion Reserva Default"
capacidad: "pasarela.contribucion_reserva_default.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.contribucion_reserva_ajax"]
acciones: ["guardar", "obtener_datos"]
endpoints: ["/src/pasarela/contribucion_reserva_default_data", "/src/pasarela/contribucion_reserva_default_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Contribucion Reserva Default

Propuesta generada automaticamente desde la capacidad `pasarela.contribucion_reserva_default.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ContribucionReservaDefault. Actualiza el valor por defecto del parámetro contribucion_reserva. Devuelve solo el valor por defecto del parámetro contribucion_reserva, para alimentar el formulario form_default desde el frontend.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.contribucion_reserva_ajax`

## Escenarios Inferidos

### Guardar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.contribucion`
- `form.default`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `post.contribucion`
- `post.default`
- `post.id_tipo_activ`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

Acciones JavaScript:
- `fnjs_modificar`
- `fnjs_modificar_default`

## Endpoints Del Flujo

- `/src/pasarela/contribucion_reserva_default_data`
- `/src/pasarela/contribucion_reserva_default_guardar`

## Errores Conocidos

- ``Debe ser un numero entero del 1 al 100``
- ``Falta valor por defecto``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
