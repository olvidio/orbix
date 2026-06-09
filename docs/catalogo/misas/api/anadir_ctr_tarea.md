---
id: "misas.anadir_ctr_tarea"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/anadir_ctr_tarea"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/anadir_ctr_tarea.php"
entrada: ["post.id_item:mixed", "post.id_tarea:mixed", "post.id_ubi:mixed", "post.que:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_AnadirCtrTareaData"
respuesta_data: ["error:string"]
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\misas\\application\\AnadirCtrTarea"]
tags: ["misas", "anadir", "ctr", "tarea"]
estado_revision: "generado"
---

# Anadir Ctr Tarea

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/anadir_ctr_tarea`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/anadir_ctr_tarea.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `mixed` | controller | No | controller |
| `id_tarea` | `mixed` | controller | No | controller |
| `id_ubi` | `mixed` | controller | No | controller |
| `que` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_AnadirCtrTareaData`):
  - `error` (`string`)

## Casos De Uso

- `src\misas\application\AnadirCtrTarea`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.