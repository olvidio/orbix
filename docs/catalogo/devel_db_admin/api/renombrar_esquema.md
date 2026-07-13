---
id: "devel_db_admin.renombrar_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/renombrar_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/renombrar_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.esquema_origen:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: ["esquema_origen", "region", "dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["El esquema «%1$s» no tiene entrada de conexión en «%2$s.inc» (ni con el nombre antiguo ni con el nuevo «%3$s»). El listado de origen sale de PostgreSQL; hace falta la misma clave en el fichero de passwords (p. ej. tras «Crear esquema») para poder renombrar."]
frontend_referencias: ["frontend/devel_db_admin/controller/db_renombrar_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\RenombrarEsquema"]
tags: ["devel_db_admin", "renombrar", "esquema"]
estado_revision: "revisado"
---

# Renombrar Esquema

Renombra un esquema `region-dl` (comun/sv/sv-e/sf): roles PostgreSQL, `.inc`, `db_idschema` y defaults.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe esquema origen (`esquema_origen` o alias `esquema`) y destino (`region`+`dl`). Idempotente por
bloque; reanuda renombres interrumpidos. Actualiza datos regexp en tablas, `db_idschema`, ficheros
password y defaults ALTER. Puede devolver `error` bloqueante (ficheros password) o `avisos`
informativos (sf parcial, réplicas).

## Endpoint

- URL: `/src/devel_db_admin/renombrar_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/renombrar_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema_origen` | `string` | controller | Si* | *Alias `esquema`; base sin v/f |
| `region` | `string` | controller | Si | Destino |
| `dl` | `string` | controller | Si | Destino |
| `comun`, `sv`, `sf` | `integer` | controller | No | Bloques activos |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `{ "ok": true, "avisos": list<string> }`.
- Error bloqueante: `success: false`, `mensaje` (p. ej. password `.inc`).
- Contexto inválido: avisos con `resumen` (no lanza excepción).

## Errores conocidos

- `El esquema «…» no tiene entrada de conexión en «….inc»…`
- Avisos sf/rol (ver `RenombrarEsquema`)
- Excepciones PostgreSQL → `mensaje`

## Permisos

- Sin control propio; menú `sistema > DB > cambiar nombre esquema`.

## Casos De Uso

- `src\devel_db_admin\application\RenombrarEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_renombrar_esquema.php`
