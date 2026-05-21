---
id: "misas.desplegable_encargos"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/desplegable_encargos"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/desplegable_encargos.php"
entrada: ["post.id_enc:mixed", "post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\DesplegableEncargosData"]
tags: ["misas", "desplegable", "encargos"]
estado_revision: "generado"
---

# Desplegable Encargos

Payload JSON para el desplegable dinamico de encargos de una zona. Sigue el contrato de desplegables de `refactor.md`: - `id` : id del `<select>` que montara `fnjs_construir_desplegable`. - `opciones` : map id_enc => desc_enc de los encargos con `id_tipo_enc >= 8100` de la zona. - `selected` : id_enc preseleccionado (`''` si no aplica). - `blanco` : true si se quiere opcion en blanco. - `val_blanco`: valor de la opcion en blanco. - `action` : handler `onchange` opcional (vacio por defecto).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/desplegable_encargos`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/desplegable_encargos.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_enc` | `mixed` | controller | No | controller |
| `id_zona` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\DesplegableEncargosData`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_centros.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.