---
id: "devel_db_admin.eliminar_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/eliminar_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/eliminar_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: ["region", "dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se pudo eliminar el esquema «%1$s» en %2$s: %3$s", "El rol «%s» ya no existía; no se intentó borrarlo.", "Aviso: no se pudo eliminar el rol «%1$s» (los esquemas ya se borraron): %2$s"]
frontend_referencias: ["frontend/devel_db_admin/controller/db_eliminar.php"]
casos_uso: ["src\\devel_db_admin\\application\\EliminarEsquemaDl"]
tags: ["devel_db_admin", "eliminar", "esquema"]
estado_revision: "revisado"
---

# Eliminar Esquema

Traslada datos de un esquema DL a «resto», elimina esquemas/roles y limpia ficheros de passwords.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Operación inversa a «nuevo esquema». Por bloques `comun`/`sv`/`sf`: traslado `dl2resto`
(`DBTrasvase`), borrado de schemas en importar (incl. réplicas), eliminación de roles PostgreSQL,
revocación de permisos y limpieza de entradas en `.inc` de passwords. Devuelve avisos no bloqueantes.

## Endpoint

- URL: `/src/devel_db_admin/eliminar_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/eliminar_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | Si | |
| `dl` | `string` | controller | Si | |
| `comun`, `sv`, `sf` | `integer` | controller | No | Bloques a eliminar |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `{ "ok": true, "avisos": list<string> }`.

## Errores conocidos

- Avisos (no abortan): fallo al eliminar esquema/rol (mensajes `_()` arriba)
- Excepciones no capturadas → `success: false`, `mensaje`

## Permisos

- Sin control propio; menú `sistema > DB > eliminar esquema`.

## Casos De Uso

- `src\devel_db_admin\application\EliminarEsquemaDl`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_eliminar.php`
