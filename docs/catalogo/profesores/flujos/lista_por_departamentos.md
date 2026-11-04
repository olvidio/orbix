---
id: "profesores.lista_por_departamentos.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "profesores"
nombre: "Flujo - Ver el claustro por departamentos"
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

Ver el claustro de profesores agrupado por departamento y categoría.

## Punto De Entrada

Menú **vest > buscar persona > claustro** (Pills2: stgr > personas > claustro).

## Escenarios Inferidos

### Ver el claustro por departamentos

Pasos propuestos:
1. Entrar en **claustro**.
2. Se muestra cada departamento (por ejemplo «Música») con sus categorías: director, Ordinario, Extraordinario, Adjunto, Encargado, Ayudante y Asociado.
3. Bajo cada categoría se listan los profesores que la tienen. Si una categoría sale sin nombres es que ahora mismo no hay nadie en ella (en la instancia de desarrollo solo se ven los títulos).

Endpoints asociados:
- `/src/profesores/lista_por_departamentos`

## Endpoints Del Flujo

- `/src/profesores/lista_por_departamentos`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** vest > buscar persona > claustro; stgr > personas > claustro
- **Pills2:** ESTUDIOS > Datos e informes > Claustro
