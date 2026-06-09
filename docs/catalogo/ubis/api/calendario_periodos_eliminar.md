---
id: "ubis.calendario_periodos_eliminar"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/calendario_periodos_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/calendario_periodos_eliminar.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["no sé cuál he de borar", "no se encuentra el periodo a borrar", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/ubis/controller/calendario_periodos.php"]
casos_uso: ["src\\ubis\\application\\CalendarioPeriodoEliminar"]
tags: ["ubis", "calendario", "periodos", "eliminar"]
estado_revision: "generado"
---

# Calendario Periodos Eliminar

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/calendario_periodos_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/calendario_periodos_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `no sé cuál he de borar`
- `no se encuentra el periodo a borrar`
- `hay un error, no se ha eliminado`

## Casos De Uso

- `src\ubis\application\CalendarioPeriodoEliminar`

## Frontend Relacionado

- `frontend/ubis/controller/calendario_periodos.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.