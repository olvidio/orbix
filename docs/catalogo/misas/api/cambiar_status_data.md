---
id: "misas.cambiar_status_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/cambiar_status_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/cambiar_status_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["zonas_opciones:object", "orden_opciones:object", "estados_opciones:object"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/cambiar_status.php"]
casos_uso: ["src\\misas\\application\\CambiarStatusPantallaData"]
tags: ["misas", "cambiar", "status", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Cambiar Status Data

Desplegables para la pantalla **Modificar estado del plan de misas**: zonas, criterio de orden y estados del plan. Solo lectura; la mutación real es [`nuevo_status.md`](nuevo_status.md).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Flujo móvil: [`_endpoints_cliente_movil.md`](../_endpoints_cliente_movil.md)

## Endpoint

- URL: `/src/misas/cambiar_status_data`
- Métodos: `POST` (recomendado), `GET` registrado
- Controller: `src/misas/infrastructure/ui/http/controllers/cambiar_status_data.php`

## Entrada

Sin parámetros POST. Usa sesión y rol del usuario (`IdNomJefeResolver`).

## Salida

- Helper: `ContestarJson::enviar`
- `data`: objeto serializado como string JSON (segundo parse).

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `zonas_opciones` | object | Mapa `id_zona → nombre` |
| `orden_opciones` | object | Claves `orden`, `prioridad`, `desc_enc` |
| `estados_opciones` | object | Mapa `1/2/3 → etiqueta` (propuesta, comunicado SACD, comunicado centros) |

### Errores

Si el usuario no puede resolver jefe de calendario (rol `p-sacd` sin permiso): `success: false`, `mensaje` traducido (*No tiene permiso para ver esta página*).

## Flujo móvil

1. `cambiar_status_data` → filtros.
2. `ver_cuadricula_zona_data` con `tipo_plantilla=p` para previsualizar (consulta; cambio de estado no implementado en móvil).

## Ejemplo

```http
POST /orbix/src/misas/cambiar_status_data HTTP/1.1
Accept: application/json
Cookie: PHPSESSID=...
```

```json
{
  "success": true,
  "data": "{\"zonas_opciones\":{\"12\":\"Zona Norte\"},\"orden_opciones\":{\"desc_enc\":\"alfabético\"},\"estados_opciones\":{\"1\":\"Propuesta\",\"2\":\"Comunicado sacerdotes\"}}"
}
```

## Cliente de referencia

- `orbix-android`: `fetchCambiarStatusPantalla()` — pantalla `CambiarStatusPlan` (solo consulta).
