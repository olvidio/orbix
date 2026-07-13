---
id: "inventario.equipajes_movimientos"
tipo: "endpoint"
modulo: "inventario"
url: "/src/inventario/equipajes_movimientos"
metodos: ["GET", "POST"]
operacion: "lista_data"
controller: "src/inventario/infrastructure/ui/http/controllers/equipajes_movimientos.php"
entrada: ["post.sel:array"]
entrada_obligatoria: ["sel"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: []
frontend_referencias: ["frontend/inventario/controller/equipajes_movimientos.php"]
casos_uso: []
tags: ["inventario", "equipajes", "movimientos"]
estado_revision: "revisado"
---

# Movimientos de documentos entre equipajes

Compara documentos en maletas de equipajes seleccionados y devuelve entradas/salidas por tipo doc y lugar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Compara documentos en maletas de equipajes seleccionados y devuelve entradas/salidas por tipo doc y lugar.

## Endpoint

- URL: `/src/inventario/equipajes_movimientos`
- Metodos registrados: `GET, POST`
- Operacion: `lista_data`
- Controller: `src/inventario/infrastructure/ui/http/controllers/equipajes_movimientos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `sel` | `array` | POST | Si | |


## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Payload `{aCambios, aLugaresPorEgm, aNomEquipajes}`.

## Errores conocidos

- Sin mensajes `_()` documentados en controller.

## Permisos

- Sin control de permisos propio en el controller; autorización de oficina vía frontend + `$_SESSION['oPerm']`.

## Casos De Uso

- Lógica inline en controller (sin `application/`).

## Frontend Relacionado

- `frontend/inventario/controller/equipajes_movimientos.php`
