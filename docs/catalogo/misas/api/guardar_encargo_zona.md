---
id: "misas.guardar_encargo_zona"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/guardar_encargo_zona"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/guardar_encargo_zona.php"
entrada: ["post.descripcion_lugar:string", "post.encargo:string", "post.id_enc:integer", "post.id_tipo_enc:integer", "post.id_ubi:integer", "post.id_zona:integer", "post.idioma_enc:string", "post.observ:string", "post.orden:integer", "post.prioridad:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
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
| `descripcion_lugar` | `string` | controller+application | No | controller+application |
| `encargo` | `string` | controller+application | No | controller+application |
| `id_enc` | `integer` | controller+application | No | controller+application |
| `id_tipo_enc` | `integer` | controller+application | No | controller+application |
| `id_ubi` | `integer` | controller+application | No | controller+application |
| `id_zona` | `integer` | controller+application | No | controller+application |
| `idioma_enc` | `string` | controller+application | No | controller+application |
| `observ` | `string` | controller+application | No | controller+application |
| `orden` | `integer` | controller+application | No | controller+application |
| `prioridad` | `integer` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\GuardarEncargoZona`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.