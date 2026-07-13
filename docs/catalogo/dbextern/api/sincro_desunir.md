---
id: "dbextern.sincro_desunir"
tipo: "endpoint"
modulo: "dbextern"
url: "/src/dbextern/sincro_desunir"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/dbextern/infrastructure/ui/http/controllers/sincro_desunir.php"
entrada: ["post.id_nom_listas:integer", "post.tipo_persona:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
errores: ["no se encontró el registro a desunir", "hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/dbextern/controller/ver_desaparecidos_de_orbix.php"]
casos_uso: ["src\\dbextern\\application\\DesunirPersonaUseCase"]
tags: ["dbextern", "sincro", "desunir"]
estado_revision: "revisado"
---

# Sincro Desunir

Elimina el vínculo `id_match` de una persona BDU (punto 3 del dashboard).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Busca `IdMatchPersona` por `id_listas` y lo elimina del repositorio.

## Endpoint

- URL: `/src/dbextern/sincro_desunir`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/dbextern/infrastructure/ui/http/controllers/sincro_desunir.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_nom_listas` | `integer` | controller | Sí | ID en BDU |
| `tipo_persona` | `string` | controller | Sí | Se propaga como `id_tabla` |

## Salida

- Helper: `ContestarJson::enviar`.
- Éxito: `success: true`, `data: "ok"`.
- Error: `success: false`, mensaje `_()`.

## Errores conocidos

- `no se encontró el registro a desunir`
- `hay un error, no se ha eliminado` (+ `getErrorTxt()`)

## Permisos

- HashFront en `ver_desaparecidos_de_orbix.phtml`.

## Casos De Uso

- `src\dbextern\application\DesunirPersonaUseCase`

## Frontend Relacionado

- `frontend/dbextern/controller/ver_desaparecidos_de_orbix.php`
