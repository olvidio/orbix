---
id: "dbextern.ver_orbix_otradl_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_orbix_otradl_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_orbix_otradl_datos.php"
entrada: ["post.ids_traslados_A:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/ver_orbix_otradl.php"]
casos_uso: ["src\\dbextern\\application\\VerOrbixOtraDlData"]
tags: ["dbextern", "ver", "orbix", "otradl", "datos"]
estado_revision: "generado"
---

# Ver Orbix Otradl Datos

Obtiene datos de personas BDU que están en otra DL en Orbix.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/ver_orbix_otradl_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_orbix_otradl_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ids_traslados_A` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\dbextern\application\VerOrbixOtraDlData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_orbix_otradl.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.