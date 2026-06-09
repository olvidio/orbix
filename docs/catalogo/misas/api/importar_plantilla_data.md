---
id: "misas.importar_plantilla_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/importar_plantilla_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/importar_plantilla_data.php"
entrada: ["post.id_zona:mixed", "post.tipo_plantilla_destino:mixed", "post.tipo_plantilla_origen:mixed"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/importar_plantilla.php"]
casos_uso: ["src\\misas\\application\\ImportarPlantillaData", "src\\misas\\application\\support\\MisasBuildInput"]
tags: ["misas", "importar", "plantilla", "data"]
estado_revision: "generado"
---

# Importar Plantilla Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/importar_plantilla_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/importar_plantilla_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `mixed` | controller | No | controller |
| `tipo_plantilla_destino` | `mixed` | controller | No | controller |
| `tipo_plantilla_origen` | `mixed` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\ImportarPlantillaData`
- `src\misas\application\support\MisasBuildInput`

## Frontend Relacionado

- `frontend/misas/controller/importar_plantilla.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.