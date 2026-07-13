---
id: "devel_db_admin.corregir_renombrar_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/corregir_renombrar_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/corregir_renombrar_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.esquema_origen:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: ["region", "dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["No se aplicó ninguna corrección: parámetros inválidos."]
frontend_referencias: ["frontend/devel_db_admin/controller/db_corregir_renombrar_esquema.php"]
casos_uso: ["src\\devel_db_admin\\application\\CorregirEstadoRenombrarEsquema"]
tags: ["devel_db_admin", "corregir", "renombrar", "esquema"]
estado_revision: "revisado"
---

# Corregir Renombrar Esquema

Repara un renombre de esquema interrumpido: reaplica defaults ALTER, reanuda renombre PostgreSQL,
alinea propietarios y sincroniza `.inc` / `db_idschema`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Tras `VerificarEstadoRenombrarEsquema`, intenta dejar consistente un renombre a medias. Acepta
`esquema_origen` (o alias legado `esquema`) vacío para modo «solo destino» (solo defaults sobre
`region-dl`). Devuelve acciones realizadas, avisos y un objeto `verificacion` re-ejecutado al final.

## Endpoint

- URL: `/src/devel_db_admin/corregir_renombrar_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/corregir_renombrar_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema_origen` | `string` | controller | No | Alias: `esquema`; vacío = solo destino |
| `region` | `string` | controller | Si | Región destino |
| `dl` | `string` | controller | Si | Delegación destino |
| `comun`, `sv`, `sf` | `integer` | controller | No | Bloques activos (`≠0`) |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Payload en `data`:
  - `acciones` (`list<string>`)
  - `avisos` (`list<string>`)
  - `verificacion` (`object`): mismo formato que `verificar_renombrar_esquema`

## Errores conocidos

- `No se aplicó ninguna corrección: parámetros inválidos.` (contexto inválido; `verificacion` en respuesta)
- Avisos dinámicos: renombre PostgreSQL, defaults, propietario, sincronización `.inc`/`db_idschema`

## Permisos

- Sin control propio; fragmento de `db_cambiar_nombre_que`.

## Casos De Uso

- `src\devel_db_admin\application\CorregirEstadoRenombrarEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_corregir_renombrar_esquema.php`
