---
id: "misas.guardar_encargo_zona"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/guardar_encargo_zona"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/guardar_encargo_zona.php"
entrada: ["post.descripcion_lugar:mixed", "post.encargo:mixed", "post.id_enc:mixed", "post.id_tipo_enc:mixed", "post.id_ubi:mixed", "post.id_zona:mixed", "post.idioma_enc:mixed", "post.observ:mixed", "post.orden:mixed", "post.prioridad:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_GuardarEncargoZonaData"
respuesta_data: ["error:string, data: array{id_enc: int, lugar: string}"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\GuardarEncargoZona"]
tags: ["misas", "guardar", "encargo", "zona"]
estado_revision: "generado"
---

# Guardar Encargo Zona

Inserta o actualiza un `Encargo` del grupo `ZONAS_MISAS`. - Si `id_enc` es 0 se crea uno nuevo con `getNewId()`. - Si hay valor, se carga el existente y se modifica. Devuelve un array con: - `error`: texto vacio si todo fue bien, mensaje del repositorio si no. - `data` : payload para el frontend con `id_enc`, `lugar` y el nombre del centro si se resolvio.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/guardar_encargo_zona`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_encargo_zona.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `descripcion_lugar` | `mixed` | controller | No | controller |
| `encargo` | `mixed` | controller | No | controller |
| `id_enc` | `mixed` | controller | No | controller |
| `id_tipo_enc` | `mixed` | controller | No | controller |
| `id_ubi` | `mixed` | controller | No | controller |
| `id_zona` | `mixed` | controller | No | controller |
| `idioma_enc` | `mixed` | controller | No | controller |
| `observ` | `mixed` | controller | No | controller |
| `orden` | `mixed` | controller | No | controller |
| `prioridad` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_GuardarEncargoZonaData`):
  - `error` (`string, data: array{id_enc: int, lugar: string}`)

## Casos De Uso

- `src\misas\application\GuardarEncargoZona`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.