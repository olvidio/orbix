---
id: "misas.modificar_plantilla_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_plantilla_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_plantilla_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_PlanDeMisasPantallaDataData"
respuesta_data: ["pantalla:string", "zonas_opciones:array", "orden_opciones:array"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_plantilla.php"]
casos_uso: ["src\\misas\\application\\PlanDeMisasPantallaData"]
tags: ["misas", "modificar", "plantilla", "data"]
estado_revision: "generado"
---

# Modificar Plantilla Data

Datos comunes para las pantallas preparar / modificar / ver plan de misas y para modificar plantilla (mismos desplegables de zona / tipo / orden).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/modificar_plantilla_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_plantilla_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_PlanDeMisasPantallaDataData`):
  - `pantalla` (`string`)
  - `zonas_opciones` (`array`)
  - `orden_opciones` (`array`)

## Casos De Uso

- `src\misas\application\PlanDeMisasPantallaData`

## Frontend Relacionado

- `frontend/misas/controller/modificar_plantilla.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.