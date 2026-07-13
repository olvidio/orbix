---
id: "inventario.equipajes_lista_activ_equipaje"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_lista_activ_equipaje"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_equipaje.php"
entrada: ["post.id_equipaje:integer"]
entrada_obligatoria: ["id_equipaje"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["OJO! No se encuentra la actividad con id: %s"]
frontend_referencias: ["frontend/inventario/controller/equipajes_imprimir.php"]
casos_uso: []
tags: ["inventario", "equipajes", "lista", "activ", "equipaje"]
estado_revision: "revisado"
---

# Actividades de un equipaje

Lista actividades vinculadas al equipaje (`ids_activ` almacenados) para la cabecera de impresión.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Lista actividades vinculadas al equipaje (`ids_activ` almacenados) para la cabecera de impresión.

## Endpoint

- URL: `/src/inventario/equipajes_lista_activ_equipaje`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_equipaje.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_equipaje` | `integer` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{a_actividades}` (nombre, fechas) o `[]`.

## Errores conocidos

  - `OJO! No se encuentra la actividad con id: %s`

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_imprimir.php`
