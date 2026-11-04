---
id: "usuarios.role_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Abrir la ficha de un rol"
capacidad: "usuarios.role_info.gestionar"
pantallas_principales: ["usuarios.pantalla.role_form"]
fragmentos: ["usuarios.pantalla.role_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/role_info"]
estado_revision: "revisado"
---

# Flujo - Role Info

## Objetivo De Usuario

Cargar la ficha de un rol: sus datos, si se puede editar y los grupos de menús que ya tiene.

## Punto De Entrada

Pulsar un rol en la lista de roles.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_form`

## Escenarios Inferidos

### Abrir la ficha de un rol

Pasos propuestos:
1. En **lista de roles**, pulsar el rol.
2. Se muestran sus datos (sf, sv, pau, sacd, casas, dmz) y la tabla de grupos de menús asignados.

Endpoints asociados:
- `/src/usuarios/role_info`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.dmz`
- `form.pau`
- `form.que`
- `form.role`
- `form.sel`
- `form.sf`
- `form.sv`
- `html.dmz`
- `html.que`
- `html.role`
- `html.sf`
- `html.sv`
- `post.id_role`
- `post.que`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_add_grupmenu`
- `fnjs_del_grupmenu`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/usuarios/role_info`

## Errores Conocidos

- `Usuario no encontrado`
- `Rol no encontrado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
