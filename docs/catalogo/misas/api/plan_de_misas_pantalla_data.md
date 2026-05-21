---
id: "misas.plan_de_misas_pantalla_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/plan_de_misas_pantalla_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/plan_de_misas_pantalla_data.php"
entrada: ["post.pantalla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_PlanDeMisasPantallaDataData"
respuesta_data: ["pantalla:string", "zonas_opciones:array", "orden_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_plan_de_misas.php", "frontend/misas/controller/preparar_plan_de_misas.php", "frontend/misas/controller/ver_plan_de_misas.php"]
casos_uso: ["src\\misas\\application\\PlanDeMisasPantallaData"]
tags: ["misas", "plan", "de", "pantalla", "data"]
estado_revision: "generado"
---

# Plan De Misas Pantalla Data

Datos comunes para las pantallas preparar / modificar / ver plan de misas y para modificar plantilla (mismos desplegables de zona / tipo / orden).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/plan_de_misas_pantalla_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/plan_de_misas_pantalla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `pantalla` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_PlanDeMisasPantallaDataData`):
  - `pantalla` (`string`)
  - `zonas_opciones` (`array`)
  - `orden_opciones` (`array`)

## Casos De Uso

- `src\misas\application\PlanDeMisasPantallaData`

## Frontend Relacionado

- `frontend/misas/controller/modificar_plan_de_misas.php`
- `frontend/misas/controller/preparar_plan_de_misas.php`
- `frontend/misas/controller/ver_plan_de_misas.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.