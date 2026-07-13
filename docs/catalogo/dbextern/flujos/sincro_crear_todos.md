---
id: "dbextern.sincro_crear_todos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Crear todas desde BDU"
capacidad: "dbextern.sincro_crear_todos.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_listas"]
acciones: ["crear"]
endpoints: ["/src/dbextern/sincro_crear_todos"]
estado_revision: "revisado"
---

# Flujo - Crear todas desde BDU

Alta masiva de personas BDU sin vínculo.

## Objetivo De Usuario

Crear en bloque todas las fichas pendientes del punto 4 sin revisar una a una.

## Punto De Entrada

Botón **crear todos** en `ver_listas` (`fnjs_crear_todos`).

## Escenarios

### Crear

1. Envía `region`, `dl`, `tipo_persona`.
2. Muestra alerta «Ja está» si todo ok, o errores acumulados.

## Endpoints Del Flujo

- `/src/dbextern/sincro_crear_todos`

## Errores Conocidos

- Errores de `sincro_crear` por cada persona fallida

## Ruta de menú

- sin entrada de menú en el índice
