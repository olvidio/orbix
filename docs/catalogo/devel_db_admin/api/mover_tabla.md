---
id: "devel_db_admin.mover_tabla"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/mover_tabla"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/mover_tabla.php"
entrada: ["post.tabla:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/db_mover.php"]
casos_uso: ["src\\devel_db_admin\\application\\MoverTabla"]
tags: ["devel_db_admin", "mover", "tabla"]
estado_revision: "generado"
---

# Mover Tabla

Lista esquemas con la tabla y ejecuta {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/mover_tabla`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/mover_tabla.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tabla` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\devel_db_admin\application\MoverTabla`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_mover.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.