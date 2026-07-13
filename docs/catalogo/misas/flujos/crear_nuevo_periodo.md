---
id: "misas.crear_nuevo_periodo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Crear Nuevo Periodo"
capacidad: "misas.crear_nuevo_periodo.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.crear_nuevo_periodo"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/crear_nuevo_periodo_data"]
estado_revision: "revisado"
---

# Flujo - Crear nuevo periodo

## Objetivo De Usuario

Crea asignaciones EncargoDia para un nuevo periodo de plan de misas a partir de plantilla y devuelve el payload de cuadrícula para renderizar ver_cuadricula_zona.phtml.

## Punto De Entrada

Menú Legacy: dre > Misas >  Nuevo plan. Pills2: ATENCIÓN SACD > Gestión de misas > Nuevo plan.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.crear_nuevo_periodo`

## Escenarios Inferidos

### Obtener Datos

Pasos propuestos:
1. Revisar manualmente los pasos de esta accion.

Endpoints asociados:
- Ninguno inferido para esta accion.

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
