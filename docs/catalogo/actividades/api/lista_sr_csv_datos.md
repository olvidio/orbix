---
id: "actividades.lista_sr_csv_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_sr_csv_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_datos.php"
entrada: ["post.c_activ:array", "post.dl_org:string", "post.empiezamax:string", "post.empiezamin:string", "post.id_cdc:array", "post.periodo:string", "post.status:array", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/lista_sr_csv.php"]
casos_uso: ["src\\actividades\\application\\ListaSrCsvListado"]
tags: ["actividades", "lista", "sr", "csv", "datos"]
estado_revision: "generado"
---

# Lista Sr Csv Datos

Endpoint backend para `lista_sr_csv` (listado SR + exportacion).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/lista_sr_csv_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_sr_csv_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `c_activ` | `array` | controller | No | controller |
| `dl_org` | `string` | controller | No | controller |
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `id_cdc` | `array` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |
| `status` | `array` | controller | No | controller |
| `year` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `sg`
- Permiso oficina `vcsd`
- Permiso oficina `des`
- Permiso oficina `admin`

## Casos De Uso

- `src\actividades\application\ListaSrCsvListado`

## Frontend Relacionado

- `frontend/actividades/controller/lista_sr_csv.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.