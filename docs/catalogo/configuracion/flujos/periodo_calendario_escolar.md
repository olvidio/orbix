---
id: "configuracion.periodo_calendario_escolar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Cambiar las fechas del curso"
capacidad: "configuracion.periodo_calendario_escolar.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/configuracion/periodo_calendario_escolar_data"]
estado_revision: "revisado"
---

# Flujo - Periodo calendario escolar (interno)

## Objetivo De Usuario

No hay pantalla de usuario: es un dato interno. Los listados y filtros de calendario usan las fechas de inicio y fin del curso del stgr y del crt para calcular sus periodos.

## Punto De Entrada

No tiene entrada: se usa por debajo cuando una pantalla necesita fechas de curso. Los valores se cambian en **config esquema** (curso del stgr y curso del crt).

## Escenarios Inferidos

### Cambiar las fechas del curso

Pasos propuestos:
1. Abrir **config esquema** (ver «Cambiar un parámetro»).
2. Cambiar el periodo del curso del stgr o del crt y guardar; los listados de calendario usarán las nuevas fechas.

Endpoints asociados:
- `/src/configuracion/periodo_calendario_escolar_data`

## Errores Conocidos

Errores de `PostRequest` propagados como `RuntimeException` si `$throwOnError=true`; en modo
legacy pueden provocar `exit` con mensaje de error.

## Ruta de menú

Sin entrada de menú en el índice (flujo técnico transversal; configuración en «config esquema»).
