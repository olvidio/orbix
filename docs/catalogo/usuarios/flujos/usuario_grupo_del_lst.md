---
id: "usuarios.usuario_grupo_del_lst.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Ver los grupos que se pueden quitar"
capacidad: "usuarios.usuario_grupo_del_lst.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_grupo_del_lst"]
fragmentos: ["usuarios.pantalla.usuario_grupo_del_lst"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_grupo_del_lst"]
estado_revision: "revisado"
---

# Flujo - Usuario Grupo Del Lst

## Objetivo De Usuario

Mostrar los grupos que el usuario ya tiene, para poder quitarle alguno.

## Punto De Entrada

Botón **quitar de un grupo de permisos** de la ficha de usuario.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_grupo_del_lst`

## Escenarios Inferidos

### Ver los grupos que se pueden quitar

Pasos propuestos:
1. En la ficha del usuario, pulsar **quitar de un grupo de permisos**.
2. Aparece la lista de grupos asignados.
3. En la tabla pulsar **quitar** en la fila del grupo: se retira al instante, sin pedir confirmación (ver «Quitar un grupo a un usuario»).

Endpoints asociados:
- `/src/usuarios/usuario_grupo_del_lst`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_usuario`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/usuario_grupo_del_lst`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
