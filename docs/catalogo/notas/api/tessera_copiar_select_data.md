---
id: "notas.tessera_copiar_select_data"
tipo: "endpoint"
modulo: "notas"
url: "/src/notas/tessera_copiar_select_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/notas/infrastructure/ui/http/controllers/tessera_copiar_select_data.php"
entrada: ["post.id_nom:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/tessera_copiar_select.php"]
casos_uso: ["src\\notas\\application\\TesseraCopiarSelectData"]
tags: ["notas", "tessera", "copiar", "select", "data"]
estado_revision: "generado"
---

# Tessera Copiar Select Data

Prepara los datos para elegir a que persona (con el mismo primer apellido) se copiara la tessera de otra persona. Devuelve `['nom' => string, 'posibles_personas' => [id_nom => nombre]]`. Lanza `RuntimeException` si no encuentra la persona origen ni como numerario ni como agregado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/notas/tessera_copiar_select_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/notas/infrastructure/ui/http/controllers/tessera_copiar_select_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\notas\application\TesseraCopiarSelectData`

## Frontend Relacionado

- `frontend/notas/controller/tessera_copiar_select.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.