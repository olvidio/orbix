---
id: "actividades.lista_centros_activ_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_centros_activ_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_centros_activ_datos.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_ctr:array", "post.id_ctr_num:integer", "post.periodo:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/lista_centros_activ.php"]
casos_uso: ["src\\actividades\\application\\ListaCentrosActivDatos"]
tags: ["actividades", "lista", "centros", "activ", "datos"]
estado_revision: "generado"
---

# Lista Centros Activ Datos

Endpoint backend para `lista_centros_activ`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/lista_centros_activ_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_centros_activ_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `id_ctr` | `array` | controller | No | controller |
| `id_ctr_num` | `integer` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |
| `year` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\actividades\application\ListaCentrosActivDatos`

## Frontend Relacionado

- `frontend/actividades/controller/lista_centros_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.