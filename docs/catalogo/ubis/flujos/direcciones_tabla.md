---
id: "ubis.direcciones_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Direcciones Tabla"
capacidad: "ubis.direcciones_tabla.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.direcciones_tabla"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/direcciones_tabla"]
estado_revision: "generado"
---

# Flujo - Gestionar Direcciones Tabla

Propuesta generada automaticamente desde la capacidad `ubis.direcciones_tabla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona DireccionesTabla. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.direcciones_tabla`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.c_p`
- `post.ciudad`
- `post.id_ubi`
- `post.obj_dir`
- `post.pais`

Acciones JavaScript:
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/ubis/direcciones_tabla`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
