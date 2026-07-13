---
id: "devel_db_admin.mover_tabla"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/mover_tabla"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/mover_tabla.php"
entrada: ["post.tabla:string"]
entrada_obligatoria: ["tabla"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Error para %s", "error al escribir el fichero"]
frontend_referencias: ["frontend/devel_db_admin/controller/db_mover.php"]
casos_uso: ["src\\devel_db_admin\\application\\MoverTabla"]
tags: ["devel_db_admin", "mover", "tabla"]
estado_revision: "revisado"
---

# Mover Tabla

Mueve una tabla de sv (`publicv`) a sv-e (`publicv-e`) en todos los esquemas que la contienen.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe `tabla`, lista esquemas con `DBPropiedades::array_esquemas_con_tabla` y por cada uno ejecuta
`DBTabla::mover` + `eliminarTabla` en origen. En servidores `.local` puede parchear referencias
`$oDB`→`$oDBE` en PHP. Devuelve esquemas afectados y líneas HTML informativas.

## Endpoint

- URL: `/src/devel_db_admin/mover_tabla`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/mover_tabla.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tabla` | `string` | controller | Si | Nombre de tabla PostgreSQL |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `{ "a_esquemas": list<string>, "lines": list<string> }` (HTML en líneas).
- Error fatal: `success: false`, `mensaje`.

## Errores conocidos

- `Error para %s` (por esquema, HTML)
- `error al escribir el fichero` (parche local)

## Permisos

- Sin control propio; menú `sistema > DB > mover tabla a otra DB`.

## Casos De Uso

- `src\devel_db_admin\application\MoverTabla`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_mover.php`
