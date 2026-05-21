---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "usuarios"
titulo: "Usuario Form Pwd"
pantalla: "usuarios.pantalla.usuario_form_pwd"
preguntas: ["Que se puede hacer en Usuario Form Pwd?", "Que campos tiene Usuario Form Pwd?", "Que acciones hay en Usuario Form Pwd?"]
capacidades: ["usuarios.usuario_check_pwd.gestionar", "usuarios.usuario_guardar_pwd.gestionar", "usuarios.usuario_info.gestionar"]
endpoints: ["/src/usuarios/usuario_check_pwd", "/src/usuarios/usuario_guardar_pwd", "/src/usuarios/usuario_info"]
source: "docs/catalogo/usuarios/pantallas/usuario_form_pwd.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - Usuario Form Pwd

## Resumen

Formulario para cambiar el password por parte del usuario.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

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

## Capacidades Relacionadas

- `usuarios.usuario_check_pwd.gestionar`
- `usuarios.usuario_guardar_pwd.gestionar`
- `usuarios.usuario_info.gestionar`

## Endpoints Relacionados

- `/src/usuarios/usuario_check_pwd`
- `/src/usuarios/usuario_guardar_pwd`
- `/src/usuarios/usuario_info`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
