---
id: "usuarios.pantalla.usuario_form"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Usuario Form"
controller: "frontend/usuarios/controller/usuario_form.php"
vistas: ["frontend/usuarios/view/usuario_form.phtml", "frontend/usuarios/view/usuario_grupo.phtml"]
fragmentos_frontend: ["frontend/cambios/controller/usuario_form_avisos.php", "frontend/usuarios/controller/perm_activ_lista.php", "frontend/usuarios/controller/usuario_grupo_del_lst.php", "frontend/usuarios/controller/usuario_grupo_lst.php"]
endpoints: ["/src/usuarios/usuario_ajax", "/src/usuarios/usuario_check_pwd", "/src/usuarios/usuario_form", "/src/usuarios/usuario_grupo_add", "/src/usuarios/usuario_grupo_del", "/src/usuarios/usuario_guardar", "/src/usuarios/usuario_info"]
capacidades: ["usuarios.usuario.gestionar", "usuarios.usuario_check_pwd.gestionar", "usuarios.usuario_grupo_add.gestionar", "usuarios.usuario_grupo_del.gestionar", "usuarios.usuario_info.gestionar"]
campos: ["form.id_usuario", "form.password", "form.usuario", "html.cambio_password", "html.has_2fa", "html.password", "post.id_usuario", "post.que", "post.quien", "post.refresh", "post.scroll_id", "post.sel", "post.stack"]
acciones: ["fnjs_add_grup", "fnjs_chk_passwd", "fnjs_del_grup", "fnjs_guardar", "fnjs_guardar_datos", "fnjs_lst_add_grup", "fnjs_lst_del_grup", "fnjs_mas_casas"]
estado_revision: "revisado"
---

# Usuario Form

Ficha usuario: datos, rol, pau, permisos menú/actividad y grupos (admin id_role≤3).

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/usuarios/controller/usuario_form.php`

## Vistas Relacionadas

- `frontend/usuarios/view/usuario_form.phtml`
- `frontend/usuarios/view/usuario_grupo.phtml`

## Fragmentos Frontend Relacionados

- `frontend/cambios/controller/usuario_form_avisos.php`
- `frontend/usuarios/controller/perm_activ_lista.php`
- `frontend/usuarios/controller/usuario_grupo_del_lst.php`
- `frontend/usuarios/controller/usuario_grupo_lst.php`

## Endpoints Usados

- `/src/usuarios/usuario_ajax`
- `/src/usuarios/usuario_check_pwd`
- `/src/usuarios/usuario_form`
- `/src/usuarios/usuario_grupo_add`
- `/src/usuarios/usuario_grupo_del`
- `/src/usuarios/usuario_guardar`
- `/src/usuarios/usuario_info`

## Capacidades Relacionadas

- `usuarios.usuario.gestionar`
- `usuarios.usuario_check_pwd.gestionar`
- `usuarios.usuario_grupo_add.gestionar`
- `usuarios.usuario_grupo_del.gestionar`
- `usuarios.usuario_info.gestionar`

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

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
