---
id: "usuarios.role_grupmenu_add.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Role Grupmenu Add"
capacidad: "usuarios.role_grupmenu_add.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.role_grupmenu"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/role_grupmenu_add"]
estado_revision: "revisado"
---

# Flujo - Role Grupmenu Add

## Objetivo De Usuario

Asocia grupmenu a rol (tokens sel `id_role#id_grupmenu`).

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

- `/src/usuarios/role_grupmenu_add`

## Errores Conocidos

- `hay un error, no se ha guardado`
- `debe seleccionar uno`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
