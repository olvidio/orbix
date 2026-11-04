---
id: "actividades.actividad_importar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividades"
nombre: "Flujo - Importar actividades de otra dl"
capacidad: "actividades.actividad_importar.gestionar"
pantallas_principales: []
fragmentos: ["actividades.pantalla.actividad_que", "actividades.pantalla.actividad_select"]
acciones: ["ejecutar"]
endpoints: ["/src/actividades/actividad_importar"]
estado_revision: "revisado"
---

# Flujo - Importar actividad de otra dl

Copiar actividades de otras delegaciones/regions a la dl propia.

## Objetivo De Usuario

Buscar actividades de otra delegación o región, elegir una o varias e importarlas a la propia.

## Punto De Entrada

Menú **importar activ** (hay variantes por colectivo: crt, cv, ca; n o agd). Primero sale el formulario de búsqueda y después la lista de resultados.

## Escenarios Inferidos

### Importar actividades de otra dl

Pasos propuestos:
1. Abrir la opción de importar del menú.
2. Rellenar el formulario de búsqueda y pulsar buscar para localizar la actividad de origen.
3. En la lista, marcar las actividades que se quieren importar y pulsar **importar**.
4. Revisar los mensajes: sale uno por cada actividad importada o con error.

Endpoints asociados:
- `/src/actividades/actividad_importar`

## Endpoints Del Flujo

- `/src/actividades/actividad_importar`

## Errores Conocidos

- `hay un error, no se ha importado` + detalle (por id fallido)

## Ruta de menú

- **Legacy:** dre/Calendario > actividades > importar activ; variantes dagd/vsm/vest (crt, cv, ca…).
- **Pills2:** ACTIVIDADES > Buscar actividad > Importar crt/cv/ca n/agd de otras r/dl; ESTUDIOS >
  Semestres de invierno > Importar.
