---
id: "actividadplazas.peticiones_guardar"
tipo: "endpoint"
modulo: "actividadplazas"
url: "/src/actividadplazas/peticiones_guardar"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/actividadplazas/infrastructure/ui/http/controllers/peticiones_guardar.php"
entrada: ["post.actividades:array", "post.id_nom:integer", "post.sactividad:string"]
entrada_obligatoria: ["id_nom"]
respuesta: "standard_envelope_string_data"
requiere_hashb: false
errores: ["faltan parametros id_nom / sactividad", "hay un error, no se han guardado todas las peticiones"]
frontend_referencias: ["frontend/actividadplazas/controller/peticiones_activ.php"]
casos_uso: ["src\\actividadplazas\\application\\PeticionesGuardar"]
tags: ["actividadplazas", "peticiones", "guardar"]
estado_revision: "generado"
---

# Peticiones Guardar

Endpoint backend: guarda las peticiones de una persona+tipo (borra las anteriores y crea las nuevas en orden).

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/actividadplazas/peticiones_guardar`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/actividadplazas/infrastructure/ui/http/controllers/peticiones_guardar.php`

## Entrada

| Campo | Tipo | Origen | Obligatorio | Notas |
|-------|------|--------|-------------|-------|
| `actividades` | `array` | controller+application | No | controller+application |
| `id_nom` | `integer` | controller+application | Si | controller+application |
| `sactividad` | `string` | controller+application | No | controller+application |

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Errores conocidos

- `faltan parametros id_nom / sactividad`
- `hay un error, no se han guardado todas las peticiones`

## Casos De Uso

- `src\actividadplazas\application\PeticionesGuardar`

## Frontend Relacionado

- `frontend/actividadplazas/controller/peticiones_activ.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.