---
id: "usuarios.usuario_grupo_lst.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Elegir un grupo para añadir"
capacidad: "usuarios.usuario_grupo_lst.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_grupo_lst"]
fragmentos: ["usuarios.pantalla.usuario_grupo_lst"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_grupo_lst"]
estado_revision: "revisado"
---

# Flujo - Usuario Grupo Lst

## Objetivo De Usuario

Mostrar los grupos de permisos que se pueden añadir a un usuario (los que aún no tiene).

## Punto De Entrada

Botón **añadir un grupo de permisos** de la ficha de usuario.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_grupo_lst`

## Escenarios Inferidos

### Elegir un grupo para añadir

Pasos propuestos:
1. En la ficha del usuario, pulsar **añadir un grupo de permisos**.
2. Aparece la lista de grupos disponibles, sin los que el usuario ya tiene.
3. En la tabla (columnas **grupo**, **sección**, **acción**) pulsar **añadir** en la fila del grupo: se asigna al instante (ver «Asignar un grupo a un usuario»).

Endpoints asociados:
- `/src/usuarios/usuario_grupo_lst`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.id_usuario`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/usuarios/usuario_grupo_lst`

## Errores Conocidos

- `Usuario no encontrado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
