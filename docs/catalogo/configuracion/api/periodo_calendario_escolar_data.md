---
id: "configuracion.periodo_calendario_escolar_data"
tipo: "endpoint"
modulo: "configuracion"
url: "/src/configuracion/periodo_calendario_escolar_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/configuracion/infrastructure/ui/http/controllers/periodo_calendario_escolar_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "configuracion_PeriodoCalendarioEscolarDataData"
respuesta_data: ["mes_fin_stgr:integer", "mes_fin_crt:integer", "dia_ini_stgr:integer", "mes_ini_stgr:integer", "dia_fin_stgr:integer", "dia_ini_crt:integer", "mes_ini_crt:integer", "dia_fin_crt:integer", "any_final_est:integer", "any_final_crt:integer"]
requiere_hashb: false
frontend_referencias: ["frontend/shared/web/Periodo.php"]
casos_uso: ["src\\configuracion\\application\\PeriodoCalendarioEscolarData"]
tags: ["configuracion", "periodo", "calendario", "escolar", "data"]
estado_revision: "generado"
---

# Periodo Calendario Escolar Data

Fechas y metadatos del curso (STGR / CRT) que antes solo estaban en `$_SESSION['oConfig']`, para inyectar en `Periodo` del frontend.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/configuracion/periodo_calendario_escolar_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/configuracion/infrastructure/ui/http/controllers/periodo_calendario_escolar_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `configuracion_PeriodoCalendarioEscolarDataData`):
  - `mes_fin_stgr` (`integer`)
  - `mes_fin_crt` (`integer`)
  - `dia_ini_stgr` (`integer`)
  - `mes_ini_stgr` (`integer`)
  - `dia_fin_stgr` (`integer`)
  - `dia_ini_crt` (`integer`)
  - `mes_ini_crt` (`integer`)
  - `dia_fin_crt` (`integer`)
  - `any_final_est` (`integer`)
  - `any_final_crt` (`integer`)

## Casos De Uso

- `src\configuracion\application\PeriodoCalendarioEscolarData`

## Frontend Relacionado

- `frontend/shared/web/Periodo.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.