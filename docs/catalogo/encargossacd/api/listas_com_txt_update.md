---
id: "encargossacd.listas_com_txt_update"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_com_txt_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_update.php"
entrada: ["post.clave:mixed", "post.comunicacion:mixed", "post.idioma:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasComTxtUpdateData"
respuesta_data: ["ok:true"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_com_txt.php", "frontend/encargossacd/controller/listas_com_txt_update.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComTxtUpdate"]
tags: ["encargossacd", "listas", "com", "txt", "update"]
estado_revision: "generado"
---

# Listas Com Txt Update

Mutacion del texto de comunicacion para un par (clave, idioma). Si el texto llega vacio, se elimina la fila. Extraido de `EncargoTextoListasComAjax` (rama `que=update`) para eliminar el dispatcher multiproposito (criterio `refactor.md`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/listas_com_txt_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clave` | `mixed` | controller | No | controller |
| `comunicacion` | `mixed` | controller | No | controller |
| `idioma` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_ListasComTxtUpdateData`):
  - `ok` (`true`)

## Efectos colaterales

- Si el texto llega vacio, se elimina la fila.
- Extraido de `EncargoTextoListasComAjax` (rama `que=update`) para eliminar el dispatcher multiproposito (criterio `refactor.md`).

## Casos De Uso

- `src\encargossacd\application\ListasComTxtUpdate`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_com_txt.php`
- `frontend/encargossacd/controller/listas_com_txt_update.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.