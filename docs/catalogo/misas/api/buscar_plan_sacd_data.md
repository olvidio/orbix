---
id: "misas.buscar_plan_sacd_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/buscar_plan_sacd_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/buscar_plan_sacd_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["sacd_opciones:object", "sacd_selected:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/buscar_plan_sacd.php"]
casos_uso: ["src\\misas\\application\\BuscarPlanSacdData"]
tags: ["misas", "buscar", "plan", "sacd", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Buscar Plan Sacd Data

Lista de sacerdotes disponibles en el buscador **Ver el plan de un sacerdote**, según rol y zonas del usuario.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Siguiente paso: [`ver_plan_sacd_data.md`](ver_plan_sacd_data.md)

## Endpoint

- URL: `/src/misas/buscar_plan_sacd_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/buscar_plan_sacd_data.php`

## Entrada

Sin parámetros POST.

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `sacd_opciones` | object | Mapa **`id_nom#iniciales` → nombre visible** |
| `sacd_selected` | string | Primera clave del mapa o vacío |

La clave compuesta (`123#AB`) es el valor que debe enviarse como `id_sacd` a [`ver_plan_sacd_data`](ver_plan_sacd_data.md).

## Ejemplo

```http
POST /orbix/src/misas/buscar_plan_sacd_data HTTP/1.1
Accept: application/json
Cookie: PHPSESSID=...
```

```json
{
  "success": true,
  "data": "{\"sacd_opciones\":{\"42#JP\":\"Juan Pérez\"},\"sacd_selected\":\"42#JP\"}"
}
```

## Cliente de referencia

- `orbix-android`: `fetchBuscarPlanSacdPage()` — `VerPlanSacdScreen`.
