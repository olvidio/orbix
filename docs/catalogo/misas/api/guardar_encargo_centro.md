---
id: "misas.guardar_encargo_centro"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/guardar_encargo_centro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/guardar_encargo_centro.php"
entrada: ["post.id_ctr:integer", "post.id_enc:integer", "post.id_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\GuardarEncargoCentro"]
tags: ["misas", "guardar", "encargo", "centro"]
estado_revision: "generado"
---

# Guardar Encargo Centro

Inserta o actualiza un `EncargoCtr` (relacion encargo ↔ centro). - Si `id_item` esta vacio se crea un nuevo `EncargoCtr` con uuid v4. - Si `id_item` es un uuid valido se carga el existente y se modifica. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/guardar_encargo_centro`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/guardar_encargo_centro.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ctr` | `integer` | controller | No | controller |
| `id_enc` | `integer` | controller | No | controller |
| `id_item` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\GuardarEncargoCentro`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_centros.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.