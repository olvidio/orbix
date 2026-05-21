---
id: "dbextern.ver_desaparecidos_de_orbix_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_desaparecidos_de_orbix_datos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_orbix_datos.php"
entrada: ["post.ids_desaparecidos_de_orbix:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/dbextern/controller/ver_desaparecidos_de_orbix.php"]
casos_uso: ["src\\dbextern\\application\\VerDesaparecidosDeOrbixData"]
tags: ["dbextern", "ver", "desaparecidos", "de", "orbix", "datos"]
estado_revision: "generado"
---

# Ver Desaparecidos De Orbix Datos

Obtiene datos de personas BDU desaparecidas de Orbix.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/dbextern/ver_desaparecidos_de_orbix_datos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_orbix_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `ids_desaparecidos_de_orbix` | `string` | controller | No | controller |
| `tipo_persona` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\dbextern\application\VerDesaparecidosDeOrbixData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.