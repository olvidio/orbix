---
id: "asignaturas.asignaturas_con_separador_data"
tipo: "endpoint"
modulo: "asignaturas"
url: "/src/asignaturas/asignaturas_con_separador_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/asignaturas/infrastructure/ui/http/controllers/asignaturas_con_separador_data.php"
entrada: ["post.op_genericas:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "asignaturas_AsignaturasConSeparadorOpcionesDataData"
respuesta_data: ["a_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/notas/controller/asig_faltan_que.php"]
casos_uso: ["src\\asignaturas\\application\\AsignaturasConSeparadorOpcionesData"]
tags: ["asignaturas", "con", "separador", "data"]
estado_revision: "generado"
---

# Asignaturas Con Separador Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/asignaturas/asignaturas_con_separador_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/asignaturas/infrastructure/ui/http/controllers/asignaturas_con_separador_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `op_genericas` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `asignaturas_AsignaturasConSeparadorOpcionesDataData`):
  - `a_opciones` (`array`)

## Casos De Uso

- `src\asignaturas\application\AsignaturasConSeparadorOpcionesData`

## Frontend Relacionado

- `frontend/notas/controller/asig_faltan_que.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.