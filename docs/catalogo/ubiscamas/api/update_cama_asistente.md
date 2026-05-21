---
id: "ubiscamas.update_cama_asistente"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/update_cama_asistente"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/update_cama_asistente.php"
entrada: ["post.ctx:string", "post.id_cama:string", "post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "raw_response"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\asistentes\\application\\services\\AsistenteActividadService"]
tags: ["ubiscamas", "update", "cama", "asistente"]
estado_revision: "generado"
---

# Update Cama Asistente

Servicio de aplicación para operaciones de asistentes que requieren coordinación entre múltiples repositorios

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubiscamas/update_cama_asistente`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/update_cama_asistente.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ctx` | `string` | controller | No | controller |
| `id_cama` | `string` | controller | No | controller |
| `id_nom` | `integer` | controller | No | controller |

## Salida

- Helper: `echo`
- Forma: `raw_response`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Servicio de aplicación para operaciones de asistentes que requieren coordinación entre múltiples repositorios @package orbix @subpackage asistentes @author Daniel Serrabou @version 1.0 @created 16/12/2025

## Casos De Uso

- `src\asistentes\application\services\AsistenteActividadService`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.