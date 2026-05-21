---
id: "dbextern.sincro_crear_todos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_crear_todos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_crear_todos.php"
entrada: ["post.dl:string", "post.region:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "dbextern_CrearPersonaDesdeListasUseCaseData"
respuesta_data: ["count:int, errors: string[]"]
requiere_hashb: false
errores: ["no se encontró la persona en la BDU", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\CrearPersonaDesdeListasUseCase", "src\\dbextern\\application\\CrearTodosDesdeListasUseCase"]
tags: ["dbextern", "sincro", "crear", "todos"]
estado_revision: "generado"
---

# Sincro Crear Todos

Crea una persona en Orbix desde la BDU y la vincula.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_crear_todos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_crear_todos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No | controller |
| `region` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `dbextern_CrearPersonaDesdeListasUseCaseData`):
  - `count` (`int, errors: string[]`)

## Errores conocidos

- `no se encontró la persona en la BDU`
- `hay un error, no se ha guardado`

## Casos De Uso

- `src\dbextern\application\CrearPersonaDesdeListasUseCase`
- `src\dbextern\application\CrearTodosDesdeListasUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_listas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.