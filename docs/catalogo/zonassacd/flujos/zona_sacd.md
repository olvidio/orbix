---
id: "zonassacd.zona_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Consultar los sacerdotes de una zona"
capacidad: "zonassacd.zona_sacd.gestionar"
pantallas_principales: ["zonassacd.pantalla.zona_sacd"]
fragmentos: ["zonassacd.pantalla.zona_sacd", "zonassacd.pantalla.zona_sacd_lista_ajax", "zonassacd.pantalla.zona_sacd_update_ajax"]
acciones: ["crear_actualizar", "ejecutar", "listar"]
endpoints: ["/src/zonassacd/zona_sacd", "/src/zonassacd/zona_sacd_lista", "/src/zonassacd/zona_sacd_update"]
estado_revision: "revisado"
---

# Flujo - Zona Sacd

## Objetivo De Usuario

Ver los sacerdotes de cada zona, los días de la semana que atienden, y cambiarlos de zona.

## Punto De Entrada

Menú **dre > zonas > zonas-sacd** (Pills2: ATENCIÓN SACD > Gestión de zonas > Zonas-sacd).

## Escenarios Inferidos

### Consultar los sacerdotes de una zona

Pasos propuestos:
1. Abrir «Lista de sacd de la zona» y elegir la zona (o «sin asignar zona»).
2. Aparece una tabla con sacerdote, zona, **propia** (1 si es su zona propia) y los días **L M X J V S D** (una «x» o un «-» en cada día: indica si el sacerdote atiende o no ese día, es decir, si está o no está).

Endpoints asociados:
- `/src/zonassacd/zona_sacd`
- `/src/zonassacd/zona_sacd_lista`
- `/src/zonassacd/zona_sacd_update`

### Cambiar los días o la zona de un sacerdote

Pasos propuestos:
1. Marcar al sacerdote y pulsar **modificar**: se abre una ventana con su nombre, la casilla **propia** y los días (Lunes a Domingo). Pulsar **Grabar** para guardar o **Cancel** para salir.
2. Para cambiar de zona a varios sacerdotes: marcarlos, elegir la zona en «asignar los sacd marcados a la zona» y pulsar **cambiar asignación zona**; con **añadir asignación iglesia/cgi** en lugar de cambiar de zona se añade otra, de modo que el sacerdote queda con dos zonas.

Endpoints asociados:
- `/src/zonassacd/zona_sacd`
- `/src/zonassacd/zona_sacd_lista`
- `/src/zonassacd/zona_sacd_update`

## Fragmentos O Pantallas Auxiliares

- `zonassacd.pantalla.zona_sacd`
- `zonassacd.pantalla.zona_sacd_lista_ajax`
- `zonassacd.pantalla.zona_sacd_update_ajax`

## Endpoints Del Flujo

- `/src/zonassacd/zona_sacd`
- `/src/zonassacd/zona_sacd_lista`
- `/src/zonassacd/zona_sacd_update`

## Errores Conocidos

- `hay un error, no se ha guardado`
- `hay un error, no se ha eliminado`

## Ruta de menú

- **Legacy:** dre > zonas > zonas-sacd
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Zonas-sacd
