---
id: "dbextern.sincro_crear_todos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Crear todas las fichas pendientes"
capacidad: "dbextern.sincro_crear_todos.gestionar"
pantallas_principales: ["dbextern.pantalla.ver_listas"]
fragmentos: ["dbextern.pantalla.ver_listas"]
acciones: ["crear"]
endpoints: ["/src/dbextern/sincro_crear_todos"]
estado_revision: "revisado"
---

# Flujo - Crear todas desde BDU

Alta masiva de personas BDU sin vínculo.

## Objetivo De Usuario

Crear de golpe todas las fichas pendientes del punto 4, sin revisarlas una a una.

## Punto De Entrada

Botón **crear todos** en la pantalla «Revisar BDU no unidas».

## Escenarios Inferidos

### Crear todas las fichas pendientes

Pasos propuestos:
1. Abrir «Revisar BDU no unidas» (punto 4).
2. Pulsar **crear todos**.
3. Si todo va bien aparece «Ja está»; si alguna persona falla, se muestran los errores acumulados.

Endpoints asociados:
- `/src/dbextern/sincro_crear_todos`

## Endpoints Del Flujo

- `/src/dbextern/sincro_crear_todos`

## Errores Conocidos

- Errores de `sincro_crear` por cada persona fallida

## Ruta de menú

- sin entrada de menú en el índice
