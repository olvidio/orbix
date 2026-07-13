---
id: "misas.buscar_plan_sacd_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/buscar_plan_sacd_data"
metodos: ["GET", "POST"]
operacion: "form_data"
controller: "src/misas/infrastructure/ui/http/controllers/buscar_plan_sacd_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_BuscarPlanSacdDataData"
respuesta_data: ["sacd_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/buscar_plan_sacd.php"]
casos_uso: ["src\\misas\\application\\BuscarPlanSacdData"]
tags: ["misas", "buscar", "plan", "sacd", "data"]
estado_revision: "revisado"
errores: []
---

# Buscar plan sacd Data

Devuelve el desplegable de sacerdotes para el buscador del plan SACD, filtrado por rol y zona del usuario.

Linaje: Slice 7 — migrado desde apps/misas/controller/buscar_plan_sacd.php.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Objetivo funcional

Devuelve el desplegable de sacerdotes para el buscador del plan SACD, filtrado por rol y zona del usuario.

## Endpoint

- URL: `/src/misas/buscar_plan_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `form_data`
- Controller: `src/misas/infrastructure/ui/http/controllers/buscar_plan_sacd_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| _(ninguno)_ | | | | |

## Salida

- Helper: `ContestarJson::enviar`.
- Forma: `standard_envelope_string_data`.
- Claves en `data` (doble `JSON.parse`):
  - `sacd_opciones`: array<string, string>
  - `sacd_selected`: string

## Errores conocidos

- _(ninguno documentado en casos de uso)_

## Permisos

Jefe de zona: sacds de sus zonas p-sacd no jefe: solo su propio plan Oficial_dl o is_jefeCalendario(): todos los sacds activos

## Casos De Uso

- `src\misas\application\BuscarPlanSacdData`

## Frontend Relacionado

- Ver `frontend_referencias` en front matter (`["frontend/misas/controller/buscar_plan_sacd.php"]`).
