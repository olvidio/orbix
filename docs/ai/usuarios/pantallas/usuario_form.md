---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Usuario Form"
pantalla: "usuarios.pantalla.usuario_form"
preguntas: ["Que se puede hacer en Usuario Form?", "Que campos tiene Usuario Form?", "Que acciones hay en Usuario Form?"]
capacidades: ["usuarios.usuario.gestionar", "usuarios.usuario_check_pwd.gestionar", "usuarios.usuario_grupo_add.gestionar", "usuarios.usuario_grupo_del.gestionar", "usuarios.usuario_info.gestionar"]
endpoints: ["/src/usuarios/usuario_ajax", "/src/usuarios/usuario_check_pwd", "/src/usuarios/usuario_form", "/src/usuarios/usuario_grupo_add", "/src/usuarios/usuario_grupo_del", "/src/usuarios/usuario_guardar", "/src/usuarios/usuario_info"]
source: "docs/catalogo/usuarios/pantallas/usuario_form.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Usuario Form

## Resumen

Ficha usuario: datos, rol, pau, permisos menú/actividad y grupos (admin id_role≤3).

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `form.id_usuario`
- `form.password`
- `form.usuario`
- `html.cambio_password`
- `html.has_2fa`
- `html.password`
- `post.id_usuario`
- `post.que`
- `post.quien`
- `post.refresh`
- `post.scroll_id`
- `post.sel`
- `post.stack`

## Acciones Detectadas

- `fnjs_add_grup`
- `fnjs_chk_passwd`
- `fnjs_del_grup`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_lst_add_grup`
- `fnjs_lst_del_grup`
- `fnjs_mas_casas`

## Capacidades Relacionadas

- `usuarios.usuario.gestionar`
- `usuarios.usuario_check_pwd.gestionar`
- `usuarios.usuario_grupo_add.gestionar`
- `usuarios.usuario_grupo_del.gestionar`
- `usuarios.usuario_info.gestionar`

## Endpoints Relacionados

- `/src/usuarios/usuario_ajax`
- `/src/usuarios/usuario_check_pwd`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_grupo_add`
- `/src/usuarios/usuario_grupo_del`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_info`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
