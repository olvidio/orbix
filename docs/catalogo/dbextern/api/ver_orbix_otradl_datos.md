---
id: "dbextern.ver_orbix_otradl_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_orbix_otradl_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_orbix_otradl_datos.php"
entrada: ["post.ids_traslados_A:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/dbextern/controller/ver_orbix_otradl.php"]
casos_uso: ["src\\dbextern\\application\\VerOrbixOtraDlData"]
tags: ["dbextern", "ver", "orbix", "otradl", "datos"]
estado_revision: "revisado"
---

# Ver Orbix Otradl Datos

Detalle del punto 7: personas Aquinate activas cuya correspondencia BDU está en otra DL.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe JSON de IDs listas (`ids_traslados_A`), resuelve `id_match` y datos BDU, formatea la DL destino
y devuelve filas para traslado con `sincro_trasladar_a`.

## Endpoint

- URL: `/src/dbextern/ver_orbix_otradl_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_orbix_otradl_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo_persona` | `string` | controller | Sí | |
| `ids_traslados_A` | `string` | controller | Sí | JSON urlencoded de `id_nom_listas` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en front).
- `personas`: filas con `id_nom_orbix`, `id_nom_listas`, `ape_nom`, `dl` (DL destino formateada).

## Permisos

- Sin control propio.

## Casos De Uso

- `src\dbextern\application\VerOrbixOtraDlData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_orbix_otradl.php`
