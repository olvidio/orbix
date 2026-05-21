---
id: "encargossacd.listas_com_txt_get"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_com_txt_get"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_get.php"
entrada: ["post.clave:mixed", "post.idioma:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasComTxtGetData"
respuesta_data: ["texto:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_com_txt.php", "frontend/encargossacd/controller/listas_com_txt_get.php"]
casos_uso: ["src\\encargossacd\\application\\ListasComTxtGet"]
tags: ["encargossacd", "listas", "com", "txt", "get"]
estado_revision: "generado"
---

# Listas Com Txt Get

Lectura del texto de comunicacion para un par (clave, idioma). Extraido de `EncargoTextoListasComAjax` (rama `que=get_texto`) para eliminar el dispatcher multiproposito (criterio `refactor.md`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/listas_com_txt_get`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_com_txt_get.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `clave` | `mixed` | controller | No | controller |
| `idioma` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_ListasComTxtGetData`):
  - `texto` (`string`)

## Efectos colaterales

- Extraido de `EncargoTextoListasComAjax` (rama `que=get_texto`) para eliminar el dispatcher multiproposito (criterio `refactor.md`).

## Casos De Uso

- `src\encargossacd\application\ListasComTxtGet`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_com_txt.php`
- `frontend/encargossacd/controller/listas_com_txt_get.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.