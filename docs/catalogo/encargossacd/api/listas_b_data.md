---
id: "encargossacd.listas_b_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_b_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_b_data.php"
entrada: ["post.sf:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasBDataData"
respuesta_data: ["cabecera_left:string", "cabecera_right:string", "cabecera_right_2:string", "Html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_b.php"]
casos_uso: ["src\\encargossacd\\application\\ListasBData"]
tags: ["encargossacd", "listas", "b", "data"]
estado_revision: "generado"
---

# Listas B Data

Genera el listado de atencion SACD "b" (cr 9/05, Anexo2, 9.4 b). Sustituye la logica de `frontend/encargossacd/controller/listas_b.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/listas_b_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_b_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sf` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_ListasBDataData`):
  - `cabecera_left` (`string`)
  - `cabecera_right` (`string`)
  - `cabecera_right_2` (`string`)
  - `Html` (`string`)

## Casos De Uso

- `src\encargossacd\application\ListasBData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_b.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.