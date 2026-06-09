---
id: "misas.ver_cuadricula_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_cuadricula_zona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php"
entrada: ["post.columna:mixed", "post.empiezamax:mixed", "post.empiezamin:mixed", "post.fila:mixed", "post.id_zona:mixed", "post.orden:mixed", "post.periodo:mixed", "post.seleccion:mixed", "post.tipo_plantilla:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_cuadricula_zona.php", "frontend/misas/controller/ver_cuadricula_zona.php"]
casos_uso: ["src\\misas\\application\\CuadriculaZonaGridData", "src\\misas\\application\\support\\MisasBuildInput"]
tags: ["misas", "ver", "cuadricula", "zona", "data"]
estado_revision: "generado"
---

# Ver Cuadricula Zona Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/ver_cuadricula_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `columna` | `mixed` | controller | No | controller |
| `empiezamax` | `mixed` | controller | No | controller |
| `empiezamin` | `mixed` | controller | No | controller |
| `fila` | `mixed` | controller | No | controller |
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

- `src\misas\application\CuadriculaZonaGridData`
- `src\misas\application\support\MisasBuildInput`

## Frontend Relacionado

- `frontend/misas/controller/modificar_cuadricula_zona.php`
- `frontend/misas/controller/ver_cuadricula_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.