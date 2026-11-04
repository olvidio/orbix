---
id: "misas.ver_misas_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Consultar el plan de misas de una zona"
capacidad: "misas.ver_misas_zona.gestionar"
pantallas_principales: ["misas.pantalla.ver_misas_zona"]
fragmentos: ["misas.pantalla.ver_misas_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_misas_zona_data"]
estado_revision: "revisado"
---

# Flujo - Ver misas zona

## Objetivo De Usuario

Consultar las misas de una zona en un rango de fechas, sin poder modificarlas.

## Punto De Entrada

Menú **dre > Misas > Ver plan zona** (Pills2: ATENCIÓN SACD > Gestión de misas > Ver plan zona).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_misas_zona`

## Escenarios Inferidos

### Consultar el plan de misas de una zona

Pasos propuestos:
1. Entrar en **Ver plan zona**.
2. Elegir la zona en **Plan de Misas de la zona**, el **Orden** y el **Periodo** (o fechas concretas con «otro»).
3. Se muestra la cuadrícula de consulta; no se puede cambiar nada.
4. Leyenda de la fila de cada sacerdote (por día): **SI** = el sacerdote está en la zona ese día; **NO** = no está en la zona ese día; **--** = ese día está en otro sitio (al pasar el ratón sale «Está en …»). El fondo rojo avisa de un problema: «Tiene dos Misas a primera hora» o «No está en la zona y tiene Misa a primera hora».

Endpoints asociados:
- `/src/misas/ver_misas_zona_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.id_zona`
- `post.seleccion`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/ver_misas_zona_data`

## Errores Conocidos

- `solo deberia haber uno`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
