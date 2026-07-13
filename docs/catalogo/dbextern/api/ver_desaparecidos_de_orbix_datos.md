---
id: "dbextern.ver_desaparecidos_de_orbix_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_desaparecidos_de_orbix_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_orbix_datos.php"
entrada: ["post.ids_desaparecidos_de_orbix:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/dbextern/controller/ver_desaparecidos_de_orbix.php"]
casos_uso: ["src\\dbextern\\application\\VerDesaparecidosDeOrbixData"]
tags: ["dbextern", "ver", "desaparecidos", "orbix", "datos"]
estado_revision: "revisado"
---

# Ver Desaparecidos De Orbix Datos

Detalle del punto 3: personas en BDU con `id_match` pero sin ficha activa en Aquinate de esta DL.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe JSON de IDs listas y devuelve nombre y DL BDU para permitir desunir (`sincro_desunir`).

## Endpoint

- URL: `/src/dbextern/ver_desaparecidos_de_orbix_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_orbix_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo_persona` | `string` | controller | Sí | |
| `ids_desaparecidos_de_orbix` | `string` | controller | Sí | JSON urlencoded de `id_nom_listas` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en front).
- `personas`: filas con `id_nom_listas`, `ape_nom`, `dl`.

## Permisos

- Sin control propio.

## Casos De Uso

- `src\dbextern\application\VerDesaparecidosDeOrbixData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`
