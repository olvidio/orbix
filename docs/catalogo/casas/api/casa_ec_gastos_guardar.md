---
id: "casas.casa_ec_gastos_guardar"
tipo: "endpoint"
modulo: "casas"
url: "/src/casas/casa_ec_gastos_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_guardar.php"
entrada: ["post.id_ubi:integer", "post.year:integer", "post.g1:string", "post.g2:string", "post.g3:string", "post.g4:string", "post.g5:string", "post.g6:string", "post.g7:string", "post.g8:string", "post.g9:string", "post.g10:string", "post.g11:string", "post.g12:string", "post.ap_sv1:string", "post.ap_sv2:string", "post.ap_sv3:string", "post.ap_sv4:string", "post.ap_sv5:string", "post.ap_sv6:string", "post.ap_sv7:string", "post.ap_sv8:string", "post.ap_sv9:string", "post.ap_sv10:string", "post.ap_sv11:string", "post.ap_sv12:string", "post.ap_sf1:string", "post.ap_sf2:string", "post.ap_sf3:string", "post.ap_sf4:string", "post.ap_sf5:string", "post.ap_sf6:string", "post.ap_sf7:string", "post.ap_sf8:string", "post.ap_sf9:string", "post.ap_sf10:string", "post.ap_sf11:string", "post.ap_sf12:string"]
entrada_obligatoria: ["id_ubi", "year"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["Faltan id_ubi o year.", "Hay un error, no se ha guardado."]
frontend_referencias: ["frontend/casas/controller/casa_ec_gastos_lista.php"]
casos_uso: ["src\\casas\\application\\CasaEcGastosGuardar"]
tags: ["casas", "casa", "ec", "gastos", "guardar"]
estado_revision: "revisado"
---

# Casa Ec Gastos Guardar

Guarda gastos y aportaciones mensuales de una casa para un año completo.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Sucesor de la rama `que=guardarGasto` de `apps/casas/controller/casa_ec_ajax.php`. Por cada mes 1–12
inserta tres registros `UbiGasto` (gasto, aportación sv, aportación sf) con fecha día 5 del mes.
Acepta decimales con coma o punto en `g$m`, `ap_sv$m`, `ap_sf$m`.

## Endpoint

- URL: `/src/casas/casa_ec_gastos_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/casas/infrastructure/ui/http/controllers/casa_ec_gastos_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `integer` | controller+application | Sí | Casa |
| `year` | `integer` | controller+application | Sí | Año |
| `g1`…`g12` | `string` | controller+application | No | Gasto mensual |
| `ap_sv1`…`ap_sv12` | `string` | controller+application | No | Aportación sv |
| `ap_sf1`…`ap_sf12` | `string` | controller+application | No | Aportación sf |

## Salida

- Helper: `ContestarJson::enviar($mensaje, $data)`.
- Éxito: `success: true`, `data: ""`.
- Error: mensaje en `data`.

## Errores conocidos

- `Faltan id_ubi o year.`
- `Hay un error, no se ha guardado.`

## Permisos

- Sin control propio; autorización en frontend.

## Casos De Uso

- `src\casas\application\CasaEcGastosGuardar`

## Frontend Relacionado

- `frontend/casas/controller/casa_ec_gastos_lista.php`: submit del formulario anual.
