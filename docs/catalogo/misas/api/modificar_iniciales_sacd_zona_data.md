---
id: "misas.modificar_iniciales_sacd_zona_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_iniciales_sacd_zona_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_iniciales_sacd_zona_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_iniciales_sacd_zona.php"]
casos_uso: ["src\\misas\\application\\ModificarInicialesSacdZonaData"]
tags: ["misas", "modificar", "iniciales", "sacd", "zona", "data"]
estado_revision: "generado"
---

# Modificar Iniciales Sacd Zona Data

Descripcion funcional pendiente de revisar.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/modificar_iniciales_sacd_zona_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_iniciales_sacd_zona_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\ModificarInicialesSacdZonaData`

## Frontend Relacionado

- `frontend/misas/controller/modificar_iniciales_sacd_zona.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.