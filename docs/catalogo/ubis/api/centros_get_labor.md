---
id: "ubis.centros_get_labor"
tipo: "endpoint"
modulo: "ubis"
url: "/src/ubis/centros_get_labor"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubis/infrastructure/ui/http/controllers/centros_get_labor.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubis/controller/centros_get_labor.php"]
casos_uso: ["src\\ubis\\application\\CentrosGetLaborData"]
tags: ["ubis", "centros", "get", "labor"]
estado_revision: "generado"
---

# Centros Get Labor

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubis/centros_get_labor`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubis/infrastructure/ui/http/controllers/centros_get_labor.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubis\application\CentrosGetLaborData`

## Frontend Relacionado

- `frontend/ubis/controller/centros_get_labor.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.