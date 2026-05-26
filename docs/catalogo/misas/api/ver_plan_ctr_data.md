---
id: "misas.ver_plan_ctr_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_plan_ctr_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/ver_plan_ctr_data.php"
entrada: ["post.id_ubi:integer", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string"]
entrada_obligatoria: ["id_ubi"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["columns:array", "rows:array", "legend:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_plan_ctr.php", "frontend/misas/controller/imprimir_plan_ctr.php"]
casos_uso: ["src\\misas\\application\\VerPlanCtrData"]
tags: ["misas", "ver", "plan", "ctr", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Ver Plan Ctr Data

Cuadrícula encargo × días del plan de un centro (`id_ubi`).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Previo: [`buscar_plan_ctr_data.md`](buscar_plan_ctr_data.md)

## Endpoint

- URL: `/src/misas/ver_plan_ctr_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_plan_ctr_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_ubi` | int | **Sí** | Centro de `centros_opciones` |
| `periodo` | string | Recomendado | Alias como en cuadrícula zona |
| `empiezamin` / `empiezamax` | string | Condicional | Si `periodo=otro` |

Visibilidad de celdas según rol y estado del plan (centro solo ve comunicado a centros).

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `columns` | array | `{ letra, num_dia, num_mes, id_dia }` |
| `rows` | array | `{ desc_enc, cells[] }` — una celda por columna |
| `legend` | array | `{ iniciales, nombre }` |

Celdas ocultas pueden mostrarse como ` -- ` en web; la app muestra el texto tal cual.

## Ejemplo

```http
POST /orbix/src/misas/ver_plan_ctr_data HTTP/1.1
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

id_ubi=5&periodo=este_mes&empiezamin=&empiezamax=
```

## Cliente de referencia

- `orbix-android`: `fetchVerPlanCtr()` — tabla `PlanCtrGridTable`.
