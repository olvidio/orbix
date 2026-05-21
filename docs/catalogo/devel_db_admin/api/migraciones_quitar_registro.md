---
id: "devel_db_admin.migraciones_quitar_registro"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/migraciones_quitar_registro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_quitar_registro.php"
entrada: ["post.sel:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "devel_db_admin_MigracionesQuitarRegistroData"
respuesta_data: ["lines:list<string>, error: string|null"]
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/migraciones_lista.php"]
casos_uso: ["src\\devel_db_admin\\application\\MigracionesQuitarRegistro"]
tags: ["devel_db_admin", "migraciones", "quitar", "registro"]
estado_revision: "generado"
---

# Migraciones Quitar Registro

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/migraciones_quitar_registro`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/migraciones_quitar_registro.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `devel_db_admin_MigracionesQuitarRegistroData`):
  - `lines` (`list<string>, error: string|null`)

## Casos De Uso

- `src\devel_db_admin\application\MigracionesQuitarRegistro`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/migraciones_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.