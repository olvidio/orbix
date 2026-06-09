---
id: "misas.desplegable_centros_zona"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/desplegable_centros_zona"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/desplegable_centros_zona.php"
entrada: ["post.id_ubi:mixed", "post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\DesplegableCentrosZonaData"]
tags: ["misas", "desplegable", "centros", "zona"]
estado_revision: "generado"
---

# Desplegable Centros Zona

Payload JSON para el desplegable de centros activos de una zona. Orden: sf (alfabetico), linea separadora, sv (alfabetico).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/desplegable_centros_zona`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/desplegable_centros_zona.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_ubi` | `mixed` | controller | No | controller |
| `id_zona` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\DesplegableCentrosZonaData`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_centros.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.