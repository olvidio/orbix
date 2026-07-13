---
id: "actividades.lista_sr_csv_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_sr_csv_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_datos.php"
entrada: ["post.periodo:string", "post.year:string", "post.dl_org:string", "post.empiezamin:string", "post.empiezamax:string", "post.c_activ:array", "post.status:array", "post.id_cdc:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado la preferencia"]
frontend_referencias: ["frontend/actividades/controller/lista_sr_csv.php"]
casos_uso: ["src\\actividades\\application\\ListaSrCsvListado"]
tags: ["actividades", "lista", "sr", "csv", "datos"]
estado_revision: "revisado"
---

# Lista Sr Csv Datos

Prepara el listado de actividades de San Rafael (SR): devuelve la tabla ya formateada y los datos
crudos para exportar a CSV desde el frontend. Además persiste los filtros como preferencia del usuario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

- Guarda la preferencia `busqueda_activ_sr` del usuario (status, periodo, tipo_activ, ubis compartidas).
- Filtra actividades por status, tipo (`^17<c_activ>` si mi sf/sv es 1, `^2[789]<c_activ>` en otro caso),
  periodo/fechas y `dl_org`, y añade las actividades de las ubicaciones compartidas indicadas (`id_cdc`),
  deduplicando por `id_activ`.
- Devuelve la tabla renderizada más los datos crudos (cabeceras + valores) para exportación CSV.

## Endpoint

- URL: `/src/actividades/lista_sr_csv_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `periodo` | `string` | controller | No | Por defecto `curso_ca`; `desdeHoy` filtra por `f_fin` |
| `year` | `string` | controller | No | Año/curso |
| `dl_org` | `string` | controller | No | Delegación organizadora |
| `empiezamin` / `empiezamax` | `string` | controller | No | Rango de fechas |
| `c_activ` | `array` | controller | No | Tipos de actividad (se agrupan en clase de carácter) |
| `status` | `array` | controller | No | Uno o varios status |
| `id_cdc` | `array` | controller | No | Ubicaciones compartidas a incluir |

## Salida

- Helper: `ContestarJson::enviar` (data serializada como string JSON; el front hace segundo `JSON.parse`).
- Forma: `standard_envelope_string_data`.
- `data` con claves: `html_tabla` (tabla renderizada), `a_cabeceras` y `a_valores` (datos crudos para
  CSV), `titulo` y `pref_error` (texto de error si falló al guardar la preferencia; vacío si todo bien).

## Errores conocidos

- `hay un error, no se ha guardado la preferencia` (devuelto en `data.pref_error`, no como `success: false`)

## Permisos

- Sin control de permisos que bloquee el endpoint. Lee `$_SESSION['oPerm']` para ocultar el nombre de
  tipo "(sin especificar)" a `sg`/`vcsd`/`des` sin `admin`. La autorización se resuelve en el frontend.

## Casos De Uso

- `src\actividades\application\ListaSrCsvListado`

## Frontend Relacionado

- `frontend/actividades/controller/lista_sr_csv.php` (renderiza y ofrece la exportación CSV).
