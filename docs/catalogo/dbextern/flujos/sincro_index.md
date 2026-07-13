---
id: "dbextern.sincro_index.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Dashboard sincronización BDU"
capacidad: "dbextern.sincro_index.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.sincro_index"]
acciones: ["obtener_datos"]
endpoints: ["/src/dbextern/sincro_index_datos"]
estado_revision: "revisado"
---

# Flujo - Dashboard sincronización BDU

Carga inicial del dashboard «Actualizar datos desde BDU».

## Objetivo De Usuario

Al abrir la pantalla de sincronización, el sistema calcula los contadores de situación BDU↔Aquinate
y prepara enlaces firmados a las subpantallas de resolución.

## Punto De Entrada

Menú «Actualizar datos desde BDU» → `sincro_index.php?tipo=<colectivo>`.

## Escenarios

### Obtener datos

1. El menú envía `tipo` (`n`/`a`/`s`/`sssc`).
2. El controller llama `sincro_index_datos`.
3. Si hay error de permisos o DL, se muestra y termina.
4. Se renderiza la tabla de 9 puntos con contadores y enlaces «ver»/«ejecutar».

## Endpoints Del Flujo

- `/src/dbextern/sincro_index_datos`

## Errores Conocidos

- `No se encontró la delegación en listas`
- `no tiene permisos`
- `No existe la clase de la persona`

## Ruta de menú

- **Legacy:** vsm/dagd/vsg/dre > … > Actualizar datos desde BDU (según `tipo`)
- **Pills2:** PERSONAS > Numerarios / Agregados / Supernumerarios > Actualizar datos desde BDU
