---
id: "actividadcargos.cargo_nuevo"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/cargo_nuevo"
metodos: ["GET", "POST"]
controller: "src/actividadcargos/infrastructure/ui/http/controllers/cargo_nuevo.php"
entrada: []
respuesta: "standard_envelope_string_data"
frontend_referencias: []
casos_uso: ["src\\actividadcargos\\application\\ActividadCargoNuevo"]
tags: ["actividadcargos", "cargo", "nuevo"]
estado_revision: "generado"
---

# Cargo Nuevo

Crea un `ActividadCargo`.

## Endpoint

- URL: `/src/actividadcargos/cargo_nuevo`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_nuevo.php`

## Entrada Inferida

No se han detectado parametros individuales mediante `filter_input`, `$_POST[...]` o `$_GET[...]`.
- Nota: el controller usa `$_POST` directamente; revisar si acepta mas campos que los listados.

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error_txt, 'ok'`

## Casos De Uso Detectados

- `src\actividadcargos\application\ActividadCargoNuevo`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
