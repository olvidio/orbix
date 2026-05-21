---
id: "dbextern.sincro_index_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_index_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_index_datos.php"
entrada: ["post.tipo:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/sincro_index.php"]
casos_uso: ["src\\dbextern\\application\\SincroIndexData"]
tags: ["dbextern", "sincro", "index", "datos"]
estado_revision: "generado"
---

# Sincro Index Datos

Calcula los 10 contadores del dashboard de sincronización.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/sincro_index_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_index_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Efectos colaterales

- Calcula los 10 contadores del dashboard de sincronización.

## Permisos

- Permiso oficina `sm`
- Permiso oficina `agd`
- Permiso oficina `sg`
- Permiso oficina `des`

## Casos De Uso

- `src\dbextern\application\SincroIndexData`

## Frontend Relacionado

- `frontend/dbextern/controller/sincro_index.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.