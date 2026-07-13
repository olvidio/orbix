---
id: "inventario.equipajes_add_doc"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_add_doc"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_add_doc.php"
entrada: ["post.id_item_egm:integer", "post.sel:array"]
entrada_obligatoria: ["id_item_egm"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["hay un error, no se ha guardado"]
frontend_referencias: ["frontend/inventario/controller/equipajes_form_add.php"]
casos_uso: []
tags: ["inventario", "equipajes", "add", "doc"]
estado_revision: "revisado"
---

# Añadir documento a maleta (EGM)

Asocia un documento libre a un item EGM (`id_item_egm`) creando registro Whereis.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Asocia un documento libre a un item EGM (`id_item_egm`) creando registro Whereis.

## Endpoint

- URL: `/src/inventario/equipajes_add_doc`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_add_doc.php`

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

  - `hay un error, no se ha guardado`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_form_add.php`
