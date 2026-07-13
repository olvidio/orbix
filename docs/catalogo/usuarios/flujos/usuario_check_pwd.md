---
id: "usuarios.usuario_check_pwd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "usuarios"
nombre: "Flujo - Gestionar Usuario Check Pwd"
capacidad: "usuarios.usuario_check_pwd.gestionar"
pantallas_principales: []
fragmentos: ["usuarios.pantalla.usuario_form", "usuarios.pantalla.usuario_form_pwd"]
acciones: ["ejecutar"]
endpoints: ["/src/usuarios/usuario_check_pwd"]
estado_revision: "revisado"
---

# Flujo - Usuario Check Pwd

## Objetivo De Usuario

Valida fortaleza de contraseña (JsonResponse directo, no envelope ContestarJson).

## Punto De Entrada

Sin entrada de menú directa; login, preferencias personales o fragmento/modal desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `usuarios.pantalla.usuario_form`
- `usuarios.pantalla.usuario_form_pwd`

## Escenarios Inferidos

### Ejecutar

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
