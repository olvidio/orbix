---
id: "usuarios.role_grupmenu_del.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Quitar un grupo de menús"
capacidad: "usuarios.role_grupmenu_del.gestionar"
pantallas_principales: ["usuarios.pantalla.role_form"]
fragmentos: ["usuarios.pantalla.role_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/role_grupmenu_del"]
estado_revision: "revisado"
---

# Flujo - Role Grupmenu Del

## Objetivo De Usuario

Quitar un grupo de menús de un rol.

## Punto De Entrada

Tabla de grupos de menús de la ficha del rol.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_form`

## Escenarios Inferidos

### Quitar un grupo de menús

Pasos propuestos:
1. En la ficha del rol, elegir el grupo en la tabla.
2. Pulsar quitar y confirmar.
3. Si el registro ya no existe aparece «no existe el registro».

Endpoints asociados:
- `/src/usuarios/role_grupmenu_del`

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

- `/src/usuarios/role_grupmenu_del`

## Errores Conocidos

- `no existe el registro`
- `hay un error, no se ha eliminado`
- `debe seleccionar uno`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
