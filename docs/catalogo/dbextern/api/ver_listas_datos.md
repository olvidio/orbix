---
id: "dbextern.ver_listas_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_listas_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_listas_datos.php"
entrada: ["post.dl:string", "post.first_load:boolean", "post.id_nom_bdu:integer", "post.region:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\VerListasData"]
tags: ["dbextern", "ver", "listas", "datos"]
estado_revision: "generado"
---

# Ver Listas Datos

Obtiene la lista de personas BDU sin unir y los posibles matches Orbix.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/ver_listas_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_listas_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `dl` | `string` | controller | No | controller |
| `first_load` | `boolean` | controller | No | controller |
| `id_nom_bdu` | `integer` | controller | No | controller |
| `region` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\dbextern\application\VerListasData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_listas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.