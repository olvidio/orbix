---
id: "misas.modificar_encargos_data"
tipo: "endpoint"
modulo: "misas"
url: "/src/misas/modificar_encargos_data"
metodos: ["GET", "POST"]
operacion: "mutacion"
controller: "src/misas/infrastructure/ui/http/controllers/modificar_encargos_data.php"
entrada: []
entrada_obligatoria: []
respuesta: "standard_envelope_string_data"
requiere_hashb: false
frontend_referencias: ["frontend/misas/controller/modificar_encargos.php"]
casos_uso: ["src\\misas\\application\\ModificarEncargosData"]
tags: ["misas", "modificar", "encargos", "data"]
estado_revision: "generado"
---

# Modificar Encargos Data

Devuelve los datos para pintar la pantalla `modificar_encargos`: el desplegable de zonas (filtrado segun el rol del usuario) y la lista de criterios de orden aceptados por el grid. Replica la logica de `apps/misas/controller/modificar_encargos.php`: si el rol es `p-sacd` y NO es jefe de calendario, se limitan las zonas a las del `id_pau` del propio usuario. Devuelve: - `error` : texto vacio si todo ok, mensaje si el usuario no tiene permiso para ver la pantalla. - `a_opciones_zona`: array id_zona => nombre_zona. - `a_orden` : array criterio => label.

Convenciones generales: [`_convenciones_api.md`](../_convenciones_api.md).

## Endpoint

- URL: `/src/misas/modificar_encargos_data`
- Metodos registrados: `GET, POST`
- Operacion: `mutacion`
- Controller: `src/misas/infrastructure/ui/http/controllers/modificar_encargos_data.php`

## Entrada

Sin parametros POST detectados (puede ser un listado sin filtros o un endpoint que lee la sesion).

## Salida

- Helper: `ContestarJson::enviar`
- Forma: `standard_envelope_string_data`
- Exito: `success: true`, `data: "ok"`.

## Casos De Uso

- `src\misas\application\ModificarEncargosData`

## Frontend Relacionado

- `frontend/misas/controller/modificar_encargos.php`

## Revision Manual

- Confirmar permisos/autorizacion de oficina.
- Anadir ejemplos reales de request/response.
- Marcar `estado_revision: "revisado"` cuando este validado.