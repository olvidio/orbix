---
id: "misas.crear_nuevo_periodo_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/crear_nuevo_periodo_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/crear_nuevo_periodo_data.php"
entrada: ["post.empiezamax:mixed", "post.empiezamin:mixed", "post.id_zona:mixed", "post.orden:mixed", "post.periodo:mixed", "post.seleccion:mixed", "post.tipo_plantilla:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/crear_nuevo_periodo.php"]
casos_uso: ["src\\misas\\application\\CrearNuevoPeriodoData", "src\\misas\\application\\support\\MisasBuildInput"]
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
| `empiezamax` | `mixed` | controller | No | controller |
| `empiezamin` | `mixed` | controller | No | controller |
| `id_zona` | `mixed` | controller | No | controller |
| `orden` | `mixed` | controller | No | controller |
| `periodo` | `mixed` | controller | No | controller |
| `seleccion` | `mixed` | controller | No | controller |
| `tipo_plantilla` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\CrearNuevoPeriodoData`
- `src\misas\application\support\MisasBuildInput`

## Frontend Relacionado

- `frontend/misas/controller/crear_nuevo_periodo.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.