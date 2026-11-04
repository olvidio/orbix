---
id: "usuarios.role_grupmenu_add.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Añadir grupos de menús a un rol"
capacidad: "usuarios.role_grupmenu_add.gestionar"
pantallas_principales: ["usuarios.pantalla.role_grupmenu"]
fragmentos: ["usuarios.pantalla.role_grupmenu"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/role_grupmenu_add"]
estado_revision: "revisado"
---

# Flujo - Role Grupmenu Add

## Objetivo De Usuario

Añadir a un rol los grupos de menús marcados.

## Punto De Entrada

Ficha del rol, ventana **añadir nuevo grupMenu**.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.role_grupmenu`

## Escenarios Inferidos

### Añadir grupos de menús a un rol

Pasos propuestos:
1. Pulsar **añadir grup menu** en la ficha del rol.
2. Marcar uno o varios grupos de la lista.
3. Guardar. Si no se marca ninguno aparece «debe seleccionar uno».

Endpoints asociados:
- `/src/usuarios/role_grupmenu_add`

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
