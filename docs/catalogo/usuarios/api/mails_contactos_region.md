---
id: "usuarios.mails_contactos_region"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/mails_contactos_region"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/mails_contactos_region.php"
entrada: ["post.region:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "usuarios_usuariosRegionContactosData"
respuesta_data: ["error:string, data: array<string, mixed>"]
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/controller/mails_contactos_region.php"]
casos_uso: ["src\\usuarios\\application\\usuariosRegionContactos"]
tags: ["usuarios", "mails", "contactos", "region"]
estado_revision: "generado"
---

# Mails Contactos Region

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/mails_contactos_region`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/mails_contactos_region.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `usuarios_usuariosRegionContactosData`):
  - `error` (`string, data: array<string, mixed>`)

## Permisos

- Permiso oficina `est`
- Permiso oficina `sm`
- Permiso oficina `agd`

## Casos De Uso

- `src\usuarios\application\usuariosRegionContactos`

## Frontend Relacionado

- `frontend/usuarios/controller/mails_contactos_region.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.