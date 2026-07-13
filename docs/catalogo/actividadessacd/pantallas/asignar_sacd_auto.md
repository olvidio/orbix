---
id: "actividadessacd.pantalla.asignar_sacd_auto"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadessacd"
nombre: "Asignar Sacd Auto"
controller: "frontend/actividadessacd/controller/asignar_sacd_auto.php"
vistas: ["frontend/actividadessacd/view/asignar_sacd_auto.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadessacd/sacd_asignar_auto"]
capacidades: ["actividadessacd.sacd_asignar_auto.gestionar"]
campos: []
acciones: ["fnjs_asignar_sacd_auto", "fnjs_esc_asauto"]
estado_revision: "revisado"
---

# Asignar Sacd Auto

Pantalla auxiliar "Auto asignar sacd a actividades": muestra el criterio de asignación automática
(sacd titular del centro encargado a actividades sr/sg actuales posteriores al inicio de curso des)
y un botón **continuar** que dispara el endpoint `/src/actividadessacd/sacd_asignar_auto` y pinta el
resultado (`asignadas`, `sin_asignar`) sin recargar.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadessacd/controller/asignar_sacd_auto.php`

## Vistas Relacionadas

- `frontend/actividadessacd/view/asignar_sacd_auto.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadessacd/sacd_asignar_auto`

## Capacidades Relacionadas

- `actividadessacd.sacd_asignar_auto.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

- `fnjs_asignar_sacd_auto`
- `fnjs_esc_asauto`

## Manual De Usuario

1. Leer el texto que describe qué hará la asignación automática (sacd titular del centro a las
   actividades sr/sg con centro encargado, actuales y a partir del inicio de curso des).
2. Pulsar **continuar**: el sistema procesa y muestra cuántas actividades se han asignado y cuántas
   quedan sin asignar. En las asignadas automáticamente, el campo observaciones queda con `auto`.

## Ruta de menú

- Sin entrada de menú en el índice: pantalla auxiliar invocada desde la pantalla "Asignar sacd a
  actividades" (`activ_sacd`).
