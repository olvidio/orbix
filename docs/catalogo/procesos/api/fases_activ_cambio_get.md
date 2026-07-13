---
id: "procesos.fases_activ_cambio_get"
tipo: "endpoint"
modulo: "procesos"
url: "/src/procesos/fases_activ_cambio_get"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_get.php"
entrada: ["post.dl_propia:string", "post.id_fase_sel:string", "post.id_tipo_activ:string"]
entrada_obligatoria: ["id_tipo_activ"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/procesos/controller/fases_activ_cambio.php"]
casos_uso: ["src\\procesos\\application\\FasesActivCambioGet"]
tags: ["procesos", "fases", "activ", "cambio", "get"]
estado_revision: "revisado"
---

# Fases Activ Cambio Get

Desplegable JSON de fases posibles para cambio masivo de fase.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye las opciones del desplegable `id_fase_nueva` según el tipo de actividad y si es de
delegación propia. Devuelve metadatos de desplegable (`id`, `action`, `blanco`) compatibles con
el helper genérico del frontend.

## Endpoint

- URL: `/src/procesos/fases_activ_cambio_get`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/procesos/infrastructure/ui/http/controllers/fases_activ_cambio_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | application | Si | Tipo de actividad seleccionado |
| `dl_propia` | `string` | application | No | `t`/`f`; filtra procesos propios vs ajenos |
| `id_fase_sel` | `string` | application | No | Valor preseleccionado del desplegable |

El controller pasa `$_POST` completo al caso de uso.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Claves en `data` (doble `JSON.parse`):
  - `id` (`string`): siempre `id_fase_nueva`
  - `opciones` (`list<array{0: string, 1: string}>`): pares valor/etiqueta
  - `selected` (`string`): eco de `id_fase_sel`
  - `blanco` (`bool`): `true` (permite opción vacía)
  - `action` (`string`): `fnjs_lista()` (dispara recarga de tabla)

## Errores conocidos

- _(ninguno documentado en el caso de uso)_

## Permisos

- Sin control de permisos propio; autorización en `fases_activ_cambio.php` y `$_SESSION['oPerm']`.

## Casos De Uso

- `src\procesos\application\FasesActivCambioGet`

## Frontend Relacionado

- `frontend/procesos/controller/fases_activ_cambio.php` (URL emitida como `url_get` / `h_actualizar`)
