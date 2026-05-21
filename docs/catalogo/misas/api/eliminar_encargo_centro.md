---
id: "misas.eliminar_encargo_centro"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/eliminar_encargo_centro"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/eliminar_encargo_centro.php"
entrada: ["post.id_item:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Falta el identificador del encargo-centro a eliminar"]
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\EliminarEncargoCentro"]
tags: ["misas", "eliminar", "encargo", "centro"]
estado_revision: "generado"
---

# Eliminar Encargo Centro

Elimina un `EncargoCtr` por su uuid. Devuelve texto vacio si todo fue bien, o el mensaje de error del repositorio en caso contrario.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/eliminar_encargo_centro`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/eliminar_encargo_centro.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Elimina un `EncargoCtr` por su uuid.

## Errores conocidos

- `Falta el identificador del encargo-centro a eliminar`

## Casos De Uso

- `src\misas\application\EliminarEncargoCentro`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_centros.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.