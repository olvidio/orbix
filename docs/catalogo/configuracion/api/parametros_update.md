---
id: "configuracion.parametros_update"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/parametros_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/configuracion/infrastructure/ui/http/controllers/parametros_update.php"
entrada: ["post.fin_dia:integer", "post.fin_mes:integer", "post.ini_dia:integer", "post.ini_mes:integer", "post.parametro:string", "post.valor:string"]
entrada_obligatoria: ["parametro"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/configuracion/controller/parametros.php"]
casos_uso: []
tags: ["configuracion", "parametros", "update"]
estado_revision: "revisado"
---

# Parametros Update

Guarda un parámetro de configuración (`ConfigSchema`) desde la pantalla de parámetros.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Persiste un único parámetro de configuración identificado por `parametro`, con su
`valor`. La lógica vive en el propio controller (no hay caso de uso de `application/`):
construye un `ConfigSchema` con `ConfigParametroCode` / `ConfigValor` y lo guarda vía
`ConfigSchemaRepositoryInterface::Guardar`.

Caso especial de fechas de curso: cuando `parametro` es `curso_stgr` o `curso_crt`, el
valor no llega en `valor`, sino que se compone a partir de `ini_dia`, `ini_mes`,
`fin_dia`, `fin_mes` y se serializa como JSON (`{ini_dia, ini_mes, fin_dia, fin_mes}`)
antes de guardarlo.

## Endpoint

- URL: `/src/configuracion/parametros_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/parametros_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `parametro` | `string` | controller | Si | Código del parámetro (`curso_stgr`, `curso_crt`, `jefe_calendario`, `nota_corte`, `idioma_default`, `ambito`, …) |
| `valor` | `string` | controller | No | Valor a guardar; se ignora para `curso_stgr`/`curso_crt` (se recompone desde `ini_*`/`fin_*`) |
| `ini_dia` | `integer` | controller | No | Solo `curso_stgr`/`curso_crt`: día de inicio del periodo |
| `ini_mes` | `integer` | controller | No | Solo `curso_stgr`/`curso_crt`: mes de inicio del periodo |
| `fin_dia` | `integer` | controller | No | Solo `curso_stgr`/`curso_crt`: día de fin del periodo |
| `fin_mes` | `integer` | controller | No | Solo `curso_stgr`/`curso_crt`: mes de fin del periodo |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Éxito: `success: true`, `data: "ok"`.
- Error: `success: false` con el texto de error del repositorio (`getErrorTxt()`), sin literal `_()` fijo.

## Permisos

- No hay control de permisos propio en el controller; la autorización de oficina se
  resuelve en el frontend (`parametros.php`) y en `$_SESSION['oPerm']`. No inferir
  permisos concretos aquí.

## Casos De Uso

- No usa capa `application/`: la lógica está en el controller, apoyada en
  `ConfigSchemaRepositoryInterface`.

## Frontend Relacionado

- `frontend/configuracion/controller/parametros.php` (cada campo del form emite un hash
  con `url = src/configuracion/parametros_update` y el hidden `parametro`).
