---
id: "casas.casas_resumen.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Consultar la estadística económica de las casas"
capacidad: "casas.casas_resumen.gestionar"
pantallas_principales: ["casas.pantalla.casas_resumen"]
fragmentos: ["casas.pantalla.casas_resumen_lista"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/casas_resumen_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Casas Resumen

Propuesta generada automaticamente desde la capacidad `casas.casas_resumen.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Ver el resumen económico de las casas: días ocupados, asistentes previstos y reales, ingresos, gastos, aportaciones y superávit.

## Punto De Entrada

Menú **adl > Gestión casas > estadística por casas** (Pills2: CASAS Y CTR > Gestión casas).

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casas_resumen_lista`

## Escenarios Inferidos

### Consultar la estadística económica de las casas

Pasos propuestos:
1. Entrar en **estadística por casas**.
2. Elegir las casas y el periodo (un año, trimestre o varios periodos).
3. Se muestra la tabla de resumen por casa.

Endpoints asociados:
- `/src/casas/casas_resumen_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.cdc_sel`
- `post.empiezamax`
- `post.empiezamin`
- `post.id_cdc`
- `post.periodo`
- `post.que`
- `post.year`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/casas/casas_resumen_data`

## Errores Conocidos

No se han documentado errores en la capacidad.
## Ruta de menú

- **Legacy:** adl > Gestión casas > estadística  por casas
- **Pills2:** CASAS Y CTR > Gestión casas > estadística  por casas

