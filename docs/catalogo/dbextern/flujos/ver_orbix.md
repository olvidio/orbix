---
id: "dbextern.ver_orbix.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dbextern"
nombre: "Flujo - Revisar Aquinate sin BDU"
capacidad: "dbextern.ver_orbix.gestionar"
pantallas_principales: []
fragmentos: ["dbextern.pantalla.ver_orbix"]
acciones: ["obtener_datos", "navegar"]
endpoints: ["/src/dbextern/ver_orbix_datos"]
estado_revision: "revisado"
---

# Flujo - Revisar Aquinate sin BDU

Exploración del punto 9.

## Objetivo De Usuario

Revisar personas Aquinate activas sin correspondencia BDU y unir si hay candidato.

## Punto De Entrada

**ver** del punto 9 en `sincro_index` → `ver_orbix.php`.

## Escenarios

### Obtener datos

1. Carga lista en `$_SESSION['DBOrbix']`.
2. Por persona: candidatos BDU vía `id_nom_orbix`.

### Navegar

1. Anterior/siguiente sobre la cola en sesión.

## Endpoints Del Flujo

- `/src/dbextern/ver_orbix_datos`

## Errores Conocidos

- `No existe la clase de la persona`

## Ruta de menú

- sin entrada de menú en el índice
