---
id: "actividades.lista_activ_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_activ_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_activ_datos.php"
entrada: ["post.que:string", "post.status:mixed", "post.id_tipo_activ:string", "post.filtro_lugar:string", "post.id_ubi:integer", "post.periodo:string", "post.year:string", "post.dl_org:string", "post.empiezamin:string", "post.empiezamax:string", "post.c_activ:array", "post.asist:array", "post.seccion:array", "post.ssfsv:string", "post.sasistentes:string", "post.sactividad:string", "post.snom_tipo:string", "post.titulo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "actividades_ListaActivTablaData"
respuesta_data: ["titulo:string", "ver_hora:integer", "ver_tarifa:integer", "ver_sacd:integer", "a_cabeceras:list<array<string, mixed>|string>", "a_valores:array"]
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/actividades/controller/lista_activ.php", "frontend/actividades/controller/lista_activ_que.php"]
casos_uso: ["src\\actividades\\application\\ListaActivTabla"]
tags: ["actividades", "lista", "activ", "datos"]
estado_revision: "revisado"
---

# Lista Activ Datos

Devuelve los datos (cabeceras + filas) del listado de actividades `lista_activ` a partir de los filtros
enviados por POST. El HTML de la tabla lo genera el controlador frontend, que firma los `link_spec` de
las celdas y llama a `Lista::mostrar_tabla`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Construye el listado de actividades filtrando por status, tipo de actividad (compuesto desde
`ssfsv`/`sasistentes`/`sactividad` o `id_tipo_activ`), ubicación, periodo/fechas y `dl_org`. Las columnas
visibles dependen del modo `que` y de los permisos de oficina del usuario:

- `ver_hora` (hora ini/fin) se activa en modos completos/sg/sr o con permiso `vcsd`/`des`.
- `ver_tarifa` y `ver_sacd` se ocultan para `sg` sin `admin` en modo `list_activ_inv_sg`.
- La columna sf/sv solo aparece con permiso `vcsd`/`des`.
- Cada fila añade (salvo en DMZ) un enlace "ver asistentes" (`link_spec` a `lista_asistentes.php`).

## Endpoint

- URL: `/src/actividades/lista_activ_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_activ_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `que` | `string` | controller | No | Modo del listado (`list_activ_compl`, `list_activ_inv_sg`, `list_activ_sr`…) |
| `status` | `mixed` | controller | No | Entero o array de status; array se une con operador `~` |
| `id_tipo_activ` | `string` | controller | No | Filtro directo por tipo (regexp `^...`) |
| `filtro_lugar` | `string` | controller | No | Leído por el controller (compat) |
| `id_ubi` | `integer` | controller | No | Ubicación |
| `periodo` | `string` | controller | No | Alias de periodo; `desdeHoy` filtra por `f_fin` |
| `year` | `string` | controller | No | Año/curso |
| `dl_org` | `string` | controller | No | Delegación organizadora |
| `empiezamin` / `empiezamax` | `string` | controller | No | Rango de fechas (fuerza periodo `otro`) |
| `c_activ` / `asist` / `seccion` | `array` | controller | No | Componen el tipo en modos sg/sr |
| `ssfsv` / `sasistentes` / `sactividad` / `snom_tipo` | `string` | controller | No | Componen `id_tipo_activ` |
| `titulo` | `string` | controller | No | Título en modos sg/sr |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- Payload en `data` (schema `actividades_ListaActivTablaData`):
  - `titulo` (`string`)
  - `ver_hora` (`integer`)
  - `ver_tarifa` (`integer`)
  - `ver_sacd` (`integer`)
  - `a_cabeceras` (`list<array<string, mixed>|string>`)
  - `a_valores` (`array` de filas; algunas celdas traen `link_spec` sin firmar)

## Permisos

- El controller lee `$_SESSION['oPerm']` para calcular flags de oficina (`vcsd`, `des`, `sg`, `admin`)
  que solo condicionan las columnas visibles, no bloquean el endpoint. La autorización real se resuelve
  en el frontend y en `$_SESSION['oPerm']`. En DMZ (`is_dmz`) se omite la columna de acciones.

## Casos De Uso

- `src\actividades\application\ListaActivTabla`

## Frontend Relacionado

- `frontend/actividades/controller/lista_activ.php` (firma los `link_spec` y renderiza la tabla).
- `frontend/actividades/controller/lista_activ_que.php` (formulario de filtros).
