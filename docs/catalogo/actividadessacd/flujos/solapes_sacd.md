---
id: "actividadessacd.solapes_sacd.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Solapes Sacd"
capacidad: "actividadessacd.solapes_sacd.gestionar"
pantallas_principales: ["actividadessacd.pantalla.activ_sacd"]
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/solapes_sacd_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Solapes Sacd

Listado de sacd con actividades incompatibles en el periodo.

## Objetivo De Usuario

Con el tipo de menú `solape`, el usuario elige un periodo y pulsa **buscar**: el sistema muestra los
sacd que tienen actividades incompatibles (solapes horarios) y, para cada uno, las actividades
afectadas.

## Punto De Entrada

Pantalla `activ_sacd` (`frontend/actividadessacd/controller/activ_sacd.php`): cuando el parámetro
`tipo=solape`, la función `fnjs_ver` llama a este endpoint en lugar de `lista_actividades_sacd_data`.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.activ_sacd`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Entrar desde el menú con tipo `solape`.
2. Elegir periodo y pulsar **buscar**.
3. El sistema construye la tabla de sacd con sus actividades incompatibles y la leyenda de colores.

Endpoints asociados:
- `/src/actividadessacd/solapes_sacd_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/solapes_sacd_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `activ_sacd` con `tipo=solape` (sin entrada dedicada en el índice; se
abre desde la misma entrada "Asignar sacd" con el tipo correspondiente):

- **Legacy:** dre > propuestas > asignar sacd
- **Pills2:** ATENCIÓN SACD > Actividades > Asignar sacd a actividades
