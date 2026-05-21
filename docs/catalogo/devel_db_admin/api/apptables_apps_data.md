---
id: "devel_db_admin.apptables_apps_data"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/apptables_apps_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/apptables_apps_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "devel_db_admin_ApptablesAppsDataData"
respuesta_data: ["a_apps:array"]
requiere_hashb: false
frontend_referencias: ["frontend/devel_db_admin/controller/apptables.php"]
casos_uso: ["src\\devel_db_admin\\application\\ApptablesAppsData"]
tags: ["devel_db_admin", "apptables", "apps", "data"]
estado_revision: "generado"
---

# Apptables Apps Data

JSON con el mapa `id_app` → nombre para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/devel_db_admin/apptables_apps_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/apptables_apps_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `devel_db_admin_ApptablesAppsDataData`):
  - `a_apps` (`array`)

## Casos De Uso

- `src\devel_db_admin\application\ApptablesAppsData`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/apptables.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.