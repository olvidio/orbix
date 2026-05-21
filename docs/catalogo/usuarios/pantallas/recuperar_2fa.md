---
id: "usuarios.pantalla.recuperar_2fa"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "usuarios"
nombre: "Recuperar 2fa"
controller: "frontend/usuarios/controller/recuperar_2fa.php"
vistas: ["frontend/usuarios/view/recuperar_2fa.phtml"]
fragmentos_frontend: []
endpoints: []
capacidades: []
campos: ["get.esquema", "get.esquema_web", "get.ubicacion", "get.url_base", "get.username"]
acciones: []
estado_revision: "generado"
---

# Recuperar 2fa

Página para recuperar el QR para la app 2fa.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/usuarios/controller/recuperar_2fa.php`

## Vistas Relacionadas

- `frontend/usuarios/view/recuperar_2fa.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

No se han detectado endpoints `/src/...`.

## Capacidades Relacionadas

No se han detectado capacidades relacionadas.

## Campos Detectados

- `get.esquema`
- `get.esquema_web`
- `get.ubicacion`
- `get.url_base`
- `get.username`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
