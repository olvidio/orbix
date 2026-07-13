---
id: "misas.ver_cuadricula_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Cuadricula Zona"
capacidad: "misas.ver_cuadricula_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.modificar_cuadricula_zona", "misas.pantalla.ver_cuadricula_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_cuadricula_zona_data"]
estado_revision: "revisado"
---

# Flujo - Ver cuadricula zona

## Objetivo De Usuario

Construye el SlickGrid de cuadrícula de zona (columnas, filas encargo/sacd, metadatos de celda) para ver/modificar plan, plantilla o cambiar estado.

## Punto De Entrada

Menú Legacy: dre > Misas > Modificar plan. Pills2: ATENCIÓN SACD > Gestión de misas > Modificar plan.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.modificar_cuadricula_zona`
- `misas.pantalla.ver_cuadricula_zona`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
