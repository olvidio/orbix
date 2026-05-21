---
id: "ubis.teleco_editar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "ubis"
nombre: "Flujo - Gestionar Teleco Editar"
capacidad: "ubis.teleco_editar.gestionar"
pantallas_principales: []
fragmentos: ["ubis.pantalla.teleco_editar"]
acciones: ["ejecutar"]
endpoints: ["/src/ubis/teleco_editar"]
estado_revision: "generado"
---

# Flujo - Gestionar Teleco Editar

Propuesta generada automaticamente desde la capacidad `ubis.teleco_editar.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona TelecoEditar. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `ubis.pantalla.teleco_editar`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_desc_teleco`
- `form.id_tipo_teleco`
- `form.mod`
- `form.num_teleco`
- `form.observ`
- `html.mod`
- `html.num_teleco`
- `html.observ`
- `post.id_ubi`
- `post.mod`
- `post.obj_pau`
- `post.s_pkey`
- `post.sel`

Acciones JavaScript:
- `fnjs_actualizar_descripcion`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/ubis/teleco_editar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
