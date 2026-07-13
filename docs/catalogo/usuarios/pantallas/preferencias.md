---
id: "usuarios.pantalla.preferencias"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "usuarios"
nombre: "Preferencias"
controller: "frontend/usuarios/controller/preferencias.php"
vistas: ["frontend/usuarios/view/preferencias.phtml"]
fragmentos_frontend: ["frontend/cambios/controller/avisos_generar.php", "frontend/cambios/controller/usuario_form_avisos.php", "frontend/usuarios/controller/usuario_form_2fa.php", "frontend/usuarios/controller/usuario_form_mail.php", "frontend/usuarios/controller/usuario_form_pwd.php"]
endpoints: ["/src/shared/locales_posibles", "/src/usuarios/usuario_preferencias"]
capacidades: ["usuarios.usuario_preferencias.gestionar"]
campos: ["form.estilo_color", "form.idioma_nou", "form.inicio", "form.layout", "form.oficina", "form.ordenApellidos", "form.tipo_menu", "form.tipo_tabla", "form.zona_horaria_nou"]
acciones: ["button:guardar preferencias", "fnjs_guardar_preferencias", "fnjs_left_side_hide", "fnjs_update_div"]
estado_revision: "revisado"
---

# Preferencias

Preferencias personales: layout, inicio, idioma, tablas, estilo.

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/usuarios/controller/preferencias.php`

## Vistas Relacionadas

- `frontend/usuarios/view/preferencias.phtml`

## Fragmentos Frontend Relacionados

- `frontend/cambios/controller/avisos_generar.php`
- `frontend/cambios/controller/usuario_form_avisos.php`
- `frontend/usuarios/controller/usuario_form_2fa.php`
- `frontend/usuarios/controller/usuario_form_mail.php`
- `frontend/usuarios/controller/usuario_form_pwd.php`

## Endpoints Usados

- `/src/shared/locales_posibles`
- `/src/usuarios/usuario_preferencias`

## Capacidades Relacionadas

- `usuarios.usuario_preferencias.gestionar`

## Campos Detectados

- `form.estilo_color`
- `form.idioma_nou`
- `form.inicio`
- `form.layout`
- `form.oficina`
- `form.ordenApellidos`
- `form.tipo_menu`
- `form.tipo_tabla`
- `form.zona_horaria_nou`

## Acciones Detectadas

- `button:guardar preferencias`
- `fnjs_guardar_preferencias`
- `fnjs_left_side_hide`
- `fnjs_update_div`

## Ruta de menú

- **Legacy:** menú usuario > preferencias
- **Pills2:** menú usuario > preferencias
