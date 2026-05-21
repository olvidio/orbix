---
id: "encargossacd.listas_a_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_a_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_a_data.php"
entrada: ["post.sf:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasADataData"
respuesta_data: ["cabecera_left:string", "cabecera_right:string", "cabecera_right_2:string", "Html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_a.php"]
casos_uso: ["src\\encargossacd\\application\\ListasAData"]
tags: ["encargossacd", "listas", "a", "data"]
estado_revision: "generado"
---

# Listas A Data

Genera el listado de atencion SACD "a" (cr 9/05, Anexo2, 9.4 a). Sustituye la logica que habia en `frontend/encargossacd/controller/listas_a.php`. Devuelve el HTML completo junto con los textos de cabecera, listos para inyectarlos en la vista `listas.phtml`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/listas_a_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_a_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sf` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_ListasADataData`):
  - `cabecera_left` (`string`)
  - `cabecera_right` (`string`)
  - `cabecera_right_2` (`string`)
  - `Html` (`string`)

## Casos De Uso

- `src\encargossacd\application\ListasAData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_a.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.