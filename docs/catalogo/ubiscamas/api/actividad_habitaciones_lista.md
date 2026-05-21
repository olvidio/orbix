---
id: "ubiscamas.actividad_habitaciones_lista"
tipo: "endpoint"
modulo: "ubiscamas"
url: "/src/ubiscamas/actividad_habitaciones_lista"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/ubiscamas/infrastructure/ui/http/controllers/actividad_habitaciones_lista.php"
entrada: ["post.id_activ:string"]
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/ubiscamas/controller/lista_habitaciones.php", "frontend/ubiscamas/controller/lista_habitaciones_distribucion.php", "frontend/ubiscamas/controller/lista_habitaciones_nombres.php"]
casos_uso: ["src\\ubiscamas\\application\\HabitacionesCamaLista"]
tags: ["ubiscamas", "actividad", "habitaciones", "lista"]
estado_revision: "generado"
---

# Actividad Habitaciones Lista

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/ubiscamas/actividad_habitaciones_lista`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/ubiscamas/infrastructure/ui/http/controllers/actividad_habitaciones_lista.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `id_activ` | `string` | controller | No | controller |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\ubiscamas\application\HabitacionesCamaLista`

## Frontend Relacionado

- `frontend/ubiscamas/controller/lista_habitaciones.php`
- `frontend/ubiscamas/controller/lista_habitaciones_distribucion.php`
- `frontend/ubiscamas/controller/lista_habitaciones_nombres.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.