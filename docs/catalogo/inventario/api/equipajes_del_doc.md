---
id: "inventario.equipajes_del_doc"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_del_doc"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_del_doc.php"
entrada: ["post.id_item_egm:integer", "post.sel:array"]
entrada_obligatoria: ["id_item_egm"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha eliminado"]
frontend_referencias: ["frontend/inventario/controller/equipajes_form_del.php"]
casos_uso: []
tags: ["inventario", "equipajes", "del", "doc"]
estado_revision: "revisado"
---

# Quitar documento de maleta

Elimina la asociación Whereis entre documento(s) seleccionados y el item EGM.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Elimina la asociación Whereis entre documento(s) seleccionados y el item EGM.

## Endpoint

- URL: `/src/inventario/equipajes_del_doc`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_del_doc.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_item_egm` | `integer` | POST | Si | |
| `sel` | `array` | POST | No | |


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

- `frontend/inventario/controller/equipajes_form_del.php`
