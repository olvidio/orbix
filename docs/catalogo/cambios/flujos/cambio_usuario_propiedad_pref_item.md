---
id: "cambios.cambio_usuario_propiedad_pref_item.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "cambios"
nombre: "Flujo - Gestionar Cambio Usuario Propiedad Pref Item"
capacidad: "cambios.cambio_usuario_propiedad_pref_item.gestionar"
pantallas_principales: []
fragmentos: ["cambios.pantalla.usuario_avisos_pref_condicion"]
acciones: ["obtener_datos"]
endpoints: ["/src/cambios/cambio_usuario_propiedad_pref_item_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Cambio Usuario Propiedad Pref Item

Propuesta generada automaticamente desde la capacidad `cambios.cambio_usuario_propiedad_pref_item.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CambioUsuarioPropiedadPrefItem. Endpoint JSON: devuelve los datos de una condicion por id_item (si existe) y la lista de casas cuando la propiedad es id_ubi.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `cambios.pantalla.usuario_avisos_pref_condicion`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.objeto`
- `form.operador`
- `form.propiedad`
- `form.salida`
- `form.valor`
- `html.id_item`
- `html.objeto`
- `html.propiedad`
- `html.salida`
- `html.valor`
- `post.id_item`
- `post.objeto`
- `post.propiedad`

Acciones JavaScript:
- `fnjs_cerrar`
- `fnjs_guardar_cond`
- `fnjs_mas_casas`

## Endpoints Del Flujo

- `/src/cambios/cambio_usuario_propiedad_pref_item_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
