---
id: "zonassacd.zona_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Consultar y reasignar los centros de una zona"
capacidad: "zonassacd.zona_ctr.gestionar"
pantallas_principales: ["zonassacd.pantalla.zona_ctr"]
fragmentos: ["zonassacd.pantalla.zona_ctr", "zonassacd.pantalla.zona_ctr_lista_ajax", "zonassacd.pantalla.zona_ctr_update_ajax"]
acciones: ["crear_actualizar", "ejecutar", "listar"]
endpoints: ["/src/zonassacd/zona_ctr", "/src/zonassacd/zona_ctr_lista", "/src/zonassacd/zona_ctr_update"]
estado_revision: "revisado"
---

# Flujo - Zona Ctr

## Objetivo De Usuario

Ver qué centros pertenecen a cada zona de sacerdotes y pasarlos de una zona a otra.

## Punto De Entrada

Menú **dre > zonas > zonas-ctr** (Pills2: ATENCIÓN SACD > Gestión de zonas > Zonas-ctr).

## Escenarios Inferidos

### Consultar y reasignar los centros de una zona

Pasos propuestos:
1. Abrir «Lista de centros de la zona» y elegir la zona en el desplegable (por ejemplo Pedralbes, o «sin asignar zona» / «sin asignar zona sf»).
2. Aparece la tabla de centros con su zona (las filas sombreadas en azul claro son centros de la sf).
3. Para cambiarlos de zona, marcar los centros, elegir la zona de destino en «asignar los centros marcados a la zona» y pulsar **asignar** (solo si tu rol lo permite).

Endpoints asociados:
- `/src/zonassacd/zona_ctr`
- `/src/zonassacd/zona_ctr_lista`
- `/src/zonassacd/zona_ctr_update`

## Fragmentos O Pantallas Auxiliares

- `zonassacd.pantalla.zona_ctr`
- `zonassacd.pantalla.zona_ctr_lista_ajax`
- `zonassacd.pantalla.zona_ctr_update_ajax`

## Endpoints Del Flujo

- `/src/zonassacd/zona_ctr`
- `/src/zonassacd/zona_ctr_lista`
- `/src/zonassacd/zona_ctr_update`

## Errores Conocidos

- `hay un error, no se ha guardado.`

## Ruta de menú

- **Legacy:** dre > zonas > zonas-ctr
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Zonas-ctr
