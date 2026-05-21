---
id: "encargossacd.ctr_get_select_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/ctr_get_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/ctr_get_select_data.php"
entrada: ["post.filtro_ctr:mixed", "post.id_ubi:mixed", "post.id_zona:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoCtrSelectDataData"
respuesta_data: ["id:string, name: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/ctr_ficha.php", "frontend/encargossacd/controller/encargo_ver.php", "frontend/encargossacd/model/DesplCentros.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoCtrSelectData"]
tags: ["encargossacd", "ctr", "get", "select", "data"]
estado_revision: "generado"
---

# Ctr Get Select Data

Payload JSON para el desplegable de centros segun filtro (y zona opcional). Devuelve el contrato estandar definido en `refactor.md` (`id`, `name`, `opciones`, `selected`, `blanco`, `val_blanco`, `action`) para que el frontend monte el `<select>` con `fnjs_construir_desplegable` (o el modelo `frontend/encargossacd/model/DesplCentros`). Importante: esta clase vive en capa `application` y por tanto **no** puede instanciar `frontend\shared\web\Desplegable` (ver `refactor.md`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/ctr_get_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/ctr_get_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `filtro_ctr` | `mixed` | controller | No | controller |
| `id_ubi` | `mixed` | controller | No | controller |
| `id_zona` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_EncargoCtrSelectDataData`):
  - `id` (`string, name: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string`)

## Casos De Uso

- `src\encargossacd\application\EncargoCtrSelectData`

## Frontend Relacionado

- `frontend/encargossacd/controller/ctr_ficha.php`
- `frontend/encargossacd/controller/encargo_ver.php`
- `frontend/encargossacd/model/DesplCentros.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.