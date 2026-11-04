---
id: "misas.crear_nuevo_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Preparar un nuevo plan de misas"
capacidad: "misas.crear_nuevo_periodo.gestionar"
pantallas_principales: ["misas.pantalla.crear_nuevo_periodo"]
fragmentos: ["misas.pantalla.crear_nuevo_periodo"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/crear_nuevo_periodo_data"]
estado_revision: "revisado"
---

# Flujo - Crear nuevo periodo

## Objetivo De Usuario

Crear el plan de misas de un periodo nuevo a partir de la plantilla y mostrarlo en la cuadrícula.

## Punto De Entrada

Menú **dre > Misas > Nuevo plan** (Pills2: ATENCIÓN SACD > Gestión de misas > Nuevo plan).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.crear_nuevo_periodo`

## Escenarios Inferidos

### Preparar un nuevo plan de misas

Pasos propuestos:
1. Entrar en **Nuevo plan** (Preparar Plan de Misas).
2. Elegir la zona en **Plan de Misas Zona**, el **Tipo de plantilla** (semanal, semanal y domingos o mensual, con una o tres opciones: la plantilla propone 1 o 3 sacerdotes para cada misa, por orden de prioridad; se elige el primero y solo se pasa al segundo si el primero no está o celebra en un centro con más prioridad), el **Orden** y el **Periodo** (próxima semana, próximo mes natural u otro, con fechas).
3. Pulsar **preparar**. Aviso: «Borrará todos los datos introducidos en este periodo!!».
4. Se muestra la cuadrícula del plan, rellenada desde la plantilla, lista para modificar: al pulsar una celda se abre la ventana **Selección de sacd** (ver «Elegir sacerdote en una celda»).

Endpoints asociados:
- `/src/misas/crear_nuevo_periodo_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `post.empiezamax`
- `post.empiezamin`
- `post.id_zona`
- `post.orden`
- `post.periodo`
- `post.seleccion`
- `post.tipoplantilla`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/crear_nuevo_periodo_data`

## Errores Conocidos

- `solo deberia haber uno`
- `<repositorio getErrorTxt() acumulado en error_txt>`

## Ruta de menú

- **Legacy:** dre > Misas >  Nuevo plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Nuevo plan
