---
id: "inventario.equipajes_eliminar_grupo"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_eliminar_grupo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_eliminar_grupo.php"
entrada: ["post.id_grupo:integer", "post.id_equipaje:integer"]
entrada_obligatoria: ["id_grupo", "id_equipaje"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/inventario/controller/equipajes_ver.php"]
casos_uso: []
tags: ["inventario", "equipajes", "eliminar", "grupo"]
estado_revision: "revisado"
---

# Eliminar grupo/maleta de equipaje

Elimina un grupo EGM (`id_grupo`) de un equipaje.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina un grupo EGM (`id_grupo`) de un equipaje.

## Endpoint

- URL: `/src/inventario/equipajes_eliminar_grupo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_eliminar_grupo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_grupo` | `integer` | POST | Si | |
| `id_equipaje` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `data: "ok"`.

## Errores conocidos

  - `hay un error, no se ha eliminado`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_ver.php`
