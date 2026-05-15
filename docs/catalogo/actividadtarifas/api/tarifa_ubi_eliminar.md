---
id: "actividadtarifas.tarifa_ubi_eliminar"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_eliminar"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php"
entrada: ["post.ctx_eliminar:string"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiEliminar"]
tags: ["actividadtarifas", "tarifa", "ubi", "eliminar"]
estado_revision: "generado"
---

# Tarifa Ubi Eliminar

Endpoint backend: elimina una `TarifaUbi`.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_eliminar`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_eliminar.php`

## Entrada Inferida

- `post.ctx_eliminar` (`string`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `_("Operación no autorizada"), 'none'`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TarifaUbiEliminar`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
