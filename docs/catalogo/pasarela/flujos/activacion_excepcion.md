---
id: "pasarela.activacion_excepcion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Activacion Excepcion"
capacidad: "pasarela.activacion_excepcion.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.activacion_ajax", "pasarela.pantalla.activacion_lista"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/pasarela/activacion_excepcion_eliminar", "/src/pasarela/activacion_excepcion_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Activacion Excepcion

Propuesta generada automaticamente desde la capacidad `pasarela.activacion_excepcion.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ActivacionExcepcion. Elimina una excepción del parámetro fecha_activacion para un id_tipo_activ concreto. Inserta o actualiza una excepción del parámetro fecha_activacion para un id_tipo_activ concreto.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.activacion_ajax`
- `pasarela.pantalla.activacion_lista`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/pasarela/activacion_excepcion_eliminar`

### Guardar

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
- `form.que`
- `form.valor`
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

- `/src/pasarela/activacion_excepcion_eliminar`
- `/src/pasarela/activacion_excepcion_guardar`

## Errores Conocidos

- ``Falta id_tipo_activ``
- ``Falta valor de activación``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
