---
id: "dbextern.sincro_crear"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_crear"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_crear.php"
entrada: ["post.id:integer", "post.id_nom_listas:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["no se encontró la persona en la BDU", "no se pudo resolver la delegación de listas", "opción no definida para tipo persona %s", "No existe la clase de la persona", "hay un error, no se ha guardado"]
frontend_referencias: ["frontend/dbextern/controller/ver_listas.php"]
casos_uso: ["src\\dbextern\\application\\CrearPersonaDesdeListasUseCase"]
tags: ["dbextern", "sincro", "crear"]
estado_revision: "revisado"
---

# Sincro Crear

Alta de persona en Aquinate a partir de ficha BDU y creación del `id_match` (punto 4).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lee datos de `PersonaBDU`, crea ficha en el repositorio del colectivo (`PersonaN`/`Agd`/`S`/`SSSC`),
situación `A`, y guarda `IdMatchPersona`. Opcionalmente avanza la lista en sesión (`id`).

## Endpoint

- URL: `/src/dbextern/sincro_crear`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_crear.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom_listas` | `integer` | controller | Sí | ID en BDU |
| `tipo_persona` | `string` | controller | Sí | `n`/`a`/`s`/`sssc` |
| `id` | `integer` | controller | No | Índice en `$_SESSION['DBListas']` |

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `success: true`, `data: "ok"`.
- Error: `success: false`, mensaje del caso de uso.

## Errores conocidos

- `no se encontró la persona en la BDU`
- `no se pudo resolver la delegación de listas`
- `opción no definida para tipo persona %s`
- `No existe la clase de la persona`
- `hay un error, no se ha guardado` (+ `getErrorTxt()` en match)

## Permisos

- HashFront en `ver_listas.phtml` (`h_crear`).

## Casos De Uso

- `src\dbextern\application\CrearPersonaDesdeListasUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_listas.php` → `fnjs_crear`
