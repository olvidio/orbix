---
id: "encargossacd.listas_d_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/listas_d_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/listas_d_data.php"
entrada: ["post.sf:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_ListasDDataData"
respuesta_data: ["cabecera_left:string", "cabecera_right:string", "cabecera_right_2:string", "Html:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/listas_d.php"]
casos_uso: ["src\\encargossacd\\application\\ListasDData"]
tags: ["encargossacd", "listas", "d", "data"]
estado_revision: "generado"
---

# Listas D Data

Genera el listado "d" de atencion SACD (cr 9/20, 10). Sustituye la logica de `frontend/encargossacd/controller/listas_d.php`. La vista original devolvia dos tablas HTML sueltas (cabecera + listado); aqui se componen ambas en `Html` para que el frontend solo tenga que volcarlas al cliente.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/listas_d_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/listas_d_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sf` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_ListasDDataData`):
  - `cabecera_left` (`string`)
  - `cabecera_right` (`string`)
  - `cabecera_right_2` (`string`)
  - `Html` (`string`)

## Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`

## Casos De Uso

- `src\encargossacd\application\ListasDData`

## Frontend Relacionado

- `frontend/encargossacd/controller/listas_d.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.