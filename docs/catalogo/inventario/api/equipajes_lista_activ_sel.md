---
id: "inventario.equipajes_lista_activ_sel"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_lista_activ_sel"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_sel.php"
entrada: ["post.id_cdc:integer", "post.sel:array"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_form_nuevo.php"]
casos_uso: []
tags: ["inventario", "equipajes", "lista", "activ", "sel"]
estado_revision: "revisado"
---

# Datos iniciales nuevo equipaje

Devuelve contexto (nombre ubi, fechas, ids actividades) tras elegir actividades al crear equipaje.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve contexto (nombre ubi, fechas, ids actividades) tras elegir actividades al crear equipaje.

## Endpoint

- URL: `/src/inventario/equipajes_lista_activ_sel`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_lista_activ_sel.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_cdc` | `integer` | POST | No | |
| `sel` | `array` | POST | No | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{nombre_ubi, ini, fin, ids_activ}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_form_nuevo.php`
