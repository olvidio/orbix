---
id: "misas.plan_de_misas_pantalla_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/plan_de_misas_pantalla_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/plan_de_misas_pantalla_data.php"
entrada: ["post.pantalla:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_PlanDeMisasPantallaDataData"
respuesta_data: ["pantalla:string", "zonas_opciones:object", "orden_opciones:object", "tipos_plantilla?:object", "plantilla_selected?:integer"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_plan_de_misas.php", "frontend/misas/controller/preparar_plan_de_misas.php", "frontend/misas/controller/ver_plan_de_misas.php"]
casos_uso: ["src\\misas\\application\\PlanDeMisasPantallaData"]
tags: ["misas", "plan", "de", "pantalla", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Plan De Misas Pantalla Data

Datos comunes para las pantallas **preparar / modificar / ver** plan de misas y modificar plantilla: desplegables de zona y orden (y tipos de plantilla en preparar).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Flujo móvil: [`_endpoints_cliente_movil.md`](../_endpoints_cliente_movil.md)

## Endpoint

- URL: `/src/misas/plan_de_misas_pantalla_data`
- Métodos: `POST` (recomendado), `GET` registrado
- Controller: `src/misas/infrastructure/ui/http/controllers/plan_de_misas_pantalla_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `pantalla` | string | No | Default efectivo: `preparar` si valor inválido |

### Valores de `pantalla`

| Valor | Pantalla web |
|-------|----------------|
| `ver` | Ver plan de misas (`ver_plan_de_misas.php`) |
| `modificar` | Modificar plan |
| `preparar` | Preparar nuevo plan |
| `modificar_plantilla` | Modificar plantilla (`modificar_plantilla.php`; la app móvil también usa este valor aquí) |

## Salida

- Helper: `ContestarJson::enviar`
- `data`: **objeto serializado como string JSON** (segundo parse).

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `pantalla` | string | Valor normalizado |
| `zonas_opciones` | object | Mapa `id_zona → etiqueta` (zonas del jefe de calendario del usuario) |
| `orden_opciones` | object | Claves fijas: `orden`, `prioridad`, `desc_enc` → etiquetas localizadas |
| `tipos_plantilla` | object | Solo si `pantalla` es `preparar` o `modificar_plantilla` |
| `plantilla_selected` | int | Preferencia `ultima_plantilla` del usuario (solo preparar/plantilla) |

Si el usuario no puede resolver jefe de calendario, el caso de uso lanza excepción → `success: false`.

## Flujo con cuadrícula

1. Llamar a este endpoint para rellenar filtros.
2. Llamar a [`ver_cuadricula_zona_data`](ver_cuadricula_zona_data.md) con `id_zona`, `periodo`, `orden`, etc.

## Ejemplo

**Request:**

```http
POST /orbix/src/misas/plan_de_misas_pantalla_data HTTP/1.1
Accept: application/json
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

pantalla=ver
```

**Response:**

```json
{
  "success": true,
  "data": "{\"pantalla\":\"ver\",\"zonas_opciones\":{\"12\":\"Zona Norte\",\"15\":\"Zona Sur\"},\"orden_opciones\":{\"orden\":\"orden\",\"prioridad\":\"prioridad\",\"desc_enc\":\"alfabético\"}}"
}
```

## Casos de uso

- `src\misas\application\PlanDeMisasPantallaData`

## Cliente de referencia

- `orbix-android`: `fetchPlanDeMisasPantalla(pantalla=…)` — valores `ver`, `modificar`, `preparar`, `modificar_plantilla` según pantalla nativa (`PlanDeMisasCuadriculaScreen`).
