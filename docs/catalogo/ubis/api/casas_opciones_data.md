---
id: "ubis.casas_opciones_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/casas_opciones_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/casas_opciones_data.php"
entrada: ["post.active:mixed", "post.id_ubi_in:mixed", "post.sf:mixed", "post.sv:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_CasasOpcionesDataData"
respuesta_data: ["opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/casas/controller/calendario_ubi_resumen.php", "frontend/shared/web/CasasQue.php"]
casos_uso: ["src\\ubis\\application\\CasasOpcionesData"]
tags: ["ubis", "casas", "opciones", "data"]
estado_revision: "generado"
---

# Casas Opciones Data

Devuelve el payload (solo datos) para poblar el <select> de casas en `frontend\shared\web\CasasQue`. La vista/componente frontend es quien construye el HTML del desplegable; aquí solo se exponen las opciones. Sustituye el acceso directo desde `CasasQue` al repositorio `CasaDlRepositoryInterface` (separación frontend ↔ backend, ver `refactor.md`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/casas_opciones_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/casas_opciones_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `active` | `mixed` | controller | No | controller |
| `id_ubi_in` | `mixed` | controller | No | controller |
| `sf` | `mixed` | controller | No | controller |
| `sv` | `mixed` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `ubis_CasasOpcionesDataData`):
  - `opciones` (`array`)

## Casos De Uso

- `src\ubis\application\CasasOpcionesData`

## Frontend Relacionado

- `frontend/casas/controller/calendario_ubi_resumen.php`
- `frontend/shared/web/CasasQue.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.