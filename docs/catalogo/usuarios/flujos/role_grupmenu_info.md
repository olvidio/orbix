---
id: "usuarios.role_grupmenu_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Ver los grupos disponibles"
capacidad: "usuarios.role_grupmenu_info.gestionar"
pantallas_principales: ["usuarios.pantalla.role_grupmenu"]
fragmentos: ["usuarios.pantalla.role_grupmenu"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/role_grupmenu_info"]
estado_revision: "revisado"
---

# Flujo - Role Grupmenu Info

## Objetivo De Usuario

Mostrar los grupos de menús que se pueden añadir a un rol.

## Punto De Entrada

Botón **añadir grup menu** en la ficha del rol.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_grupmenu`

## Escenarios Inferidos

### Ver los grupos disponibles

Pasos propuestos:
1. En la ficha del rol, pulsar **añadir grup menu**.
2. Aparece la lista de grupos de menús que el rol aún no tiene.

Endpoints asociados:
- `/src/usuarios/role_grupmenu_info`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `post.id_role`
- `post.sel`

Acciones JavaScript:
- `fnjs_add_grupmenu`

## Endpoints Del Flujo

- `/src/usuarios/role_grupmenu_info`

## Errores Conocidos

- `Rol no encontrado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
