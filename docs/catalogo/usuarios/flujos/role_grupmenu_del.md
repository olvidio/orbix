---
id: "usuarios.role_grupmenu_del.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Role Grupmenu Del"
capacidad: "usuarios.role_grupmenu_del.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.role_form"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/role_grupmenu_del"]
estado_revision: "revisado"
---

# Flujo - Role Grupmenu Del

## Objetivo De Usuario

Quita asociación grupmenu↔rol por id_item.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_form`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
