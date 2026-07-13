---
id: "casas.grupo_eliminar"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/grupo_eliminar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/grupo_eliminar.php"
entrada: ["post.id_item:integer"]
entrada_obligatoria: ["id_item"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["debe indicar el grupo a eliminar", "no se encuentra el grupo", "Hay un error, no se ha eliminado."]
frontend_referencias: ["frontend/casas/controller/grupo.php", "frontend/casas/view/grupo.phtml"]
casos_uso: ["src\\casas\\application\\GrupoCasaEliminar"]
tags: ["casas", "grupo", "eliminar"]
estado_revision: "revisado"
---

# Grupo Eliminar

Elimina un `GrupoCasa` por `id_item`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `eliminar` de `apps/casas/controller/grupo_ajax.php`. Localiza el grupo y lo
elimina del repositorio.

## Endpoint

- URL: `/src/casas/grupo_eliminar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/grupo_eliminar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item` | `integer` | controller+application | Sí | ID del grupo |

## Salida

- Helper: `ContestarJson::enviar($error, 'ok')`.
- Éxito: `success: true`, `data: "ok"`.

## Errores conocidos

- `debe indicar el grupo a eliminar`
- `no se encuentra el grupo`
- `Hay un error, no se ha eliminado.` (+ texto de repositorio)

## Permisos

- Sin control propio; autorización en frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- `src\casas\application\GrupoCasaEliminar`

## Frontend Relacionado

- `frontend/casas/controller/grupo.php`: `fnjs_eliminar` desde el listado.
