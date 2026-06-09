---
id: "ubiscamas.update_cama_asistente"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/update_cama_asistente"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/update_cama_asistente.php"
entrada: ["post.ctx:string", "post.id_activ:integer", "post.id_cama:string", "post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "raw_response"
respuesta_data_schema: "ubiscamas_UpdateCamaAsistenteData"
respuesta_data: ["success:bool, mensaje: string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\ubiscamas\\application\\UpdateCamaAsistente"]
tags: ["ubiscamas", "update", "cama", "asistente"]
estado_revision: "generado"
---

# Update Cama Asistente

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubiscamas/update_cama_asistente`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/update_cama_asistente.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx` | `string` | controller | No | controller |
| `id_activ` | `integer` | controller | No | controller |
| `id_cama` | `string` | controller | No | controller |
| `id_nom` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubiscamas_UpdateCamaAsistenteData`):
  - `success` (`bool, mensaje: string`)

## Casos De Uso

- `src\ubiscamas\application\UpdateCamaAsistente`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.