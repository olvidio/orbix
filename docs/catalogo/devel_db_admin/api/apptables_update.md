---
id: "devel_db_admin.apptables_update"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/apptables_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/apptables_update.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "devel_db_admin_ApptablesUpdateData"
respuesta_data: ["ok:true, mensaje: string, bases: list<string>, replica: bool"]
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/apptables_update.php"]
casos_uso: ["src\\devel_db_admin\\application\\ApptablesUpdate"]
tags: ["devel_db_admin", "apptables", "update"]
estado_revision: "generado"
---

# Apptables Update

Ejecuta {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/apptables_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/apptables_update.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `devel_db_admin_ApptablesUpdateData`):
  - `ok` (`true, mensaje: string, bases: list<string>, replica: bool`)

## Efectos colaterales

- Crear / eliminar / llenar tablas de aplicación (herramienta apptables).

## Casos De Uso

- `src\devel_db_admin\application\ApptablesUpdate`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/apptables_update.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.