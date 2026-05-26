---
id: "misas.modificar_encargos_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_encargos_data"
metodos: ["GET", "POST"]
operacion: "consulta"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_encargos_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data: ["a_opciones_zona:object", "a_orden:object"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_encargos.php"]
casos_uso: ["src\\misas\\application\\ModificarEncargosData"]
tags: ["misas", "modificar", "encargos", "data", "cliente_movil"]
estado_revision: "revisado"
---

# Modificar Encargos Data

Desplegables iniciales de **Crear y modificar los encargos**: zonas visibles y criterios de orden. La edición usa otros endpoints (`guardar_encargo_zona`, etc.).

Convenciones: [`_convenciones_api.md`](../_convenciones_api.md) · Grid: [`ver_encargos_zona_data.md`](ver_encargos_zona_data.md)

## Endpoint

- URL: `/src/misas/modificar_encargos_data`
- Métodos: `POST` (recomendado)
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_encargos_data.php`

## Entrada

Sin parámetros POST. Permisos vía `IdNomJefeResolver` (rol `p-sacd` limitado a zonas de su `id_pau` si no es jefe de calendario).

## Salida

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `a_opciones_zona` | object | Mapa `id_zona → nombre` |
| `a_orden` | object | `orden`, `prioridad`, `desc_enc` |

### Errores

Si falta permiso: `success: false`, `mensaje` traducido; `data` puede traer mapas vacíos.

## Cliente de referencia

- `orbix-android`: `fetchModificarEncargosPage()` → `EncargosZonaScreen` (consulta; edición no en móvil).
