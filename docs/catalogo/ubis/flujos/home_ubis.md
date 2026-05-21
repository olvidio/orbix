---
id: "ubis.home_ubis.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Home Ubis"
capacidad: "ubis.home_ubis.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.home_ubis"]
acciones: ["obtener_datos"]
endpoints: ["/src/ubis/home_ubis_data"]
estado_revision: "generado"
---

# Flujo - Gestionar Home Ubis

Propuesta generada automaticamente desde la capacidad `ubis.home_ubis.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona HomeUbis. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.home_ubis`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.bloque`
- `post.id_ubi`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_left_side_show`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/home_ubis_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
