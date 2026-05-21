---
id: "asistentes.lista_ultim_que_ctr_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_ultim_que_ctr_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_ultim_que_ctr_data.php"
entrada: ["post.curso:string", "post.que:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asistentes_ListaUltimQueCtrDataData"
respuesta_data: ["opciones_centros:array"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_ultim_que_ctr.php"]
casos_uso: ["src\\asistentes\\application\\ListaUltimQueCtrData"]
tags: ["asistentes", "lista", "ultim", "que", "ctr", "data"]
estado_revision: "generado"
---

# Lista Ultim Que Ctr Data

JSON para {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/lista_ultim_que_ctr_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_ultim_que_ctr_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `curso` | `string` | application | No | application |
| `que` | `string` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `asistentes_ListaUltimQueCtrDataData`):
  - `opciones_centros` (`array`)

## Efectos colaterales

- Hash del formulario y URL del action en {@see \frontend\asistentes\helpers\ListaUltimQueCtrRender}.

## Casos De Uso

- `src\asistentes\application\ListaUltimQueCtrData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_ultim_que_ctr.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.