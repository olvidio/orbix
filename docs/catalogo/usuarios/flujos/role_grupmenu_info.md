---
id: "usuarios.role_grupmenu_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Role Grupmenu Info"
capacidad: "usuarios.role_grupmenu_info.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.role_grupmenu"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/role_grupmenu_info"]
estado_revision: "revisado"
---

# Flujo - Role Grupmenu Info

## Objetivo De Usuario

Lista grupmenus disponibles para añadir a un rol.

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_grupmenu`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
