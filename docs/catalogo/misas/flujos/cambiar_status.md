---
id: "misas.cambiar_status.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Cambiar el estado del plan"
capacidad: "misas.cambiar_status.gestionar"
pantallas_principales: ["misas.pantalla.cambiar_status"]
fragmentos: ["misas.pantalla.cambiar_status"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/cambiar_status_data"]
estado_revision: "revisado"
---

# Flujo - Cambiar status

## Objetivo De Usuario

Cambiar el estado del plan de misas de una zona y periodo: propuesta, visible para sacerdotes o visible para centros.

## Punto De Entrada

Menú **dre > Misas > Cambiar estado** (Pills2: ATENCIÓN SACD > Gestión de misas > Cambiar estado).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.cambiar_status`

## Escenarios Inferidos

### Cambiar el estado del plan

Pasos propuestos:
1. Entrar en **Cambiar estado**.
2. Elegir la zona en **Plan de Misas Zona**, el **Orden** y el **Periodo**.
3. En **Nuevo estado** elegir propuesta, visible sacd o visible ctr.
4. Pulsar **cambiar**: se aplica a todas las misas de la zona en ese periodo. No muestra ningún mensaje de confirmación; para comprobarlo hay que volver a abrir el plan.

Endpoints asociados:
- `/src/misas/cambiar_status_data`

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
