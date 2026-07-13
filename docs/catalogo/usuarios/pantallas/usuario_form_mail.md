---
id: "usuarios.pantalla.usuario_form_mail"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Usuario Form Mail"
controller: "frontend/usuarios/controller/usuario_form_mail.php"
vistas: ["frontend/usuarios/view/usuario_form_mail.phtml"]
fragmentos_frontend: []
endpoints: ["/src/usuarios/usuario_info"]
capacidades: ["usuarios.usuario_info.gestionar"]
campos: ["form.email"]
acciones: ["fnjs_guardar"]
estado_revision: "revisado"
---

# Usuario Form Mail

Formulario cambio de email del usuario autenticado.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/usuarios/controller/usuario_form_mail.php`

## Vistas Relacionadas

- `frontend/usuarios/view/usuario_form_mail.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/usuarios/usuario_info`

## Capacidades Relacionadas

- `usuarios.usuario_info.gestionar`

## Campos Detectados

- `form.email`

## Acciones Detectadas

- `fnjs_guardar`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
