---
id: "devel_db_admin.absorber_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/absorber_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/absorber_esquema.php"
entrada: ["post.esquema_del:string", "post.esquema_matriz:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/db_absorber_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\AbsorberEsquema"]
tags: ["devel_db_admin", "absorber", "esquema"]
estado_revision: "generado"
---

# Absorber Esquema

JSON `{ "lines": string[] }` para la absorción de esquema (POST `esquema_matriz`, `esquema_del`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/absorber_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/absorber_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema_del` | `string` | controller | No | controller |
| `esquema_matriz` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\devel_db_admin\application\AbsorberEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_absorber_esquema.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.