---
id: "casas.grupo_update"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/grupo_update"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/grupo_update.php"
entrada: ["post.id_item:string", "post.id_ubi_hijo:integer", "post.id_ubi_padre:integer"]
entrada_obligatoria: ["id_ubi_padre", "id_ubi_hijo"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe indicar las dos casas", "No puede ser la misma casa", "no se encuentra el grupo", "Hay un error, no se ha guardado."]
frontend_referencias: ["frontend/casas/controller/grupo.php", "frontend/casas/view/grupo.phtml"]
casos_uso: ["src\\casas\\application\\GrupoCasaUpdate"]
tags: ["casas", "grupo", "update"]
estado_revision: "revisado"
---

# Grupo Update

Crea o actualiza un `GrupoCasa` (relación casa padre ↔ casa hijo).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `update` de `apps/casas/controller/grupo_ajax.php`. Valida que se indiquen dos
casas distintas, crea un nuevo `id_item` en alta o localiza el existente en edición, y persiste la
relación.

## Endpoint

- URL: `/src/casas/grupo_update`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_update.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `string` | controller+application | No | Vacío o `nuevo` = alta |
| `id_ubi_padre` | `integer` | controller+application | Sí | Casa padre |
| `id_ubi_hijo` | `integer` | controller+application | Sí | Casa hijo |

## Salida

- Helper: `ContestarJson::enviar($error, 'ok')`.
- Éxito: `success: true`, `data: "ok"`.
- Error: mensaje en `data` (primer argumento del helper).

## Errores conocidos

- `debe indicar las dos casas`
- `No puede ser la misma casa`
- `no se encuentra el grupo`
- `Hay un error, no se ha guardado.` (+ texto de repositorio en nueva línea)

## Permisos

- Sin control propio en el caso de uso; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\casas\application\GrupoCasaUpdate`

## Frontend Relacionado

- `frontend/casas/controller/grupo.php` / `grupo.phtml`: submit del formulario modal.
