---
id: "misas.modificar_encargos_centros_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_encargos_centros_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_encargos_centros_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["a_opciones_zona:object"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\ModificarEncargosCentrosData"]
tags: ["misas", "modificar", "encargos", "centros", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Modificar Encargos Centros Data

Desplegable de zonas para **Modificar los encargos visibles para un centro**.

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Grid: [`ver_encargos_centros_data.md`](ver_encargos_centros_data.md)

## Endpoint

- URL: `/src/misas/modificar_encargos_centros_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_encargos_centros_data.php`

## Entrada

Sin parámetros POST. Misma lógica de permisos que [`modificar_encargos_data.md`](modificar_encargos_data.md).

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `a_opciones_zona` | object | Mapa `id_zona → nombre` |

### Errores

Sin permiso: `success: false`, `mensaje` traducido.

## Cliente de referencia

- `orbix-android`: `fetchModificarEncargosCentrosPage()` → `EncargosCentrosScreen`.
