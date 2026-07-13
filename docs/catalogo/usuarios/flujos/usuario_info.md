---
id: "usuarios.usuario_info.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Info"
capacidad: "usuarios.usuario_info.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_2fa", "usuarios.pantalla.usuario_form_mail", "usuarios.pantalla.usuario_form_pwd"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_info"]
estado_revision: "revisado"
---

# Flujo - Usuario Info

## Objetivo De Usuario

Resumen usuario para cabecera ficha (grupos, login, email).

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_2fa`
- `usuarios.pantalla.usuario_form_mail`
- `usuarios.pantalla.usuario_form_pwd`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
