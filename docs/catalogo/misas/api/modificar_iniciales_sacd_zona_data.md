---
id: "misas.modificar_iniciales_sacd_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_iniciales_sacd_zona_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_iniciales_sacd_zona_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["a_opciones:object"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_iniciales_sacd_zona.php"]
casos_uso: ["src\\misas\\application\\ModificarInicialesSacdZonaData"]
tags: ["misas", "modificar", "iniciales", "sacd", "zona", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Modificar Iniciales Sacd Zona Data

Desplegable de zonas para **Modificar la tabla de iniciales de los sacerdotes**. La mutación es [`update_iniciales.md`](update_iniciales.md).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Grid: [`ver_iniciales_zona_data.md`](ver_iniciales_zona_data.md)

## Endpoint

- URL: `/src/misas/modificar_iniciales_sacd_zona_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_iniciales_sacd_zona_data.php`

## Entrada

Sin parámetros POST.

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `a_opciones` | object | Mapa **todas** las zonas (`getArrayZonas`) |

## Cliente de referencia

- `orbix-android`: `fetchModificarInicialesPage()` → `InicialesZonaScreen`.
