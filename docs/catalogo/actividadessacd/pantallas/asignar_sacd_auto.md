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
estado_revision: "generado"
---

# Asignar Sacd Auto

Pantalla auxiliar "Auto asignar sacd a actividades".

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

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
