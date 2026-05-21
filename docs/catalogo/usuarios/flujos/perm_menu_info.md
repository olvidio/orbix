---
id: "usuarios.perm_menu_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Perm Menu Info"
capacidad: "usuarios.perm_menu_info.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.perm_menu_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/perm_menu_info"]
estado_revision: "generado"
---

# Flujo - Gestionar Perm Menu Info

Propuesta generada automaticamente desde la capacidad `usuarios.perm_menu_info.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona PermMenuInfo. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.perm_menu_form`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.menu_perm`
- `post.id_item`
- `post.id_usuario`
- `post.sel`

Acciones JavaScript:
- `fnjs_grabar`

## Endpoints Del Flujo

- `/src/usuarios/perm_menu_info`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
