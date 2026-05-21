---
id: "encargossacd.zonas_get_select_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/zonas_get_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/zonas_get_select_data.php"
entrada: ["post.id_zona:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoZonasSelectDataData"
respuesta_data: ["label_prefix:string, id: string, name: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/encargo_ver.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoZonasSelectData"]
tags: ["encargossacd", "zonas", "get", "select", "data"]
estado_revision: "generado"
---

# Zonas Get Select Data

Payload JSON para el desplegable de zonas (grupo «zonas misas»). Devuelve el contrato estandar definido en `refactor.md`, sin instanciar `frontend\shared\web\Desplegable` (responsabilidad exclusiva del frontend).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/zonas_get_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/zonas_get_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_EncargoZonasSelectDataData`):
  - `label_prefix` (`string, id: string, name: string, opciones: array<string, string>, selected: string, blanco: bool, val_blanco: string, action: string`)

## Casos De Uso

- `src\encargossacd\application\EncargoZonasSelectData`

## Frontend Relacionado

- `frontend/encargossacd/controller/encargo_ver.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.