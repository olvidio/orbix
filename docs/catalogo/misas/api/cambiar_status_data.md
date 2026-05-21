---
id: "misas.cambiar_status_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/cambiar_status_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/cambiar_status_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_CambiarStatusPantallaDataData"
respuesta_data: ["zonas_opciones:array", "orden_opciones:array", "estados_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/cambiar_status.php"]
casos_uso: ["src\\misas\\application\\CambiarStatusPantallaData"]
tags: ["misas", "cambiar", "status", "data"]
estado_revision: "generado"
---

# Cambiar Status Data

Formulario "Cambiar estado del plan de misas" (zona, estado, orden).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/cambiar_status_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/cambiar_status_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_CambiarStatusPantallaDataData`):
  - `zonas_opciones` (`array`)
  - `orden_opciones` (`array`)
  - `estados_opciones` (`array`)

## Casos De Uso

- `src\misas\application\CambiarStatusPantallaData`

## Frontend Relacionado

- `frontend/misas/controller/cambiar_status.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.