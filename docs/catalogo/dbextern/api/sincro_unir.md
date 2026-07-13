---
id: "dbextern.sincro_unir"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_unir"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_unir.php"
entrada: ["post.id:integer", "post.id_nom_listas:integer", "post.id_orbix:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/dbextern/controller/ver_listas.php", "frontend/dbextern/controller/ver_orbix.php"]
casos_uso: ["src\\dbextern\\application\\UnirPersonaUseCase"]
tags: ["dbextern", "sincro", "unir"]
estado_revision: "revisado"
---

# Sincro Unir

Crea el vínculo `id_match` entre una persona BDU (`id_nom_listas`) y una Orbix (`id_orbix`).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Guarda `IdMatchPersona` con `id_tabla = tipo_persona`. Si llega `id` de sesión `DBListas`/`DBOrbix`,
elimina esa fila de la sesión tras éxito.

## Endpoint

- URL: `/src/dbextern/sincro_unir`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_unir.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom_listas` | `integer` | controller | Sí | ID en BDU |
| `id_orbix` | `integer` | controller | Sí | `id_nom` en Aquinate |
| `tipo_persona` | `string` | controller | Sí | `n`/`a`/`s`/`sssc` |
| `id` | `integer` | controller | No | Índice en `$_SESSION['DBListas']` o `DBOrbix` para avanzar lista |

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `success: true`, `data: "ok"`.
- Error: `success: false`, mensaje del repositorio.

## Errores conocidos

- `hay un error, no se ha guardado` (+ texto de `getErrorTxt()`)

## Permisos

- HashFront en pantallas `ver_listas` / `ver_orbix`.

## Casos De Uso

- `src\dbextern\application\UnirPersonaUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_listas.php` → `fnjs_unir`
- `frontend/dbextern/controller/ver_orbix.php` → `fnjs_unir_bdu`
