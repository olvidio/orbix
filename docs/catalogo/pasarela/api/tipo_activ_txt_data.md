---
id: "pasarela.tipo_activ_txt_data"
tipo: "endpoint"
modulo: "pasarela"
url: "/src/pasarela/tipo_activ_txt_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/pasarela/infrastructure/ui/http/controllers/tipo_activ_txt_data.php"
entrada: ["post.id_tipo_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "pasarela_TipoActivTxtDataData"
respuesta_data: ["tipo_txt:string"]
requiere_hashb: false
frontend_referencias: ["frontend/pasarela/controller/activacion_ajax.php", "frontend/pasarela/controller/contribucion_no_duerme_ajax.php", "frontend/pasarela/controller/contribucion_reserva_ajax.php", "frontend/pasarela/controller/nombre_ajax.php"]
casos_uso: ["src\\pasarela\\application\\TipoActivTxtData"]
tags: ["pasarela", "tipo", "activ", "txt", "data"]
estado_revision: "generado"
---

# Tipo Activ Txt Data

Devuelve el texto descriptivo (`sfsv asistentes actividad`) para un `id_tipo_activ`. Lo consumen los formularios `form_modificar` desde el frontend para mostrar a qué tipo de actividad corresponde la fila editada.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/pasarela/tipo_activ_txt_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/pasarela/infrastructure/ui/http/controllers/tipo_activ_txt_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_tipo_activ` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `pasarela_TipoActivTxtDataData`):
  - `tipo_txt` (`string`)

## Efectos colaterales

- Devuelve el texto descriptivo (`sfsv asistentes actividad`) para un `id_tipo_activ`.

## Casos De Uso

- `src\pasarela\application\TipoActivTxtData`

## Frontend Relacionado

- `frontend/pasarela/controller/activacion_ajax.php`
- `frontend/pasarela/controller/contribucion_no_duerme_ajax.php`
- `frontend/pasarela/controller/contribucion_reserva_ajax.php`
- `frontend/pasarela/controller/nombre_ajax.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.