---
id: "usuarios.grupo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Buscar un grupo"
capacidad: "usuarios.grupo.gestionar"
pantallas_principales: ["usuarios.pantalla.grupo_lista"]
fragmentos: ["usuarios.pantalla.grupo_lista"]
acciones: ["eliminar", "guardar", "listar"]
endpoints: ["/src/usuarios/grupo_eliminar", "/src/usuarios/grupo_guardar", "/src/usuarios/grupo_lista"]
estado_revision: "revisado"
---

# Flujo - Grupo

## Objetivo De Usuario

Administrar los grupos de permisos que se asignan a los usuarios: crear, renombrar y eliminar.

## Punto De Entrada

Menú **sistema > usuarios web > grupos** (Pills2: ADMIN LOCAL > usuarios web > grupos).

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.grupo_lista`

## Escenarios Inferidos

### Buscar un grupo

Pasos propuestos:
1. Entrar en **grupos**.
2. Escribir parte del nombre en **Nombre** y pulsar **Buscar**.

Endpoints asociados:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

### Crear un grupo

Pasos propuestos:
1. Pulsar **NUEVO GRUPO**.
2. Escribir el nombre. Convención de la pantalla: poner entre paréntesis si el grupo se refiere a menús (m), permisos (p) o ambos (m+p).
3. Guardar.

Endpoints asociados:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

### Cambiar el nombre de un grupo

Pasos propuestos:
1. Pulsar el grupo en la lista, modificar el nombre y guardar.

Endpoints asociados:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

### Eliminar un grupo

Pasos propuestos:
1. Marcar el grupo en la lista y pulsar eliminar.
2. Confirmar. Si hay un error aparece «hay un error, no se ha eliminado».

Endpoints asociados:
- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.sel`
- `form.username`
- `html.btn_ok`
- `post.id_sel`
- `post.scroll_id`
- `post.stack`
- `post.username`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_buscar`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_left_side_hide`
- `fnjs_modificar`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/usuarios/grupo_eliminar`
- `/src/usuarios/grupo_guardar`
- `/src/usuarios/grupo_lista`

## Errores Conocidos

- `Grupo no encontrado`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** sistema > usuarios web > grupos
- **Pills2:** ADMIN LOCAL > usuarios web > grupos
