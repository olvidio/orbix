---
id: "usuarios.usuario.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Buscar un usuario"
capacidad: "usuarios.usuario.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_lista"]
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_lista"]
acciones: ["eliminar", "guardar", "listar", "ver_formulario"]
endpoints: ["/src/usuarios/usuario_eliminar", "/src/usuarios/usuario_form", "/src/usuarios/usuario_guardar", "/src/usuarios/usuario_lista"]
estado_revision: "revisado"
---

# Flujo - Usuario

## Objetivo De Usuario

Dar de alta, consultar, modificar y eliminar usuarios web de la delegación, y asignarles grupos de permisos y permisos propios.

## Punto De Entrada

Menú **sistema > usuarios web > lista usuarios** (Pills2: ADMIN LOCAL > usuarios web > lista usuarios).

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_lista`

## Escenarios Inferidos

### Buscar un usuario

Pasos propuestos:
1. Entrar en **sistema > usuarios web > lista usuarios**.
2. Escribir parte del nombre en **Nombre** y pulsar **Buscar**, o usar **Buscar en todas las columnas** sobre la tabla.
3. Pulsar el login del usuario para abrir su ficha.

Endpoints asociados:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

### Crear un usuario

Pasos propuestos:
1. En la lista, pulsar **NUEVO USUARIO**.
2. Rellenar **Login** y elegir el **Role** (admin, casa, centro sv, director, oficial_dl, p-sacd, supervisor).
3. Escribir un **Password** (debe cumplir los requisitos de seguridad; si se deja vacío en un usuario existente no se cambia).
4. Marcar **Cambio password** si se quiere obligar a cambiarlo al primer acceso, y **¿tiene doble factor activado?** si procede.
5. Escribir el **Email** y pulsar **guardar datos usuario**.
6. Al guardar aparece la ficha completa con grupos y permisos.

Endpoints asociados:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

### Modificar un usuario

Pasos propuestos:
1. Abrir la ficha desde la lista.
2. Cambiar login, nombre a mostrar, role, email o las casillas, y pulsar **guardar datos usuario**.
3. Para cambiar la contraseña, escribir la nueva en **Password** (se comprueba su fortaleza antes de guardar).

Endpoints asociados:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

### Asignar grupos de permisos

Pasos propuestos:
1. En la ficha, apartado **grupos**, pulsar **añadir un grupo de permisos** y, en la tabla, **añadir** en la fila del grupo (se asigna al instante).
2. Para retirarlo, pulsar **quitar de un grupo de permisos** y **quitar** en la fila del grupo.
3. Aviso de la pantalla: si el usuario tiene varios grupos, sus permisos deberían ser independientes porque no hay preferencia entre ellos.

Endpoints asociados:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

### Eliminar un usuario

Pasos propuestos:
1. En la lista, marcar el usuario y pulsar el botón de eliminar.
2. Confirmar el mensaje «¿Está seguro que desea borrar este usuario?».

Endpoints asociados:
- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_usuario`
- `form.password`
- `form.sel`
- `form.username`
- `form.usuario`
- `html.btn_ok`
- `html.cambio_password`
- `html.has_2fa`
- `html.password`
- `post.id_sel`
- `post.id_usuario`
- `post.que`
- `post.quien`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`
- `post.username`

Acciones JavaScript:
- `fnjs_actualizar`
- `fnjs_add_grup`
- `fnjs_buscar`
- `fnjs_chk_passwd`
- `fnjs_del_grup`
- `fnjs_eliminar`
- `fnjs_enviar`
- `fnjs_enviar_formulario`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_left_side_hide`
- `fnjs_lst_add_grup`
- `fnjs_lst_del_grup`
- `fnjs_mas_casas`
- `fnjs_solo_uno`
- `fnjs_update_div`

## Endpoints Del Flujo

- `/src/usuarios/usuario_eliminar`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_lista`

## Errores Conocidos

- `Usuario no encontrado`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** sistema > usuarios web > lista usuarios
- **Pills2:** ADMIN LOCAL > usuarios web > lista usuarios
