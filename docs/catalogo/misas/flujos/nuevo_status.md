---
id: "misas.nuevo_status.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Aplicar un nuevo estado a un periodo"
capacidad: "misas.nuevo_status.gestionar"
pantallas_principales: ["misas.pantalla.cambiar_status"]
fragmentos: ["misas.pantalla.cambiar_status"]
acciones: ["ejecutar"]
endpoints: ["/src/misas/nuevo_status"]
estado_revision: "revisado"
---

# Flujo - Nuevo status

## Objetivo De Usuario

Aplicar el nuevo estado a todas las misas de una zona en un rango de fechas.

## Punto De Entrada

Menú **dre > Misas > Cambiar estado** (Pills2: ATENCIÓN SACD > Gestión de misas > Cambiar estado).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.cambiar_status`

## Escenarios Inferidos

### Aplicar un nuevo estado a un periodo

Pasos propuestos:
1. En **Cambiar estado**, elegir zona, periodo y **Nuevo estado**.
2. Pulsar **cambiar**. Los sacerdotes o centros pasan a ver el plan según el estado elegido.

Endpoints asociados:
- `/src/misas/nuevo_status`

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
