---
id: "profesores.lista_por_departamentos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Consultar claustro"
capacidad: "profesores.lista_por_departamentos.gestionar"
pantallas_principales: ["profesores.pantalla.lista_por_departamentos"]
fragmentos: []
acciones: ["consultar"]
endpoints: ["/src/profesores/lista_por_departamentos"]
estado_revision: "revisado"
---

# Flujo - Consultar claustro

Consulta del claustro STGR agrupado por departamento y tipo de profesor.

## Objetivo De Usuario

Ver quiénes integran el claustro vigente, opcionalmente filtrado por delegación en RSTGR.

## Punto De Entrada

Pantalla `lista_por_departamentos` (`frontend/profesores/controller/lista_por_departamentos.php`).

## Escenarios Inferidos

### Consultar

Pasos:
1. Abrir **claustro** desde el menú.
2. En RSTGR sin filtro: marcar delegaciones y pulsar **Aplicar filtro** (`filtro=1`, `dl[]`).
3. Revisar departamentos con subsecciones director y tipos de profesor.

Endpoints asociados:
- `/src/profesores/lista_por_departamentos`

## Endpoints Del Flujo

- `/src/profesores/lista_por_departamentos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > buscar persona > claustro; stgr > personas > claustro
- **Pills2:** ESTUDIOS > Datos e informes > Claustro
