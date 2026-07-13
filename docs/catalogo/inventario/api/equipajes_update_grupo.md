---
id: "inventario.equipajes_update_grupo"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_update_grupo"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_update_grupo.php"
entrada: ["post.id_grupo:integer", "post.id_equipaje:integer", "post.id_lugar:integer", "post.sel:array"]
entrada_obligatoria: ["id_equipaje", "id_grupo", "id_lugar"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/inventario/controller/equipajes_posibles_maletas.php"]
casos_uso: []
tags: ["inventario", "equipajes", "update", "grupo"]
estado_revision: "revisado"
---

# Crear grupo y asignar documentos

Crea item EGM en grupo/lugar si no existe y asocia documentos (`sel`) vía Whereis. Devuelve `id_item_egm` creado.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Crea item EGM en grupo/lugar si no existe y asocia documentos (`sel`) vía Whereis. Devuelve `id_item_egm` creado.

## Endpoint

- URL: `/src/inventario/equipajes_update_grupo`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_update_grupo.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_grupo` | `integer` | POST | Si | |
| `id_equipaje` | `integer` | POST | Si | |
| `id_lugar` | `integer` | POST | Si | |
| `sel` | `array` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Éxito: `data: {id_item_egm}`.

## Errores conocidos

  - `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_posibles_maletas.php`
