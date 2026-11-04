---
id: "inventario.equipajes_nuevo.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "inventario"
nombre: "Flujo - Crear un equipaje nuevo"
capacidad: "inventario.equipajes_nuevo.gestionar"
pantallas_principales: ["inventario.pantalla.equipajes_nuevo"]
fragmentos: []
acciones: ["guardar"]
endpoints: ["/src/inventario/equipajes_nuevo_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Equipajes Nuevo

## Objetivo De Usuario

Crear un equipaje: la documentación que se lleva a una casa para las actividades de unas fechas.

## Punto De Entrada

Menú **Inventario > equipajes > nuevo equipaje**.

## Fragmentos O Pantallas Auxiliares

No se han detectado fragmentos AJAX relacionados.

## Escenarios Inferidos

### Crear un equipaje nuevo

Pasos propuestos:
1. Abrir **nuevo equipaje**.
2. En **periodo de selección de actividades** elegir el periodo (**todo el año**, un trimestre, **curso ca**, **curso crt** u **otro**), el año, o las fechas **entre … y …**.
3. Pulsar **ver las casas donde se realizan las actividades**.
4. Elegir la casa en el desplegable que aparece; se muestran las actividades de esa casa en el periodo.
5. Marcar las actividades que van en el mismo equipaje y pulsar **seleccionar**.
6. El **nombre del equipaje** viene propuesto como «casa (fechas)»; se puede cambiar. Pulsar **guardar**.
7. La pantalla pasa a **hacer equipajes** con el equipaje ya creado.

Endpoints asociados:
- `/src/inventario/equipajes_nuevo_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/inventario/equipajes_nuevo_guardar`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- **Legacy:** scdl > Inventario > equipajes > nuevo equipaje
- **Pills2:** —
