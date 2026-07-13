---
id: "devel_db_admin.db_propiedades_data"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/db_propiedades_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/db_propiedades_data.php"
entrada: ["post.default_esquema:string", "post.op:string", "post.tabla:string"]
entrada_obligatoria: ["op"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["op no válida"]
frontend_referencias: ["frontend/devel_db_admin/controller/apptables.php", "frontend/devel_db_admin/controller/db_crear_esquema_que.php", "frontend/devel_db_admin/controller/db_cambiar_nombre_que.php", "frontend/devel_db_admin/controller/db_absorber_esquema_que.php", "frontend/devel_db_admin/controller/db_mover_que.php"]
casos_uso: ["src\\devel_db_admin\\application\\DbPropiedadesFormData"]
tags: ["devel_db_admin", "db", "propiedades", "data"]
estado_revision: "revisado"
---

# Db Propiedades Data

Builder multi-operación de desplegables y metadatos de esquemas/tablas para pantallas DB.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Según `post.op`, delega en `DBPropiedades` y devuelve HTML de desplegables u opciones en array:

| `op` | Payload |
|------|---------|
| `apptables_esquemas` | `{ oDesplEsquemas }` (+ `default_esquema` opcional) |
| `db_que_esquema_ref` | `{ oEsquemaRef, a_opciones_regiones }` |
| `db_cambiar_nombre_esquemas` | `{ a_esquemas_union, a_opciones_regiones }` |
| `db_absorber_esquema_que` | `{ a_posibles_esquemas }` |
| `db_mover_tablas` | `{ desplTablas }` |
| `db_mover_esquemas_con_tabla` | `{ a_esquemas_con_tabla }` (+ `tabla`) |

## Endpoint

- URL: `/src/devel_db_admin/db_propiedades_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/db_propiedades_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `op` | `string` | application | Si | Una de las operaciones anteriores |
| `default_esquema` | `string` | application | No | Solo `apptables_esquemas` |
| `tabla` | `string` | application | No | Solo `db_mover_esquemas_con_tabla` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: claves según `op` (ver tabla).
- Error: `success: false`, `mensaje`: `op no válida`.

## Errores conocidos

- `op no válida`

## Permisos

- Sin control propio.

## Casos De Uso

- `src\devel_db_admin\application\DbPropiedadesFormData`

## Frontend Relacionado

- Invocado con `PostRequest::getDataFromUrl` al cargar pantallas DB y `apptables`.
