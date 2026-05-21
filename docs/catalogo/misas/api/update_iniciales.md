---
id: "misas.update_iniciales"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/update_iniciales"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/update_iniciales.php"
entrada: ["post.color:string", "post.id_sacd:integer", "post.iniciales:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/ver_iniciales_zona.php"]
casos_uso: ["src\\misas\\application\\UpdateIniciales"]
tags: ["misas", "update", "iniciales"]
estado_revision: "generado"
---

# Update Iniciales

Inserta o actualiza la fila de iniciales/color para un sacerdote. Devuelve texto vacio si todo fue bien; en otro caso, el mensaje de error del repositorio. El controlador HTTP es quien serializa la respuesta con `ContestarJson::enviar(...)`.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/update_iniciales`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/update_iniciales.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `color` | `string` | controller | No | controller |
| `id_sacd` | `integer` | controller | No | controller |
| `iniciales` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\UpdateIniciales`

## Frontend Relacionado

- `frontend/misas/controller/ver_iniciales_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.