---
id: "actividades.actividad_publicar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Publicar actividades"
capacidad: "actividades.actividad_publicar.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_select"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_publicar"]
estado_revision: "revisado"
---

# Flujo - Publicar actividades

Marcar actividades de la dl propia como publicadas (visibles externamente según reglas).

## Objetivo De Usuario

Buscar actividades que aún no están publicadas y publicarlas de golpe.

## Punto De Entrada

Menú **publicar activ**. Primero sale el formulario de búsqueda y después la lista de resultados.

## Escenarios Inferidos

### Publicar actividades

Pasos propuestos:
1. Abrir **publicar activ** del menú.
2. Buscar las actividades de la propia dl que aún no están publicadas.
3. En la lista, marcar las filas y pulsar **publicar**.
4. Comprobar en el listado que ya aparecen como publicadas.

Endpoints asociados:
- `/src/actividades/actividad_publicar`

## Endpoints Del Flujo

- `/src/actividades/actividad_publicar`

## Errores Conocidos

- `hay un error, no se ha guardado` + detalle (por id fallido)

## Ruta de menú

- **Legacy:** dre/Calendario > actividades > publicar activ.
- **Pills2:** dre/Calendario > actividades > publicar activ; ATENCIÓN SACD > Actividades >
  publicar activ.
