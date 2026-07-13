---
id: "dbextern.ver_orbix_datos"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/ver_orbix_datos"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/dbextern/infrastructure/ui/http/controllers/ver_orbix_datos.php"
entrada: ["post.dl:string", "post.id_nom_orbix:integer", "post.region:string", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["No existe la clase de la persona"]
frontend_referencias: ["frontend/dbextern/controller/ver_orbix.php"]
casos_uso: ["src\\dbextern\\application\\VerOrbixData"]
tags: ["dbextern", "ver", "orbix", "datos"]
estado_revision: "revisado"
---

# Ver Orbix Datos

Lista personas Aquinate activas sin correspondencia en BDU (punto 9) o busca candidatos BDU para unir.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Dos modos según `id_nom_orbix`:

- **`id_nom_orbix` = 0**: personas Orbix situación `A` sin `id_match`; devuelve `lista`.
- **`id_nom_orbix` > 0**: candidatos BDU (`posiblesBDU`) para unir con esa persona.

## Endpoint

- URL: `/src/dbextern/ver_orbix_datos`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/ver_orbix_datos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `region` | `string` | controller | Sí | |
| `dl` | `string` | controller | No | Usado en modo matches |
| `tipo_persona` | `string` | controller | Sí | `n`/`a`/`s`/`sssc` |
| `id_nom_orbix` | `integer` | controller | No | Si > 0, modo candidatos BDU |

## Salida

- Helper: `ContestarJson::enviar` (doble `JSON.parse` en front).
- Modo lista: `lista` (filas con `id_nom_orbix`, `ape_nom`, nombre, apellidos, `f_nacimiento`).
- Modo matches: array de candidatos BDU (el front usa `DbexternPayload::listaBduFromMatches`).
- Error: `error` con mensaje si clase de persona inválida.

## Errores conocidos

- `No existe la clase de la persona`

## Permisos

- Sin control propio en el caso de uso.

## Casos De Uso

- `src\dbextern\application\VerOrbixData`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_orbix.php` (sesión `$_SESSION['DBOrbix']`)
