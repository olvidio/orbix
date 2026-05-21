---
id: "pasarela.contribucion_no_duerme_excepcion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Contribucion No Duerme Excepcion"
capacidad: "pasarela.contribucion_no_duerme_excepcion.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.contribucion_no_duerme_ajax", "pasarela.pantalla.contribucion_no_duerme_lista"]
acciones: ["eliminar", "guardar"]
endpoints: ["/src/pasarela/contribucion_no_duerme_excepcion_eliminar", "/src/pasarela/contribucion_no_duerme_excepcion_guardar"]
estado_revision: "generado"
---

# Flujo - Gestionar Contribucion No Duerme Excepcion

Propuesta generada automaticamente desde la capacidad `pasarela.contribucion_no_duerme_excepcion.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ContribucionNoDuermeExcepcion. Elimina una excepción del parámetro contribucion_no_duerme para un id_tipo_activ concreto. Inserta o actualiza una excepción del parámetro contribucion_no_duerme para un id_tipo_activ concreto.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.contribucion_no_duerme_ajax`
- `pasarela.pantalla.contribucion_no_duerme_lista`

## Escenarios Inferidos

### Eliminar

Pasos propuestos:
1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Endpoints asociados:
- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`

### Guardar

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
- `form.que`
- `form.valor`
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

- `/src/pasarela/contribucion_no_duerme_excepcion_eliminar`
- `/src/pasarela/contribucion_no_duerme_excepcion_guardar`

## Errores Conocidos

- ``Debe ser un numero entero del 1 al 100``
- ``Falta id_tipo_activ``
- ``Falta valor de contribución``

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
