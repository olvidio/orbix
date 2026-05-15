---
id: "actividadcargos.cargo_eliminar"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/cargo_eliminar"
metodos: ["GET", "POST"]
controller: "src/actividadcargos/infrastructure/ui/http/controllers/cargo_eliminar.php"
entrada: []
respuesta: "standard_envelope_string_data"
frontend_referencias: []
casos_uso: ["src\\actividadcargos\\application\\ActividadCargoEliminar"]
tags: ["actividadcargos", "cargo", "eliminar"]
estado_revision: "generado"
---

# Cargo Eliminar

Elimina un `ActividadCargo` y, si procede, su `Asistente`.

## Endpoint

- URL: `/src/actividadcargos/cargo_eliminar`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_eliminar.php`

## Entrada Inferida

No se han detectado parametros individuales mediante `filter_input`, `$_POST[...]` o `$_GET[...]`.
- Nota: el controller usa `$_POST` directamente; revisar si acepta mas campos que los listados.

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error_txt, 'ok'`

## Casos De Uso Detectados

- `src\actividadcargos\application\ActividadCargoEliminar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
