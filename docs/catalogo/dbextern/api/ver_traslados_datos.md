---
id: "dbextern.ver_traslados_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_traslados_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_traslados_datos.php"
entrada: ["post.ids_traslados:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["No existe la clase de la persona"]
frontend_referencias: ["frontend/dbextern/controller/ver_traslados.php"]
casos_uso: ["src\\dbextern\\application\\VerTrasladosData"]
tags: ["dbextern", "ver", "traslados", "datos"]
estado_revision: "revisado"
---

# Ver Traslados Datos

Detalle del punto 2 del dashboard: personas unidas a BDU pero situadas en otra DL en Orbix.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe JSON de IDs Orbix (`ids_traslados`), consulta cada persona en su DL remota y devuelve nombre,
DL Orbix y DL actual para permitir el traslado con `sincro_trasladar`.

## Endpoint

- URL: `/src/dbextern/ver_traslados_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_traslados_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo_persona` | `string` | controller | Sí | `n`/`a`/`s`/`sssc` |
| `ids_traslados` | `string` | controller | Sí | JSON urlencoded de array de `id_nom_orbix` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en front).
- `personas`: filas con `id_nom_orbix`, `ape_nom`, `dl`, `dl_actual`.
- Error: clave `error` si `tipo_persona` no resuelve clase.

## Errores conocidos

- `No existe la clase de la persona`

## Permisos

- Sin control propio.

## Casos De Uso

- `src\dbextern\application\VerTrasladosData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_traslados.php`
