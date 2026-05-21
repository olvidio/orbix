---
id: "usuarios.recuperar_password_mail"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/recuperar_password_mail"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php"
entrada: ["post.esquema:string", "post.esquema_web:string", "post.ubicacion:string", "post.url_index:string", "post.username:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: []
tags: ["usuarios", "recuperar", "password", "mail"]
estado_revision: "generado"
---

# Recuperar Password Mail

Página para recuperar la contraseña de un usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/recuperar_password_mail`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/recuperar_password_mail.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema` | `string` | controller | No | controller |
| `esquema_web` | `string` | controller | No | controller |
| `ubicacion` | `string` | controller | No | controller |
| `url_index` | `string` | controller | No | controller |
| `username` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.