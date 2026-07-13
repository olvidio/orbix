---
id: "usuarios.pantalla.recovery"
tipo: "pantalla_frontend"
subtipo: "pantalla_principal"
modulo: "usuarios"
nombre: "Recovery"
controller: "frontend/usuarios/controller/recovery.php"
vistas: []
fragmentos_frontend: ["frontend/usuarios/controller/usuario_form_2fa.php"]
endpoints: []
capacidades: []
campos: ["get.esquema", "get.id_usuario", "get.token"]
acciones: []
estado_revision: "revisado"
---

# Recovery

Dispatcher recuperación acceso (password/2FA/ayuda).

## Tipo

- Subtipo: `pantalla_principal`


- Controller: `frontend/usuarios/controller/recovery.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/usuario_form_2fa.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `get.esquema`
- `get.id_usuario`
- `get.token`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
