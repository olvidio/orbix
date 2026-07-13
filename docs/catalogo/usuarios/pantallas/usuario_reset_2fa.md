---
id: "usuarios.pantalla.usuario_reset_2fa"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Usuario Reset 2fa"
controller: "frontend/usuarios/controller/usuario_reset_2fa.php"
vistas: []
fragmentos_frontend: ["frontend/usuarios/controller/usuario_form_2fa.php"]
endpoints: ["/src/usuarios/usuario_2fa_update"]
capacidades: ["usuarios.usuario_2fa.gestionar"]
campos: ["post.id_usuario"]
acciones: []
estado_revision: "revisado"
---

# Usuario Reset 2fa

Fragmento admin para resetear 2FA de un usuario.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/usuarios/controller/usuario_reset_2fa.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/usuario_form_2fa.php`

## Endpoints Usados

- `/src/usuarios/usuario_2fa_update`

## Capacidades Relacionadas

- `usuarios.usuario_2fa.gestionar`

## Campos Detectados

- `post.id_usuario`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
