---
id: "encargossacd.sacd_ausencias_jefe_zona_data"
tipo: "endpoint"
modulo: "encargossacd"
url: "/src/encargossacd/sacd_ausencias_jefe_zona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_jefe_zona_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "encargossacd_SacdAusenciasJefeZonaDataData"
respuesta_data: ["a_sacd:array"]
requiere_hashb: false
frontend_referencias: ["frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php"]
casos_uso: ["src\\encargossacd\\application\\SacdAusenciasJefeZonaData"]
tags: ["encargossacd", "sacd", "ausencias", "jefe", "zona", "data"]
estado_revision: "generado"
---

# Sacd Ausencias Jefe Zona Data

Datos para el listado de SACDs susceptibles de gestionar ausencias desde la ficha de jefe de zona (`frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php`). Recopila los SACDs de la(s) zona(s) del jefe y, cuando corresponde (Oficial_dl o jefe de calendario), la totalidad de SACDs activos. El array se devuelve ordenado por iniciales para alimentar el desplegable.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/encargossacd/sacd_ausencias_jefe_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/encargossacd/infrastructure/ui/http/controllers/sacd_ausencias_jefe_zona_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `encargossacd_SacdAusenciasJefeZonaDataData`):
  - `a_sacd` (`array`)

## Casos De Uso

- `src\encargossacd\application\SacdAusenciasJefeZonaData`

## Frontend Relacionado

- `frontend/encargossacd/controller/sacd_ausencias_jefe_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.