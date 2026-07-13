---
id: "dbextern.sincro_syncro"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_syncro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_syncro.php"
entrada: ["post.dl_listas:string", "post.region:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\SincroPersonas"]
tags: ["dbextern", "sincro", "syncro"]
estado_revision: "revisado"
---

# Sincro Syncro

Sincroniza (punto 1) todas las personas BDU ya unidas a Aquinate en la DL actual.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Para cada persona BDU con `id_match`, llama `SincroDB::syncro` actualizando la ficha Orbix. Acumula
mensajes de aviso/error por persona. Devuelve recuento y texto resumen.

## Endpoint

- URL: `/src/dbextern/sincro_syncro`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_syncro.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | Sí | |
| `dl_listas` | `string` | controller | Sí | DL en nomenclatura listas |
| `tipo_persona` | `string` | controller | Sí | `n`/`a`/`s`/`sssc` |

## Salida

- Helper: `ContestarJson::enviar` (el front hace segundo `JSON.parse` de `data`).
- Éxito: `success: true`, `data` con `mensaje` (`OK. N personas sincronizadas` o texto acumulado de syncro).
- Los errores parciales van dentro de `mensaje`, no en `success: false`.

## Permisos

- HashFront (`h1`) en `sincro_index`; permisos de colectivo ya validados en bootstrap.

## Casos De Uso

- `src\dbextern\application\SincroPersonas`

## Frontend Relacionado

- `frontend/dbextern/controller/sincro_index.php` → `fnjs_sincronizar()` (muestra `alert(data.mensaje)`)
