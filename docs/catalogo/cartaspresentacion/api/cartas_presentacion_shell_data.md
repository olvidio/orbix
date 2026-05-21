---
id: "cartaspresentacion.cartas_presentacion_shell_data"
tipo: "endpoint"
modulo: "cartaspresentacion"
url: "/src/cartaspresentacion/cartas_presentacion_shell_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_shell_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/cartaspresentacion/controller/cartas_presentacion.php"]
casos_uso: ["src\\cartaspresentacion\\application\\CartasPresentacionShellData"]
tags: ["cartaspresentacion", "cartas", "presentacion", "shell", "data"]
estado_revision: "generado"
---

# Cartas Presentacion Shell Data

Datos para la shell `cartas_presentacion.php`: delegación y paths relativos. URLs absolutas y fragment Hash: {

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/cartaspresentacion/cartas_presentacion_shell_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/cartaspresentacion/infrastructure/ui/http/controllers/cartas_presentacion_shell_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\cartaspresentacion\application\CartasPresentacionShellData`

## Frontend Relacionado

- `frontend/cartaspresentacion/controller/cartas_presentacion.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.