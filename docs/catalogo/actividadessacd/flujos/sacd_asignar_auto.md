---
id: "actividadessacd.sacd_asignar_auto.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Sacd Asignar Auto"
capacidad: "actividadessacd.sacd_asignar_auto.gestionar"
pantallas_principales: ["actividadessacd.pantalla.asignar_sacd_auto"]
fragmentos: []
acciones: ["ejecutar"]
endpoints: ["/src/actividadessacd/sacd_asignar_auto"]
estado_revision: "revisado"
---

# Flujo - Gestionar Sacd Asignar Auto

Auto-asignación masiva del sacd titular del centro encargado.

## Objetivo De Usuario

El usuario confirma la asignación automática: el sistema asigna el sacd titular del centro encargado
a las actividades sr/sg actuales posteriores al inicio de curso des que aún no tienen sacd. Devuelve
cuántas se han asignado y cuántas quedan sin asignar; las asignadas quedan con observaciones `auto`.

## Punto De Entrada

Pantalla `asignar_sacd_auto` (`frontend/actividadessacd/controller/asignar_sacd_auto.php`): la
función `fnjs_asignar_sacd_auto` llama a este endpoint al pulsar **continuar**.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.asignar_sacd_auto`

## Escenarios Inferidos

### Ejecutar

Pasos:
1. Leer el texto que describe el criterio de asignación automática.
2. Pulsar **continuar**.
3. El sistema procesa y muestra el resultado (`asignadas`, `sin_asignar`) sin recargar la página.

Endpoints asociados:
- `/src/actividadessacd/sacd_asignar_auto`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/sacd_asignar_auto`

## Errores Conocidos

No se han documentado errores en la capacidad (fallos de guardado se cuentan como `sin_asignar`).

## Ruta de menú

- Sin entrada de menú en el índice: pantalla auxiliar invocada desde "Asignar sacd a actividades"
  (`activ_sacd`).
