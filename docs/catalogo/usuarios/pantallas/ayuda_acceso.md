---
id: "usuarios.pantalla.ayuda_acceso"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Ayuda Acceso"
controller: "frontend/usuarios/controller/ayuda_acceso.php"
vistas: ["frontend/usuarios/view/ayuda_acceso.phtml"]
fragmentos_frontend: ["frontend/usuarios/controller/ayuda_2fa_reset.php", "frontend/usuarios/controller/recuperar_password.php"]
endpoints: []
capacidades: []
campos: ["post.esquema", "post.esquema_web", "post.ubicacion", "post.username"]
acciones: []
estado_revision: "revisado"
---

# Ayuda Acceso

Ayuda acceso: muestra email ofuscado y contacto admin.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/usuarios/controller/ayuda_acceso.php`

## Vistas Relacionadas

- `frontend/usuarios/view/ayuda_acceso.phtml`

## Fragmentos Frontend Relacionados

- `frontend/usuarios/controller/ayuda_2fa_reset.php`
- `frontend/usuarios/controller/recuperar_password.php`

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `post.esquema`
- `post.esquema_web`
- `post.ubicacion`
- `post.username`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
