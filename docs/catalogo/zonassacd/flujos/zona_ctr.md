---
id: "zonassacd.zona_ctr.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Gestionar Zona Ctr"
capacidad: "zonassacd.zona_ctr.gestionar"
pantallas_principales: []
fragmentos: ["zonassacd.pantalla.zona_ctr", "zonassacd.pantalla.zona_ctr_lista_ajax", "zonassacd.pantalla.zona_ctr_update_ajax"]
acciones: ["crear_actualizar", "ejecutar", "listar"]
endpoints: ["/src/zonassacd/zona_ctr", "/src/zonassacd/zona_ctr_lista", "/src/zonassacd/zona_ctr_update"]
estado_revision: "revisado"
---

# Flujo - Zona Ctr

## Objetivo De Usuario

Consultar y reasignar centros (dl y sf) a zonas geográficas desde el desplegable de zona.

## Punto De Entrada

Menú Legacy: dre > zonas > zonas-ctr. Pills2: ATENCIÓN SACD > Gestión de zonas > Zonas-ctr.

## Escenarios

### Consultar centros de una zona

1. Abrir Zonas-ctr.
2. Elegir zona (`int`, `no`, o `no_sf` con perm_des) → AJAX `zona_ctr_lista`.

### Reasignar centros (perm_des)

1. Marcar centros, elegir zona destino (o «sin asignar zona»).
2. Pulsar «asignar» → `zona_ctr_update`.

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
