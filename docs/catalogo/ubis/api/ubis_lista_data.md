---
id: "ubis.ubis_lista_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_lista_data"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_lista_data.php"
entrada: ["post.nombre_ubi:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "ubis_UbisListaDataData"
respuesta_data: ["a_cabeceras:list<string>, a_valores: list<array<string|int>>"]
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/ubis_lista.php"]
casos_uso: ["src\\ubis\\application\\UbisListaData"]
tags: ["ubis", "lista", "data"]
estado_revision: "generado"
---

# Ubis Lista Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/ubis_lista_data`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_lista_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `nombre_ubi` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload en `data` (schema `ubis_UbisListaDataData`):
  - `a_cabeceras` (`list<string>, a_valores: list<array<string|int>>`)

## Permisos

- Permiso oficina `vcsd`
- Permiso oficina `des`

## Casos De Uso

- `src\ubis\application\UbisListaData`

## Frontend Relacionado

- `frontend/ubis/controller/ubis_lista.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.