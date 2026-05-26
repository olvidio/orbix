---
id: "misas.buscar_plan_ctr_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/buscar_plan_ctr_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/buscar_plan_ctr_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["view:string", "zonas_opciones:object", "zonas_selected:integer", "centros_opciones:object", "centros_selected:string", "id_ubi_centro:string"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/buscar_plan_ctr.php"]
casos_uso: ["src\\misas\\application\\BuscarPlanCtrData"]
tags: ["misas", "buscar", "plan", "ctr", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Buscar Plan Ctr Data

Formulario **Ver el plan de misas y encargos de un centro**: zonas, centros y modo de vista según rol.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Siguiente: [`ver_plan_ctr_data.md`](ver_plan_ctr_data.md)

## Endpoint

- URL: `/src/misas/buscar_plan_ctr_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/buscar_plan_ctr_data.php`

## Entrada

| Campo | Tipo | Obligatorio | Notas |
|-------|------|-------------|-------|
| `id_zona` | int | No | Default `0`; al cambiar zona en web se reenvía |

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `view` | string | `sacd`, `centro` o `none` |
| `zonas_opciones` | object | Mapa zonas |
| `zonas_selected` | int | Zona seleccionada |
| `centros_opciones` | object | Mapa `id_ubi → nombre` |
| `centros_selected` | string | Centro seleccionado |
| `id_ubi_centro` | string | Solo rol centro: id del propio centro |

### Errores

Si `view === 'none'`: `success: false`, `mensaje` *No tiene permiso para ver esta página* (sin payload útil).

Rol **Centro sv/sf**: `view=centro`, un solo centro. Rol **p-sacd**: zonas del jefe de calendario.

## Ejemplo

```http
POST /orbix/src/misas/buscar_plan_ctr_data HTTP/1.1
Content-Type: application/x-www-form-urlencoded
Cookie: PHPSESSID=...

id_zona=12
```

## Cliente de referencia

- `orbix-android`: `fetchBuscarPlanCtrPage()` — si `view=none`, muestra error de permiso.
