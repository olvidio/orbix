---
id: "misas.ver_misas_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "misas"
nombre: "Flujo - Gestionar Ver Misas Zona"
capacidad: "misas.ver_misas_zona.gestionar"
pantallas_principales: []
fragmentos: ["misas.pantalla.ver_misas_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/misas/ver_misas_zona_data"]
estado_revision: "revisado"
---

# Flujo - Ver misas zona

## Objetivo De Usuario

Construye la cuadrícula de consulta de misas por zona y rango de fechas (solo lectura, con metadatos dia/tipo en celdas).

## Punto De Entrada

Sin entrada de menú directa; fragmento o modal invocado desde pantalla padre.

## Fragmentos O Pantallas Auxiliares

- `misas.pantalla.ver_misas_zona`

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
