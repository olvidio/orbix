---
id: "devel_db_admin.apptables_apps_data"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/apptables_apps_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/apptables_apps_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/devel_db_admin/controller/apptables.php"]
casos_uso: ["src\\devel_db_admin\\application\\ApptablesAppsData"]
tags: ["devel_db_admin", "apptables", "apps", "data"]
estado_revision: "revisado"
---

# Apptables Apps Data

Mapa de aplicaciones instaladas para el formulario de gestión de tablas (`apptables`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el desplegable de apps (`id_app` → nombre) leyendo `AppRepository::getApps()`. Se invoca al
cargar la pantalla «Tablas de apps».

## Endpoint

- URL: `/src/devel_db_admin/apptables_apps_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/apptables_apps_data.php`

## Entrada

Sin parámetros.

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `a_apps` (`array<int|string, string>`): `id_app` → nombre de aplicación.

## Permisos

- Sin control propio; pantalla restringida al menú de configuración de desarrollo.

## Casos De Uso

- `src\devel_db_admin\application\ApptablesAppsData`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/apptables.php`
