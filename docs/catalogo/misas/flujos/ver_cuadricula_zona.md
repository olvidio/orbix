---
id: "misas.ver_cuadricula_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Ver y modificar el plan de una zona"
capacidad: "misas.ver_cuadricula_zona.gestionar"
pantallas_principales: ["misas.pantalla.ver_cuadricula_zona"]
fragmentos: ["misas.pantalla.modificar_cuadricula_zona", "misas.pantalla.ver_cuadricula_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_cuadricula_zona_data"]
estado_revision: "revisado"
---

# Flujo - Ver cuadricula zona

## Objetivo De Usuario

Mostrar la cuadrícula del plan de misas de una zona (encargos en filas, días en columnas, sacerdotes en las celdas) para consultarla o modificarla.

## Punto De Entrada

Menú **dre > Misas > Modificar plan / Ver plan zona** (Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plan, Ver plan zona).

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_cuadricula_zona`
- `misas.pantalla.ver_cuadricula_zona`

## Escenarios Inferidos

### Ver y modificar el plan de una zona

Pasos propuestos:
1. Entrar en **Modificar plan** (o **Ver plan zona** para solo consulta).
2. Elegir la zona, el **Orden** y el **Periodo** (esta semana, este mes, próxima semana, próximo mes u otro) y pulsar buscar.
3. Aparece la cuadrícula con las misas del periodo.
4. Al elegir la zona en **Nuevo plan** o **Modificar plan** ya aparece la cuadrícula del periodo (una fila por sacerdote, una columna por día; se ven SI, NO o -- según el día). En modificar, pulsar una celda para elegir sacerdote; si no hay datos aparece «No hay datos para mostrar en la cuadrícula».

Endpoints asociados:
- `/src/misas/ver_cuadricula_zona_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `html.grupos_sacd`
- `post.columna`
- `post.empiezamax`
- `post.empiezamin`
- `post.fila`
- `post.id_zona`
- `post.orden`
- `post.periodo`
- `post.seleccion`
- `post.tipo_plantilla`

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/misas/ver_cuadricula_zona_data`

## Errores Conocidos

- `hay un error, no se ha guardado`
- `sólo debería haber uno`

## Ruta de menú

- **Legacy:** dre > Misas > Modificar plan
- **Pills2:** ATENCIÓN SACD > Gestión de misas > Modificar plan
