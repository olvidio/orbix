---
id: "usuarios.pantalla.usuario_form_pwd"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Usuario Form Pwd"
controller: "frontend/usuarios/controller/usuario_form_pwd.php"
vistas: ["frontend/usuarios/view/usuario_form_pwd.phtml"]
fragmentos_frontend: []
endpoints: ["/src/usuarios/usuario_check_pwd", "/src/usuarios/usuario_guardar_pwd", "/src/usuarios/usuario_info"]
capacidades: ["usuarios.usuario_check_pwd.gestionar", "usuarios.usuario_guardar_pwd.gestionar", "usuarios.usuario_info.gestionar"]
campos: ["form.id_usuario", "form.password", "form.password1", "html.password", "html.password1"]
acciones: ["fnjs_chk_passwd", "fnjs_guardar", "fnjs_guardar_datos", "fnjs_logout"]
estado_revision: "generado"
---

# Usuario Form Pwd

Formulario para cambiar el password por parte del usuario.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/usuario_form_pwd.php`

## Vistas Relacionadas

- `frontend/usuarios/view/usuario_form_pwd.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/usuarios/usuario_check_pwd`
- `/src/usuarios/usuario_guardar_pwd`
- `/src/usuarios/usuario_info`

## Capacidades Relacionadas

- `usuarios.usuario_check_pwd.gestionar`
- `usuarios.usuario_guardar_pwd.gestionar`
- `usuarios.usuario_info.gestionar`

## Campos Detectados

- `form.id_usuario`
- `form.password`
- `form.password1`
- `html.password`
- `html.password1`

## Acciones Detectadas

- `fnjs_chk_passwd`
- `fnjs_guardar`
- `fnjs_guardar_datos`
- `fnjs_logout`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
