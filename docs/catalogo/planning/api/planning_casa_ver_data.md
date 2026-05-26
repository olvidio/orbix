---
id: "planning.planning_casa_ver_data"
tipo: "endpoint"
modulo: "planning"
url: "/src/planning/planning_casa_ver_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/planning/infrastructure/ui/http/controllers/planning_casa_ver_data.php"
entrada: ["post.cdc_sel:integer", "post.f_ini_iso:string", "post.f_fin_iso:string", "post.sSeleccionados:string", "post.sin_activ:integer", "post.periodo:string", "post.year:integer", "post.propuesta_calendario:string", "post.modelo:integer"]
entrada_obligatoria: ["f_ini_iso", "f_fin_iso"]
respuesta: "standard_envelope_string_data"
respuesta_data: ["a_actividades:object", "casa_periodos_por_ubi:object"]
requiere_hashb: false
frontend_referencias: ["frontend/planning/controller/planning_casa_ver.php"]
casos_uso: ["src\\planning\\application\\PlanningCasaVerData"]
tags: ["planning", "casa", "ver", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Planning Casa Ver Data

Actividades por casa en un periodo. Invocado desde `planning_casa_ver.php` (tras `planning_casa_que` → `planning_casa_select`).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md)

## Endpoint

- URL: `/src/planning/planning_casa_ver_data`
- Métodos: `POST` (recomendado)
- Controller: `src/planning/infrastructure/ui/http/controllers/planning_casa_ver_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `f_ini_iso`, `f_fin_iso` | string | **Sí** | Rango Y-m-d (la web los calcula con `Periodo`) |
| `cdc_sel` | int | Recomendado | Grupo de casas: 1–6, 9 (una casa), 11–12 (actividades sv/sf) |
| `sSeleccionados` | string | Condicional | IDs separados por coma si `cdc_sel=9` |
| `id_cdc[]` | int[] | Condicional | Alternativa web a `sSeleccionados` |
| `sin_activ` | int | No | `1` incluye casas sin actividades |
| `periodo`, `year`, `empiezamin`, `empiezamax` | mixed | No | Eco del formulario |
| `propuesta_calendario` | string | No | `1` en menú **Nuevo planing** |
| `modelo` | int | No | `1` tabla, `2` impresión |

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `a_actividades` | object | Mapa `nombre_casa → { u#id#nombre → [actividades] }` |
| `casa_periodos_por_ubi` | object | Periodos casa para colorear calendario web |

Cada actividad incluye `nom_curt`, `nom_llarg`, `f_ini`, `h_ini`, `f_fi`, `h_fi`, `css`, etc.

## Ejemplo

```http
POST /orbix/src/planning/planning_casa_ver_data HTTP/1.1
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

cdc_sel=4&sin_activ=0&year=2026&periodo=trimestre_2&f_ini_iso=2026-04-01&f_fin_iso=2026-06-30&propuesta_calendario=1&modelo=1
```

## Cliente de referencia

- `orbix-android`: `fetchPlanningCasaVer()` — `NuevoPlanScreen`.
