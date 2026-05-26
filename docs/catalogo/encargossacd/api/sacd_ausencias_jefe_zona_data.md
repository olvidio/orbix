---
id: "encargossacd.sacd_ausencias_jefe_zona_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ausencias_jefe_zona_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_jefe_zona_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["a_sacd:object"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasJefeZonaData"]
tags: ["encargossacd", "sacd", "ausencias", "jefe", "zona", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Sacd Ausencias Jefe Zona Data

Lista de sacerdotes para el desplegable de **Ausencias** (vista jefe de zona / oficial).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Siguiente: [`sacd_ausencias_get_data.md`](sacd_ausencias_get_data.md)

## Endpoint

- URL: `/src/encargossacd/sacd_ausencias_jefe_zona_data`
- Métodos: `POST` (recomendado)
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_jefe_zona_data.php`

## Entrada

Sin parámetros POST. Usa sesión, rol y zonas del usuario.

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `a_sacd` | object | Mapa **`iniciales#id_nom` → nombre** (ordenado por iniciales) |

Incluye SACDs de las zonas del jefe; rol `Oficial_dl` o jefe de calendario amplía a todos los SACD activos.

## Flujo móvil

1. Cargar este endpoint → desplegable sacerdote.
2. Extraer `id_nom` de la clave (parte tras `#`).
3. [`sacd_ausencias_get_data`](sacd_ausencias_get_data.md) con `filtro_sacd=n` implícito en web; móvil no envía `filtro_sacd`.

## Ejemplo

```http
POST /orbix/src/encargossacd/sacd_ausencias_jefe_zona_data HTTP/1.1
Accept: application/json
Cookie: PHPSESSID=...
```

```json
{
  "success": true,
  "data": "{\"a_sacd\":{\"JP#42\":\"Juan Pérez\",\"MR#51\":\"María Ruiz\"}}"
}
```

## Cliente de referencia

- `orbix-android`: `fetchSacdAusenciasJefeZonaPage()` — menú `sacd_ausencias_jefe_zona.php`, modo `JefeZona`.
