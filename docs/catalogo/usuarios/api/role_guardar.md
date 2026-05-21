---
id: "usuarios.role_guardar"
tipo: "endpoint"
modulo: "usuarios"
url: "/src/usuarios/role_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/usuarios/infrastructure/ui/http/controllers/role_guardar.php"
entrada: ["post.dmz:integer", "post.id_role:integer", "post.pau:string", "post.role:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/usuarios/view/role_form.phtml"]
casos_uso: []
tags: ["usuarios", "role", "guardar"]
estado_revision: "generado"
---

# Role Guardar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/usuarios/role_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/usuarios/infrastructure/ui/http/controllers/role_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dmz` | `integer` | controller | No | controller |
| `id_role` | `integer` | controller | No | controller |
| `pau` | `string` | controller | No | controller |
| `role` | `string` | controller | No | controller |
| `sf` | `integer` | controller | No | controller |
| `sv` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

No se han detectado imports de `src\...\application\...`.

## Frontend Relacionado

- `frontend/usuarios/view/role_form.phtml`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.