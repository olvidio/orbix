---
id: "misas.buscar_plan_sacd_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/buscar_plan_sacd_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
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
estado_revision: "generado"
---

# Buscar Plan Sacd Data

Lista de sacerdotes disponibles en el buscador del plan SACD (según rol y zona).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/buscar_plan_sacd_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/buscar_plan_sacd_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_BuscarPlanSacdDataData`):
  - `sacd_opciones` (`array`)

## Casos De Uso

- `src\misas\application\BuscarPlanSacdData`

## Frontend Relacionado

- `frontend/misas/controller/buscar_plan_sacd.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.