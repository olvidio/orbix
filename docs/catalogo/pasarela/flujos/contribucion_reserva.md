---
id: "pasarela.contribucion_reserva.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "pasarela"
nombre: "Flujo - Gestionar Contribucion Reserva"
capacidad: "pasarela.contribucion_reserva.gestionar"
pantallas_principales: []
fragmentos: ["pasarela.pantalla.contribucion_reserva_ajax"]
acciones: ["listar"]
endpoints: ["/src/pasarela/contribucion_reserva_lista"]
estado_revision: "generado"
---

# Flujo - Gestionar Contribucion Reserva

Propuesta generada automaticamente desde la capacidad `pasarela.contribucion_reserva.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona ContribucionReservaLista. Devuelve el listado del parámetro contribucion_reserva listo para serializar. Estructura: {default, excepciones: [{id_tipo_activ, etiqueta, valor}]}.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `pasarela.pantalla.contribucion_reserva_ajax`

## Escenarios Inferidos

### Listar

Pasos propuestos:
1. Abrir la pantalla principal del flujo.
2. Rellenar los filtros visibles si los hay.
3. Ejecutar la accion de busqueda/listado.
4. Revisar el listado mostrado en pantalla.

Endpoints asociados:
- `/src/pasarela/contribucion_reserva_lista`

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

- `/src/pasarela/contribucion_reserva_lista`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
