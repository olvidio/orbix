---
id: "pasarela.activacion_default.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Activacion Default"
capacidad: "pasarela.activacion_default.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.activacion_ajax"]
acciones: ["guardar", "obtener_datos"]
endpoints: ["/src/pasarela/activacion_default_data", "/src/pasarela/activacion_default_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Activacion Default

Propuesta generada automaticamente desde la capacidad `pasarela.activacion_default.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActivacionDefault. Actualiza el valor por defecto del parámetro fecha_activacion. Devuelve solo el valor por defecto del parámetro fecha_activacion, para alimentar el formulario form_default desde el frontend.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.activacion_ajax`

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
- `form.activacion`
- `form.default`
- `form.extendida`
- `form.iactividad_val`
- `form.iasistentes_val`
- `form.id_tipo_activ`
- `form.inom_tipo_val`
- `form.isfsv_val`
- `post.activacion`
- `post.default`
- `post.id_tipo_activ`
- `post.que`
- `post.sactividad`
- `post.sasistentes`
- `post.snom_tipo`

Acciones JavaScript:
- `fnjs_modificar_activacion`
- `fnjs_modificar_activacion_default`

## Endpoints Del Flujo

- `/src/pasarela/activacion_default_data`
- `/src/pasarela/activacion_default_guardar`

## Errores Conocidos

- ``Falta valor por defecto``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
