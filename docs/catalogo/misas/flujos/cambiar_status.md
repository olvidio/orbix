---
id: "misas.cambiar_status.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Cambiar Status"
capacidad: "misas.cambiar_status.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.cambiar_status"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/cambiar_status_data"]
estado_revision: "revisado"
---

# Flujo - Cambiar status

## Objetivo De Usuario

Carga los desplegables de la pantalla cambiar estado del plan de misas: zonas permitidas, criterios de orden y estados posibles.

## Punto De Entrada

Menú Legacy: dre > Misas > Cambiar estado. Pills2: dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.cambiar_status`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.empiezamax`
- `form.empiezamin`
- `form.estado`
- `form.id_zona`
- `form.orden`
- `form.periodo`
- `form.tipo_plantilla`
- `html.cambiar`

Acciones JavaScript:
- `button:cambiar`
- `fnjs_nuevo_estado`
- `fnjs_ver_cuadricula_zona`

## Endpoints Del Flujo

- `/src/misas/cambiar_status_data`

## Errores Conocidos

- `Usuario no encontrado`
- `No tiene permiso para ver esta página`

## Ruta de menú

- **Legacy:** dre > Misas > Cambiar estado
- **Pills2:** dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado
