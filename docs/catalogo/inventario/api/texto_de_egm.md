---
id: "inventario.texto_de_egm"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/texto_de_egm"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/inventario/infrastructure/ui/http/controllers/texto_de_egm.php"
entrada: ["post.id_equipaje:integer", "post.id_grupo:integer", "post.id_item_egm:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_form_texto_listado.php"]
casos_uso: []
tags: ["inventario", "texto", "de", "egm"]
estado_revision: "revisado"
---

# Texto editable de grupo EGM

Devuelve texto almacenado para cabecera/listado de un grupo o item EGM.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve texto almacenado para cabecera/listado de un grupo o item EGM.

## Endpoint

- URL: `/src/inventario/texto_de_egm`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/texto_de_egm.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | POST | No | |
| `id_grupo` | `integer` | POST | No | |
| `id_item_egm` | `integer` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{texto}` o `[]`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_form_texto_listado.php`
