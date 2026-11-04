---
id: "encargossacd.sacd_ausencias_jefe_zona.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "encargossacd"
nombre: "Flujo - Anotar ausencias de los sacerdotes de la zona"
capacidad: "encargossacd.sacd_ausencias_jefe_zona.gestionar"
pantallas_principales: ["encargossacd.pantalla.sacd_ausencias_jefe_zona"]
fragmentos: ["encargossacd.pantalla.sacd_ausencias_jefe_zona"]
acciones: ["obtener_datos"]
endpoints: ["/src/encargossacd/sacd_ausencias_jefe_zona_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Ausencias Jefe Zona

Propuesta generada automaticamente desde la capacidad `encargossacd.sacd_ausencias_jefe_zona.gestionar` y sus pantallas relacionadas.

## Objetivo De Usuario

Que el jefe de zona anote las ausencias de los sacerdotes de su zona.

## Punto De Entrada

Menú **exterior > sacd > Misas > Ausencias**.

## Fragmentos O Pantallas Auxiliares

- `encargossacd.pantalla.sacd_ausencias_jefe_zona`

## Escenarios Inferidos

### Anotar ausencias de los sacerdotes de la zona

Pasos propuestos:
1. Entrar en **Ausencias**.
2. Elegir el **sacd** de la lista (los de la zona del jefe; el oficial de la dl ve todos).
3. Rellenar sus ausencias como en «Anotar una ausencia».

Endpoints asociados:
- `/src/encargossacd/sacd_ausencias_jefe_zona_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- `form.filtro_sacd`
- `form.historial`
- `form.id_nom`

Acciones JavaScript:
- `fnjs_horario`
- `fnjs_lista_sacd`
- `fnjs_ver_ficha`

## Endpoints Del Flujo

- `/src/encargossacd/sacd_ausencias_jefe_zona_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** exterior > sacd > Misas > Ausencias
- **Pills2:** sin entrada de menú en el índice


## Ruta de menú

- **Legacy:** exterior > sacd > Misas > Ausencias
- **Pills2:** sin entrada de menú en el índice

