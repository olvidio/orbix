---
id: "encargossacd.comprobaciones_ctr"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/comprobaciones_ctr"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/comprobaciones_ctr.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_EncargoComprobacionesCtrData"
respuesta_data: ["texto:string"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/comprobaciones.php"]
casos_uso: ["src\\encargossacd\\application\\EncargoComprobacionesCtr"]
tags: ["encargossacd", "comprobaciones", "ctr"]
estado_revision: "generado"
---

# Comprobaciones Ctr

Elimina encargos ligados a centros inactivos y sacd huérfanos (misma lógica que el antiguo `frontend/encargossacd/controller/comprobaciones.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/comprobaciones_ctr`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/comprobaciones_ctr.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_EncargoComprobacionesCtrData`):
  - `texto` (`string`)

## Efectos colaterales

- Elimina encargos ligados a centros inactivos y sacd huérfanos (misma lógica que el antiguo `frontend/encargossacd/controller/comprobaciones.php`).

## Casos De Uso

- `src\encargossacd\application\EncargoComprobacionesCtr`

## Frontend Relacionado

- `frontend/encargossacd/controller/comprobaciones.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.