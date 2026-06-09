---
id: "ubis.ubis_editar_normalize_dl_data"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/ubis_editar_normalize_dl_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/ubis_editar_normalize_dl_data.php"
entrada: ["post.id_ubi:integer", "post.nombre_ubi:string", "post.obj_pau:string", "post.tipo_ubi:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: []
casos_uso: ["src\\ubis\\application\\UbisEditarNormalizeDlData"]
tags: ["ubis", "editar", "normalize", "dl", "data"]
estado_revision: "generado"
---

# Ubis Editar Normalize Dl Data

Ajusta `obj_pau` a CentroDl/CasaDl cuando la ficha es de la delegación actual.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/ubis_editar_normalize_dl_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/ubis_editar_normalize_dl_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller | No | controller |
| `nombre_ubi` | `string` | controller | No | controller |
| `obj_pau` | `string` | controller | No | controller |
| `tipo_ubi` | `string` | controller | No | controller |

El controller pasa `$_POST` completo al caso de uso; la tabla incluye campos inferidos del application layer.

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\UbisEditarNormalizeDlData`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.