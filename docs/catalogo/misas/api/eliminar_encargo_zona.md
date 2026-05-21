---
id: "misas.eliminar_encargo_zona"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/eliminar_encargo_zona"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/eliminar_encargo_zona.php"
entrada: ["post.id_enc:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_zona.php"]
casos_uso: ["src\\misas\\application\\EliminarEncargoZona"]
tags: ["misas", "eliminar", "encargo", "zona"]
estado_revision: "generado"
---

# Eliminar Encargo Zona

Elimina un `Encargo` por id. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/eliminar_encargo_zona`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/eliminar_encargo_zona.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_enc` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina un `Encargo` por id.

## Casos De Uso

- `src\misas\application\EliminarEncargoZona`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.