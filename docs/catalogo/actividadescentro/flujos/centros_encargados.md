---
id: "actividadescentro.centros_encargados.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadescentro"
nombre: "Flujo - Gestionar Centros Encargados"
capacidad: "actividadescentro.centros_encargados.gestionar"
pantallas_principales: []
fragmentos: ["actividadescentro.pantalla.activ_ctr"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadescentro/centros_encargados_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Centros Encargados

Recarga de la celda de centros encargados de una actividad tras una mutación.

## Objetivo De Usuario

Tras asignar, reordenar o eliminar un centro encargado, la celda de esa actividad se refresca con la
lista actualizada de centros y el flag `permite_modificar` (que decide si cada centro se pinta como
enlace o como texto plano). Es un paso automático, no una acción explícita del usuario.

## Punto De Entrada

Pantalla `activ_ctr` (`frontend/actividadescentro/controller/activ_ctr.php`): la función
`fnjs_actualizar_activ` llama a este endpoint después de cada mutación.

## Fragmentos O Pantallas Auxiliares

- `actividadescentro.pantalla.activ_ctr`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadescentro/centros_encargados_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_ctr` (colectivo según `tipo`):

- **Legacy:** dre > actividades > asignar centros (y variantes por tipo: activ sg, activ sr, sv n y
  agd, sf s y sg, sf sr, sf n, nax y agd, sss+); también Calendario > actividades > asignar centros.
- **Pills2:** dre > actividades > asignar centros (mismas variantes); Calendario > actividades >
  asignar centros; ACTIVIDADES > Listados > Asignar ctr organizadores sg / sr.
