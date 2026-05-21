---
id: "usuarios.pantalla.recovery"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "usuarios"
nombre: "Recovery"
controller: "frontend/usuarios/controller/recovery.php"
vistas: []
fragmentos_frontend: ["frontend/usuarios/controller/usuario_form_2fa.php"]
endpoints: []
capacidades: []
campos: ["get.esquema", "get.id_usuario", "get.token"]
acciones: []
estado_revision: "generado"
---

# Recovery

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `pantalla`
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

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
