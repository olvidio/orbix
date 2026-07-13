---
id: "casas.casas_resumen.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "casas"
nombre: "Flujo - Gestionar Casas Resumen"
capacidad: "casas.casas_resumen.gestionar"
pantallas_principales: []
fragmentos: ["casas.pantalla.casas_resumen_lista"]
acciones: ["obtener_datos"]
endpoints: ["/src/casas/casas_resumen_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Casas Resumen

Propuesta generada automaticamente desde la capacidad `casas.casas_resumen.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Gestiona CasasResumen. Use case: resumen económico de casas (dias ocupados, asistentes previstos/reales, ingresos, gastos, aportaciones, superávit). Sucesor de apps/casas/controller/casas_resumen_ajax.php. Dos modos: - que='' → un único periodo (año/trimestre/rango) por casa. - que!='' → estadística por año (5 años) por casa.

## Punto De Entrada

No se ha detectado pantalla principal. Revisar si el flujo solo aparece como fragmento o desde otra pantalla.

## Fragmentos O Pantallas Auxiliares

- `casas.pantalla.casas_resumen_lista`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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

