---
id: "ubis.ubis_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Ubis Tabla"
capacidad: "ubis.ubis_tabla.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.ubis_tabla"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/ubis_tabla_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Ubis Tabla

Propuesta generada automaticamente desde la capacidad `ubis.ubis_tabla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UbisTabla. Normaliza los parámetros de entrada del request.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.ubis_tabla`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `html.b_mas`
- `post.stack`

Acciones JavaScript:
- `fnjs_borrar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_trasladar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/ubis_tabla_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
