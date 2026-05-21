---
id: "misas.desplegable_sacd"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/desplegable_sacd"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/desplegable_sacd.php"
entrada: ["post.dia:string", "post.id_sacd:integer", "post.id_zona:integer", "post.seleccion:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/support/CuadriculaZonaRenderer.php"]
casos_uso: ["src\\misas\\application\\DesplegableSacdData"]
tags: ["misas", "desplegable", "sacd"]
estado_revision: "generado"
---

# Desplegable Sacd

Opciones del desplegable dinámico de SACD en el modal de la cuadrícula de zona. El payload sigue el espíritu del contrato de `refactor.md` (id, selected, filas ordenadas). `rows` conserva el orden del HTML legacy: opción actual, opción en blanco si aplica, resto ordenado por clave.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/desplegable_sacd`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/desplegable_sacd.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dia` | `string` | controller | No | controller |
| `id_sacd` | `integer` | controller | No | controller |
| `id_zona` | `integer` | controller | No | controller |
| `seleccion` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\DesplegableSacdData`

## Frontend Relacionado

- `frontend/misas/support/CuadriculaZonaRenderer.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.