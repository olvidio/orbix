---
id: "menus.grupmenu_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "menus"
nombre: "Flujo - Gestionar Grupmenu Info"
capacidad: "menus.grupmenu_info.gestionar"
pantallas_principales: []
fragmentos: ["menus.pantalla.grupmenu_form"]
acciones: ["ejecutar"]
endpoints: ["/src/menus/grupmenu_info"]
estado_revision: "generado"
---

# Flujo - Gestionar Grupmenu Info

Propuesta generada automaticamente desde la capacidad `menus.grupmenu_info.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona GrupmenuInfo. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `menus.pantalla.grupmenu_form`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.grupmenu`
- `form.orden`
- `form.que`
- `post.id_grupmenu`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_cancelar`
- `fnjs_guardar`

## Endpoints Del Flujo

- `/src/menus/grupmenu_info`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
