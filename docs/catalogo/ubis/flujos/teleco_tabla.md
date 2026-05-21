---
id: "ubis.teleco_tabla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Teleco Tabla"
capacidad: "ubis.teleco_tabla.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.teleco_tabla"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/teleco_tabla"]
estado_revision: "generado"
---

# Flujo - Gestionar Teleco Tabla

Propuesta generada automaticamente desde la capacidad `ubis.teleco_tabla.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TelecoTabla. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_tabla`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.mod`
- `form.sel`
- `html.btn_new`
- `html.mod`
- `html.refresh`
- `post.id_ubi`
- `post.obj_pau`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_eliminar`
- `fnjs_enviar_formulario`
- `fnjs_modificar`
- `fnjs_nuevo`
- `fnjs_solo_uno`

## Endpoints Del Flujo

- `/src/ubis/teleco_tabla`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
