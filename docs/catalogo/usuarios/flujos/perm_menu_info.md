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
estado_revision: "revisado"
---

# Flujo - Perm Menu Info

## Objetivo De Usuario

Carga formulario modal de permiso menú (nuevo o edición).

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

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

- `Grupo no encontrado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
