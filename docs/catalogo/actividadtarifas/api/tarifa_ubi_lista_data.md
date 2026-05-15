---
id: "actividadtarifas.tarifa_ubi_lista_data"
tipo: "endpoint"
modulo: "actividadtarifas"
url: "/src/actividadtarifas/tarifa_ubi_lista_data"
metodos: ["GET", "POST"]
controller: "src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_lista_data.php"
entrada: ["post.id_ubi:integer", "post.year:integer"]
respuesta: "standard_envelope_string_data"
frontend_referencias: ["frontend/actividadtarifas/controller/tarifa_ubi_lista.php"]
casos_uso: ["src\\actividadtarifas\\application\\TarifaUbiListaData"]
tags: ["actividadtarifas", "tarifa", "ubi", "lista", "data"]
estado_revision: "generado"
---

# Tarifa Ubi Lista Data

Endpoint backend: listado de `TarifaUbi` por `id_ubi` + `year`.

## Endpoint

- URL: `/src/actividadtarifas/tarifa_ubi_lista_data`
- Metodos registrados: `GET, POST`
- Controller: `src/actividadtarifas/infrastructure/ui/http/controllers/tarifa_ubi_lista_data.php`

## Entrada Inferida

- `post.id_ubi` (`integer`)
- `post.year` (`integer`)

## Salida Inferida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Evidencia: `'', $data`

## Casos De Uso Detectados

- `src\actividadtarifas\application\TarifaUbiListaData`

## Frontend Relacionado

- `frontend/actividadtarifas/controller/tarifa_ubi_lista.php`

## Revision Manual

- Completar objetivo funcional.
- Confirmar permisos/autorizacion.
- Confirmar efectos sobre datos.
- Anadir ejemplos reales de request/response.
- Marcar procesos parecidos o duplicados si aplica.
