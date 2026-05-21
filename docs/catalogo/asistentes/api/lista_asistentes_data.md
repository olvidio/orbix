---
id: "asistentes.lista_asistentes_data"
tipo: "endpoint"
modulo: "asistentes"
url: "/src/asistentes/lista_asistentes_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asistentes/infrastructure/ui/http/controllers/lista_asistentes_data.php"
entrada: ["post.id_pau:integer", "post.queSel:string", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asistentes_ListaAsistentesDataData"
respuesta_data: ["nom_activ:string, queSel: string, aAsistentes: array<string|int, array{nombre: string, a_datos_cl: array<string, string>}>"]
requiere_hashb: false
frontend_referencias: ["frontend/asistentes/controller/lista_asistentes.php"]
casos_uso: ["src\\asistentes\\application\\ListaAsistentesData"]
tags: ["asistentes", "lista", "data"]
estado_revision: "generado"
---

# Lista Asistentes Data

Listado de asistentes a una actividad (`lista_asistentes.php`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asistentes/lista_asistentes_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asistentes/infrastructure/ui/http/controllers/lista_asistentes_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_pau` | `integer` | application | No | application |
| `queSel` | `string` | application | No | application |
| `sel` | `array` | application | No | application |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `asistentes_ListaAsistentesDataData`):
  - `nom_activ` (`string, queSel: string, aAsistentes: array<string|int, array{nombre: string, a_datos_cl: array<string, string>}>`)

## Efectos colaterales

- Listado de asistentes a una actividad (`lista_asistentes.php`).

## Casos De Uso

- `src\asistentes\application\ListaAsistentesData`

## Frontend Relacionado

- `frontend/asistentes/controller/lista_asistentes.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.