---
id: "usuarios.usuario_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Ver el resumen de un usuario en su ficha"
capacidad: "usuarios.usuario_info.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_form_mail", "usuarios.pantalla.usuario_form_pwd"]
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_form_mail", "usuarios.pantalla.usuario_form_pwd"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_info"]
estado_revision: "revisado"
---

# Flujo - Usuario Info

## Objetivo De Usuario

Calcular el resumen del usuario (grupos, login, correo) que se muestra en la cabecera de su ficha.

## Punto De Entrada

Sin entrada de menú propia: se usa desde otra pantalla o de forma automática.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_form_mail`
- `usuarios.pantalla.usuario_form_pwd`

## Escenarios Inferidos

### Ver el resumen de un usuario en su ficha

Pasos propuestos:
1. Abrir la ficha de un usuario desde **lista usuarios**.
2. La cabecera muestra el login, el correo y los grupos asignados; no hay que hacer nada más.
3. Si el usuario no existe aparece «Usuario no encontrado».

Endpoints asociados:
- `/src/usuarios/usuario_info`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.email`
- `form.enable_2fa`
- `form.id_usuario`
- `form.password`
- `form.password1`
- `form.secret_2fa`
- `form.usuario`
- `form.verification_code`
- `html.btn_ok`
- `html.cambio_password`
- `html.enable_2fa`
- `html.has_2fa`
- `html.id_usuario`
- `html.password`
- `html.password1`
- `html.verification_code`
- `post.id_usuario`
- `post.que`
- `post.quien`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

Acciones JavaScript:
- `fnjs_add_grup`
- `fnjs_chk_passwd`
- `fnjs_del_grup`
- `fnjs_enviar`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`
- `fnjs_lst_add_grup`
- `fnjs_lst_del_grup`
- `fnjs_mas_casas`

## Endpoints Del Flujo

- `/src/usuarios/usuario_info`

## Errores Conocidos

- `Id de usuario no válido`
- `Usuario no encontrado`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
