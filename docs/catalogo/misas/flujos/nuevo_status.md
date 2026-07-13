---
id: "misas.nuevo_status.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Nuevo Status"
capacidad: "misas.nuevo_status.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.cambiar_status"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/nuevo_status"]
estado_revision: "revisado"
---

# Flujo - Nuevo status

## Objetivo De Usuario

Actualiza masivamente el status de todos los EncargoDia de encargos 8100+ de una zona en el rango de fechas indicado.

## Punto De Entrada

Menú Legacy: dre > Misas > Cambiar estado. Pills2: dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.cambiar_status`

## Escenarios Inferidos

### Ejecutar

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

- `/src/misas/nuevo_status`

## Errores Conocidos

- `<repositorio getErrorTxt() acumulado>`

## Ruta de menú

- **Legacy:** dre > Misas > Cambiar estado
- **Pills2:** dre > ?110 > Cambiar estado<br>ATENCIÓN SACD > Gestión de misas > Cambiar estado
