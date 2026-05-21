---
id: "actividades.calendario_listas_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/calendario_listas_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/calendario_listas_datos.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_cdc:array", "post.periodo:string", "post.que:string", "post.ver_ctr:string", "post.year:string", "post.yeardefault:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/calendario_listas.php"]
casos_uso: ["src\\actividades\\application\\CalendarioListasDatos"]
tags: ["actividades", "calendario", "listas", "datos"]
estado_revision: "generado"
---

# Calendario Listas Datos

Endpoint backend para `calendario_listas`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/calendario_listas_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/calendario_listas_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `id_cdc` | `array` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |
| `que` | `string` | controller | No | controller |
| `ver_ctr` | `string` | controller | No | controller |
| `year` | `string` | controller | No | controller |
| `yeardefault` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\CalendarioListasDatos`

## Frontend Relacionado

- `frontend/actividades/controller/calendario_listas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.