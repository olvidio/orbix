---
id: "devel_db_admin.copiar_esquema"
tipo: "endpoint"
modulo: "devel_db_admin"
url: "/src/devel_db_admin/copiar_esquema"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/devel_db_admin/infrastructure/ui/http/controllers/copiar_esquema.php"
entrada: ["post.comun:integer", "post.dl:string", "post.esquema:string", "post.region:string", "post.sf:integer", "post.sv:integer"]
entrada_obligatoria: ["esquema", "region", "dl"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan región o delegación destino para copiar datos.", "Esquema de referencia no válido."]
frontend_referencias: ["frontend/devel_db_admin/controller/db_copiar.php"]
casos_uso: ["src\\devel_db_admin\\application\\CopiarEsquema"]
tags: ["devel_db_admin", "copiar", "esquema"]
estado_revision: "revisado"
---

# Copiar Esquema

Copia tablas de configuración desde un esquema de referencia y traslada datos `resto→dl` según flags
comun/sv/sf (paso «importar datos» del asistente de nuevo esquema).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Tras crear la estructura vacía, rellena tablas clave (`a_tipos_actividad`, aux usuarios/menús, etc.)
desde el esquema de referencia (`post.esquema`, nombre base `region-dl`) hacia el destino
(`post.region` + `post.dl`). Por cada bloque marcado (`comun`, `sv`, `sf`) ejecuta `DBTabla::copiar`
y `DBTrasvase` (`actividades`, `cdc`, `ctr`, `fix_seq`). Omite bloques cuya conexión `importar` no
esté configurada (avisos no bloqueantes).

## Endpoint

- URL: `/src/devel_db_admin/copiar_esquema`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/devel_db_admin/infrastructure/ui/http/controllers/copiar_esquema.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `esquema` | `string` | controller | Si | Esquema referencia (base, sin sufijo v/f) |
| `region` | `string` | controller | Si | Región destino |
| `dl` | `string` | controller | Si | Delegación destino |
| `comun` | `integer` | controller | No | `≠0` copia bloque comun |
| `sv` | `integer` | controller | No | `≠0` copia bloque sv |
| `sf` | `integer` | controller | No | `≠0` copia bloque sf |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse`).
- Éxito: `data`: `{ "ok": true, "avisos": list<string> }` (avisos de conexión omitida, etc.).
- Error: `success: false`, `mensaje` con excepción (`InvalidArgumentException` u otras).

## Errores conocidos

- `Faltan región o delegación destino para copiar datos.`
- `Esquema de referencia no válido.`

## Permisos

- Sin control propio; invocado desde `db_crear_esquema_que` (menú DB).

## Casos De Uso

- `src\devel_db_admin\application\CopiarEsquema`

## Frontend Relacionado

- `frontend/devel_db_admin/controller/db_copiar.php` (botón «importar datos»).
