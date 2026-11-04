---
id: "actividadescentro.lista_actividades_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Consultar las actividades y sus centros encargados"
capacidad: "actividadescentro.lista_actividades_ctr.gestionar"
pantallas_principales: ["actividadescentro.pantalla.activ_ctr"]
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/lista_actividades_ctr_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Lista Actividades Ctr

Listado de actividades del colectivo + periodo elegidos, con sus centros encargados.

## Objetivo De Usuario

Ver las actividades de un periodo con los centros encargados actuales.

## Punto De Entrada

Pantalla de asignación de centros encargados.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Consultar las actividades y sus centros encargados

Pasos propuestos:
1. Elegir el periodo.
2. Pulsar **buscar**.
3. Se muestra la tabla de actividades con sus centros encargados; según los permisos aparecen los botones para modificar o crear.

Endpoints asociados:
- `/src/actividadescentro/lista_actividades_ctr_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadescentro/lista_actividades_ctr_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
