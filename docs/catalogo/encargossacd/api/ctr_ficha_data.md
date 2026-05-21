---
id: "encargossacd.ctr_ficha_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/ctr_ficha_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/ctr_ficha_data.php"
entrada: ["post.filtro_ctr:mixed", "post.id_ubi:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_CtrFichaDataData"
respuesta_data: ["filtro_ctr:int, opciones_seccion: array<string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/ctr_ficha.php"]
casos_uso: ["src\\encargossacd\\application\\CtrFichaData"]
tags: ["encargossacd", "ctr", "ficha", "data"]
estado_revision: "generado"
---

# Ctr Ficha Data

Datos de la pantalla `ctr_ficha`: - calcula el `filtro_ctr` efectivo a partir del centro (cuando no viene del POST) - devuelve las `opciones_seccion` para el desplegable de grupo de ctrs. Reemplaza la lectura directa de repos y el acceso a `EncargoAplicacionService` que el frontend hacia en `ctr_ficha.php`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/ctr_ficha_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_ficha_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro_ctr` | `mixed` | controller | No | controller |
| `id_ubi` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_CtrFichaDataData`):
  - `filtro_ctr` (`int, opciones_seccion: array<string, string>`)

## Casos De Uso

- `src\encargossacd\application\CtrFichaData`

## Frontend Relacionado

- `frontend/encargossacd/controller/ctr_ficha.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.