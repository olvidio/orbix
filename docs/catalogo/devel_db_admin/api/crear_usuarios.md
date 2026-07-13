---
id: "devel_db_admin.crear_usuarios"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/crear_usuarios"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/crear_usuarios.php"
entrada: ["post.dl:string", "post.region:string"]
entrada_obligatoria: ["region", "dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/devel_db_admin/controller/db_crear_usuarios.php"]
casos_uso: ["src\\devel_db_admin\\application\\CrearUsuarios"]
tags: ["devel_db_admin", "crear", "usuarios"]
estado_revision: "revisado"
---

# Crear Usuarios

Crea roles PostgreSQL para un esquema `region-dl` y registra passwords en ficheros de importación.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Paso 1 del asistente «nuevo esquema». Genera passwords aleatorios y crea usuarios `region-dl`,
`region-dlv` y `region-dlf` en comun, sv, sv-e y sf (sf-e según sesión `$_SESSION['sfsv']==='sf'`).
Actualiza ficheros `.inc` de passwords vía `ConfigDB::addEsquemaEnFicheroPasswords`.

## Endpoint

- URL: `/src/devel_db_admin/crear_usuarios`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/crear_usuarios.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | Si | |
| `dl` | `string` | controller | Si | |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `esquema`, `esquemaPwd` (comun)
  - `esquemav`, `esquemavPwd` (sv / sv-e)
  - `esquemaf`, `esquemafPwd` (sf)

## Permisos

- Sin control propio; menú DB desarrollo.

## Casos De Uso

- `src\devel_db_admin\application\CrearUsuarios`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_crear_usuarios.php`
