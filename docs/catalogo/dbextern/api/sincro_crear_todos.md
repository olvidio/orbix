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
errores: ["no se encontró la persona en la BDU", "no se pudo resolver la delegación de listas", "opción no definida para tipo persona %s", "No existe la clase de la persona", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\CrearTodosDesdeListasUseCase"]
tags: ["dbextern", "sincro", "crear", "todos"]
estado_revision: "revisado"
---

# Sincro Crear Todos

Crea en bloque todas las personas BDU de la DL sin `id_match` (acción «crear todos» en punto 4).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Itera `getPersonasBDU()` y llama `CrearPersonaDesdeListasUseCase` por cada no unida. Devuelve
recuento intentado y lista de errores.

## Endpoint

- URL: `/src/dbextern/sincro_crear_todos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_crear_todos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | Sí | |
| `dl` | `string` | controller | Sí | DL listas |
| `tipo_persona` | `string` | controller | Sí | |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` si el front parsea `data`).
- Éxito parcial/total: `success: true`, `data` con `count`.
- Si hubo errores: `success: false`, `mensaje` = errores unidos por `\n`.

## Errores conocidos

- Los mismos que `sincro_crear` (por cada persona fallida).

## Permisos

- HashFront en `ver_listas.phtml` (`h_crear_todos`).

## Casos De Uso

- `src\dbextern\application\CrearTodosDesdeListasUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_listas.php` → `fnjs_crear_todos`
