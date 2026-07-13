---
id: "zonassacd.zona_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "zonassacd"
nombre: "Flujo - Gestionar Zona Sacd"
capacidad: "zonassacd.zona_sacd.gestionar"
pantallas_principales: []
fragmentos: ["zonassacd.pantalla.zona_sacd", "zonassacd.pantalla.zona_sacd_lista_ajax", "zonassacd.pantalla.zona_sacd_update_ajax"]
acciones: ["crear_actualizar", "ejecutar", "listar"]
endpoints: ["/src/zonassacd/zona_sacd", "/src/zonassacd/zona_sacd_lista", "/src/zonassacd/zona_sacd_update"]
estado_revision: "revisado"
---

# Flujo - Zona Sacd

## Objetivo De Usuario

Consultar y gestionar la asignación de sacerdotes (sacd) a zonas geográficas: listado por zona, cambio de zona propia, asignaciones iglesia/cgi y edición de días de atención semanal.

## Punto De Entrada

Menú Legacy: dre > zonas > zonas-sacd. Pills2: ATENCIÓN SACD > Gestión de zonas > Zonas-sacd.

## Escenarios

### Consultar sacd de una zona

1. Abrir Zonas-sacd desde el menú.
2. Elegir zona (o «sin asignar zona») en el desplegable → carga AJAX `zona_sacd_lista`.
3. Revisar tabla: sacd, zona, propia, días L–D.

### Cambiar asignación de zona (perm_des)

1. Marcar sacd en la tabla.
2. Elegir zona destino y pulsar «cambiar asignación zona» (`acumular=1`) o «añadir asignación iglesia/cgi» (`acumular=2`).
3. Validaciones cliente: zona destino y al menos un sacd marcado.

### Editar días de atención (perm_des)

1. Marcar un solo sacd → botón «modificar» → modal.
2. GET `/src/misas/zona_sacd_datos_get`; grabar con PUT `/src/misas/zona_sacd_datos_put`.

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
