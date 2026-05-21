---
id: "misas.ver_encargos_centros_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_encargos_centros_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/ver_encargos_centros_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_encargos_centros.php"]
casos_uso: ["src\\misas\\application\\VerEncargosCentrosData"]
tags: ["misas", "ver", "encargos", "centros", "data"]
estado_revision: "generado"
---

# Ver Encargos Centros Data

Devuelve los datos del SlickGrid de `EncargoCtr` (encargos visibles para cada centro de una zona) + los desplegables estaticos del modal de edicion (zonas posibles para filtrar encargos, centros de la zona). El desplegable dinamico de encargos (que cambia al seleccionar zona en el modal) no se incluye aqui: el frontend lo pide por separado a `DesplegableEncargosData` cuando el usuario lo necesita.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/ver_encargos_centros_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_encargos_centros_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\VerEncargosCentrosData`

## Frontend Relacionado

- `frontend/misas/controller/ver_encargos_centros.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.