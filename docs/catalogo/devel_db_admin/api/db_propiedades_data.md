---
id: "devel_db_admin.db_propiedades_data"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/db_propiedades_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/db_propiedades_data.php"
entrada: ["post.default_esquema:string", "post.op:string", "post.tabla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "devel_db_admin_DbPropiedadesFormDataData"
respuesta_data: ["oDesplEsquemas:string|false"]
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/apptables.php", "frontend/devel_db_admin/controller/db_absorber_esquema_que.php", "frontend/devel_db_admin/controller/db_cambiar_nombre_que.php", "frontend/devel_db_admin/controller/db_crear_esquema_que.php", "frontend/devel_db_admin/controller/db_eliminar_esquema_que.php", "frontend/devel_db_admin/controller/db_mover_que.php"]
casos_uso: ["src\\devel_db_admin\\application\\DbPropiedadesFormData"]
tags: ["devel_db_admin", "db", "propiedades", "data"]
estado_revision: "generado"
---

# Db Propiedades Data

JSON para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/db_propiedades_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/db_propiedades_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `default_esquema` | `string` | application | No | application |
| `op` | `string` | application | No | application |
| `tabla` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `devel_db_admin_DbPropiedadesFormDataData`):
  - `oDesplEsquemas` (`string|false`)

## Casos De Uso

- `src\devel_db_admin\application\DbPropiedadesFormData`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/apptables.php`
- `frontend/devel_db_admin/controller/db_absorber_esquema_que.php`
- `frontend/devel_db_admin/controller/db_cambiar_nombre_que.php`
- `frontend/devel_db_admin/controller/db_crear_esquema_que.php`
- `frontend/devel_db_admin/controller/db_eliminar_esquema_que.php`
- `frontend/devel_db_admin/controller/db_mover_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.