---
id: "usuarios.perm_menu_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Abrir el formulario de permiso de menú"
capacidad: "usuarios.perm_menu_info.gestionar"
pantallas_principales: ["usuarios.pantalla.perm_menu_form"]
fragmentos: ["usuarios.pantalla.perm_menu_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/perm_menu_info"]
estado_revision: "revisado"
---

# Flujo - Perm Menu Info

## Objetivo De Usuario

Cargar el formulario (nuevo o de edición) de un permiso de menú.

## Punto De Entrada

Botón de añadir o fila de la tabla de permisos de menú.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.perm_menu_form`

## Escenarios Inferidos

### Abrir el formulario de permiso de menú

Pasos propuestos:
1. En la ficha del usuario, pulsar añadir (o la fila a editar).
2. Se abre el formulario con el grupo de menús elegible.
3. Si el grupo ya no existe aparece «Grupo no encontrado».

Endpoints asociados:
- `/src/usuarios/perm_menu_info`

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
