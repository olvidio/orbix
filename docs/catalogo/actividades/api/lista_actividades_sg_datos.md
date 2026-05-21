---
id: "actividades.lista_actividades_sg_datos"
tipo: "endpoint"
modulo: "actividades"
url: "/src/actividades/lista_actividades_sg_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividades/infrastructure/ui/http/controllers/lista_actividades_sg_datos.php"
entrada: ["post.continuar:string", "post.dl_org:string", "post.empiezamax:string", "post.empiezamin:string", "post.id_ubi:integer", "post.periodo:string", "post.scroll_id:string", "post.sel:array", "post.stack_go:integer", "post.status:integer", "post.tipo_activ_sg:string", "post.year:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/actividades/controller/lista_actividades_sg.php"]
casos_uso: ["src\\actividades\\application\\ListaActividadesSgListado"]
tags: ["actividades", "lista", "sg", "datos"]
estado_revision: "generado"
---

# Lista Actividades Sg Datos

JSON del listado para `lista_actividades_sg`: POST → {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividades/lista_actividades_sg_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividades/infrastructure/ui/http/controllers/lista_actividades_sg_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `continuar` | `string` | controller | No | controller |
| `dl_org` | `string` | controller | No | controller |
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `id_ubi` | `integer` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |
| `scroll_id` | `string` | controller | No | controller |
| `sel` | `array` | controller | No | controller |
| `stack_go` | `integer` | controller | No | controller |
| `status` | `integer` | controller | No | controller |
| `tipo_activ_sg` | `string` | controller | No | controller |
| `year` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Permisos

- Permiso oficina `des`

## Casos De Uso

- `src\actividades\application\ListaActividadesSgListado`

## Frontend Relacionado

- `frontend/actividades/controller/lista_actividades_sg.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.