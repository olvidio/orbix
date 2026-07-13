---
id: "inventario.equipajes_egm"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_egm"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_egm.php"
entrada: ["post.id_equipaje:integer"]
entrada_obligatoria: ["id_equipaje"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_doc_casa.php", "frontend/inventario/controller/equipajes_imprimir.php"]
casos_uso: []
tags: ["inventario", "equipajes", "egm"]
estado_revision: "revisado"
---

# Estructura EGM de un equipaje

Devuelve grupos/maletas (EGM) del equipaje con sus documentos asociados para edición o impresión.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve grupos/maletas (EGM) del equipaje con sus documentos asociados para edición o impresión.

## Endpoint

- URL: `/src/inventario/equipajes_egm`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_egm.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_egm}` con jerarquía grupo → item → documentos.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_doc_casa.php`
- `frontend/inventario/controller/equipajes_imprimir.php`
