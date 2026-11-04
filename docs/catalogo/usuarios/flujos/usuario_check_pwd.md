---
id: "usuarios.usuario_check_pwd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Validar una contraseña al escribirla"
capacidad: "usuarios.usuario_check_pwd.gestionar"
pantallas_principales: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_pwd"]
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_pwd"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_check_pwd"]
estado_revision: "revisado"
---

# Flujo - Usuario Check Pwd

## Objetivo De Usuario

Comprobar, antes de guardar, que la contraseña escrita cumple los requisitos.

## Punto De Entrada

Sin entrada de menú propia: se usa desde otra pantalla o de forma automática.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_pwd`

## Escenarios Inferidos

### Validar una contraseña al escribirla

Pasos propuestos:
1. Escribir la contraseña en el formulario.
2. Al pulsar guardar se comprueba automáticamente.
3. Si no cumple, sale un aviso «password error» con el motivo y no se guarda. La regla: más de 8 caracteres e incluir al menos una letra mayúscula, un número y un carácter especial. Ejemplos: «abc» falla por corta; «abcdefgh1» falla por no tener mayúscula ni carácter especial.

Endpoints asociados:
- `/src/usuarios/usuario_check_pwd`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.id_usuario`
- `form.password`
- `form.password1`
- `form.usuario`
- `html.cambio_password`
- `html.has_2fa`
- `html.password`
- `html.password1`
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
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`
- `fnjs_lst_add_grup`
- `fnjs_lst_del_grup`
- `fnjs_mas_casas`

## Endpoints Del Flujo

- `/src/usuarios/usuario_check_pwd`

## Errores Conocidos

- _(ninguno documentado)_

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
