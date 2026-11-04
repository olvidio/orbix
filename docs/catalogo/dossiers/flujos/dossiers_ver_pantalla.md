---
id: "dossiers.dossiers_ver_pantalla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Abrir un dossier"
capacidad: "dossiers.dossiers_ver_pantalla.gestionar"
pantallas_principales: ["dossiers.pantalla.dossiers_ver"]
fragmentos: ["dossiers.pantalla.dossiers_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
estado_revision: "revisado"
---

# Flujo - Dossiers Ver Pantalla

## Objetivo De Usuario

Abrir y recorrer los dossiers de una persona, actividad o centro/casa: cabecera, relación de carpetas y el contenido de cada una (matrículas, asistentes, certificados, tablas).

## Punto De Entrada

Ficha de persona, ficha de actividad o ficha de centro/casa.

## Escenarios Inferidos

### Abrir un dossier

Pasos propuestos:
1. Desde la ficha, pulsar dossiers y luego la carpeta que interesa.
2. Se muestra su contenido; según el permiso se puede solo consultar o también modificar.
3. Usar los enlaces de la cabecera para volver a la relación de dossiers o a la ficha de origen.

Endpoints asociados:
- `/src/dossiers/dossiers_ver_pantalla_data`

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.dossiers_ver`

## Endpoints Del Flujo

- `/src/dossiers/dossiers_ver_pantalla_data`

## Errores Conocidos

- `clase_info invalida`
- `No encuentro a nadie con id_nom: <id>`
- `ubi no encontrada`
- `actividad no encontrada`
- `pau desconocido`
- `El dossier <id> no está disponible (sin widget ni datos configurados en d_tipos_dossiers).`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
