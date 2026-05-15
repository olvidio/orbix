---
id: "actividadcargos.cargo_editar"
tipo: "endpoint"
modulo: "actividadcargos"
url: "/src/actividadcargos/cargo_editar"
metodos: ["GET", "POST"]
controller: "src/actividadcargos/infrastructure/ui/http/controllers/cargo_editar.php"
entrada: ["post.asis:mixed", "post.asis_presente:mixed"]
respuesta: "standard_envelope_string_data"
frontend_referencias: []
casos_uso: ["src\\actividadcargos\\application\\ActividadCargoEditar"]
tags: ["actividadcargos", "cargo", "editar"]
estado_revision: "generado"
---

# Cargo Editar

Edita un `ActividadCargo` existente.

## Endpoint

- URL: `/src/actividadcargos/cargo_editar`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadcargos/infrastructure/ui/http/controllers/cargo_editar.php`

## Entrada Inferida

- `post.asis` (`mixed`)
- `post.asis_presente` (`mixed`)
- Nota: el controller usa `$_POST` directamente; revisar si acepta mas campos que los listados.

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `$error_txt, 'ok'`

## Casos De Uso Detectados

- `src\actividadcargos\application\ActividadCargoEditar`

## Frontend Relacionado

No se han encontrado referencias exactas al endpoint en `frontend/`.

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
