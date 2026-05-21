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
estado_revision: "generado"
---

# Flujo - Gestionar Usuario Info

Propuesta generada automaticamente desde la capacidad `usuarios.usuario_info.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona UsuarioInfo. Descripcion funcional pendiente de revisar.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

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

No se han documentado errores en la capacidad.

## Revision Manual

- Confirmar si el flujo debe separarse en varios flujos de usuario.
- Cambiar nombres tecnicos por nombres de usuario.
- Completar precondiciones, permisos, validaciones y errores comunes.
- Redactar los pasos definitivos para el manual de usuario.
