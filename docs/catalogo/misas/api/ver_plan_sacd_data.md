---
id: "misas.ver_plan_sacd_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_plan_sacd_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/ver_plan_sacd_data.php"
entrada: ["post.id_sacd:string", "post.periodo:string", "post.empiezamin:string", "post.empiezamax:string"]
entrada_obligatoria: ["id_sacd"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["rows:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_plan_sacd.php"]
casos_uso: ["src\\misas\\application\\VerPlanSacdData"]
tags: ["misas", "ver", "plan", "sacd", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Ver Plan Sacd Data

Filas del plan de misas de un sacerdote en un rango de fechas.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Previo: [`buscar_plan_sacd_data.md`](buscar_plan_sacd_data.md)

## Endpoint

- URL: `/src/misas/ver_plan_sacd_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_plan_sacd_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_sacd` | string | **Sí** | Clave de `sacd_opciones` (`id_nom#iniciales`; solo `id_nom` antes de `#` se usa en backend) |
| `periodo` | string | Recomendado | Ver tabla de periodos en [`ver_cuadricula_zona_data.md`](ver_cuadricula_zona_data.md) |
| `empiezamin` | string | Condicional | Si `periodo=otro` |
| `empiezamax` | string | Condicional | Si `periodo=otro` |

Visibilidad por estado del plan: usuarios que no son jefe de zona no ven días en estado *propuesta*.

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `rows` | array | `{ dia, encargo, observ }` por fila |

## Ejemplo

```http
POST /orbix/src/misas/ver_plan_sacd_data HTTP/1.1
Accept: application/json
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

id_sacd=42%23JP&periodo=esta_semana&empiezamin=&empiezamax=
```

```json
{
  "success": true,
  "data": "{\"rows\":[{\"dia\":\"2026-05-26 10:00\",\"encargo\":\"Misa dominical\",\"observ\":\"\"}]}"
}
```

## Cliente de referencia

- `orbix-android`: `fetchVerPlanSacd()` — lista en `SacdPlanList`.
