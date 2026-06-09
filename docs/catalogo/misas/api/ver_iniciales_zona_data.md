---
id: "misas.ver_iniciales_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/ver_iniciales_zona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/ver_iniciales_zona_data.php"
entrada: ["post.id_zona:integer"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_iniciales_zona.php"]
casos_uso: ["src\\misas\\application\\VerInicialesZonaData"]
tags: ["misas", "ver", "iniciales", "zona", "data"]
estado_revision: "generado"
---

# Ver Iniciales Zona Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/ver_iniciales_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/ver_iniciales_zona_data.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_zona` | `integer` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\VerInicialesZonaData`

## Frontend Relacionado

- `frontend/misas/controller/ver_iniciales_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.