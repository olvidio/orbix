---
id: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Gestionar Cambio Usuario Objeto Pref Propiedades"
capacidad: "cambios.cambio_usuario_objeto_pref_propiedades.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref_propiedades"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_objeto_pref_propiedades_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Cambio Usuario Objeto Pref Propiedades

Propuesta generada automaticamente desde la capacidad `cambios.cambio_usuario_objeto_pref_propiedades.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CambioUsuarioObjetoPrefPropiedades. Endpoint JSON: listado de propiedades configurables del objeto indicado, preseleccionadas segun las preferencias ya guardadas.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cambios.pantalla.usuario_avisos_pref_propiedades`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.<?= htmlspecialchars($Qobjeto, ENT_QUOTES, `
- `html.id_item_usuario_objeto_prop`
- `html.salida`
- `post.id_item_usuario_objeto`
- `post.objeto`

Acciones JavaScript:
- `fnjs_modificar`
- `fnjs_selectAll`

## Endpoints Del Flujo

- `/src/cambios/cambio_usuario_objeto_pref_propiedades_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
