---
id: "misas.modificar_encargos_centros_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_encargos_centros_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_encargos_centros_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
respuesta_data_schema: "misas_ModificarEncargosCentrosDataData"
respuesta_data: ["error:string, a_opciones_zona: array<int|string, string>"]
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\ModificarEncargosCentrosData"]
tags: ["misas", "modificar", "encargos", "centros", "data"]
estado_revision: "generado"
---

# Modificar Encargos Centros Data

Devuelve el desplegable de zonas que el usuario actual puede ver, para pintar la pantalla `modificar_encargos_centros`. Replica la logica de permisos de `apps/misas/controller/modificar_encargos_centros.php`: si el rol es `p-sacd` y NO es jefe de calendario, se limitan las zonas a las del `id_pau` del propio usuario. Devuelve: - `error` : texto vacio si todo ok, mensaje si falta permiso. - `a_opciones_zona`: array id_zona => nombre_zona.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/modificar_encargos_centros_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_encargos_centros_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.
- Payload en `data` (schema `misas_ModificarEncargosCentrosDataData`):
  - `error` (`string, a_opciones_zona: array<int|string, string>`)

## Casos De Uso

- `src\misas\application\ModificarEncargosCentrosData`

## Frontend Relacionado

- `frontend/misas/controller/modificar_encargos_centros.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.