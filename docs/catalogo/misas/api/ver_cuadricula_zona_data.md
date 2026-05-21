---
id: "misas.ver_cuadricula_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_cuadricula_zona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/ver_cuadricula_zona_data.php"
entrada: ["post.columna:integer", "post.empiezamax:string", "post.empiezamin:string", "post.fila:integer", "post.id_zona:integer", "post.orden:string", "post.periodo:string", "post.seleccion:integer", "post.tipo_plantilla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_cuadricula_zona.php", "frontend/misas/controller/ver_cuadricula_zona.php", "frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\CuadriculaZonaGridData"]
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
| `columna` | `integer` | controller | No | controller |
| `empiezamax` | `string` | controller | No | controller |
| `empiezamin` | `string` | controller | No | controller |
| `fila` | `integer` | controller | No | controller |
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

- `src\misas\application\CuadriculaZonaGridData`

## Frontend Relacionado

- `frontend/misas/controller/modificar_cuadricula_zona.php`
- `frontend/misas/controller/ver_cuadricula_zona.php`
- `frontend/misas/support/CuadriculaZonaRenderer.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.