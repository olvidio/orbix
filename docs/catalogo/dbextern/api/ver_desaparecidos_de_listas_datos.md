---
id: "dbextern.ver_desaparecidos_de_listas_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_desaparecidos_de_listas_datos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_listas_datos.php"
entrada: ["post.ids_desaparecidos_de_listas:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/dbextern/controller/ver_desaparecidos_de_listas.php"]
casos_uso: ["src\\dbextern\\application\\VerDesaparecidosDeListasData"]
tags: ["dbextern", "ver", "desaparecidos", "listas", "datos"]
estado_revision: "revisado"
---

# Ver Desaparecidos De Listas Datos

Detalle del punto 8: personas Aquinate con `id_match` cuya ficha BDU ya no existe o está vacía.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Recibe JSON de IDs Orbix y devuelve nombre y DL para dar de baja la ficha (`sincro_baja`).

## Endpoint

- URL: `/src/dbextern/ver_desaparecidos_de_listas_datos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_desaparecidos_de_listas_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `tipo_persona` | `string` | controller | Sí | |
| `ids_desaparecidos_de_listas` | `string` | controller | Sí | JSON urlencoded de `id_nom_orbix` |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en front).
- `personas`: filas con `id_nom_orbix`, `ape_nom`, `dl`.

## Permisos

- Sin control propio.

## Casos De Uso

- `src\dbextern\application\VerDesaparecidosDeListasData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_desaparecidos_de_listas.php`
