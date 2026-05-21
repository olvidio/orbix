---
id: "misas.crear_nuevo_periodo_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/crear_nuevo_periodo_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/crear_nuevo_periodo_data.php"
entrada: ["post.empiezamax:string", "post.empiezamin:string", "post.id_zona:integer", "post.orden:string", "post.periodo:string", "post.seleccion:integer", "post.tipo_plantilla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/crear_nuevo_periodo.php", "frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\CrearNuevoPeriodoData"]
tags: ["misas", "crear", "nuevo", "periodo", "data"]
estado_revision: "generado"
---

# Crear Nuevo Periodo Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/crear_nuevo_periodo_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/crear_nuevo_periodo_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `id_zona` | `integer` | controller | No | controller |
| `orden` | `string` | controller | No | controller |
| `periodo` | `string` | controller | No | controller |
| `seleccion` | `integer` | controller | No | controller |
| `tipo_plantilla` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\CrearNuevoPeriodoData`

## Frontend Relacionado

- `frontend/misas/controller/crear_nuevo_periodo.php`
- `frontend/misas/support/CuadriculaZonaRenderer.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.