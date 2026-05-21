---
id: "configuracion.pantalla.parametros"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "configuracion"
nombre: "Parametros"
controller: "frontend/configuracion/controller/parametros.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/configuracion/parametros_lista"]
capacidades: ["configuracion.parametros.gestionar"]
campos: ["form.fin_dia", "form.fin_mes", "form.ini_dia", "form.ini_mes", "form.valor"]
acciones: []
estado_revision: "generado"
---

# Parametros

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/configuracion/controller/parametros.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/configuracion/parametros_lista`

## Capacidades Relacionadas

- `configuracion.parametros.gestionar`

## Campos Detectados

- `form.fin_dia`
- `form.fin_mes`
- `form.ini_dia`
- `form.ini_mes`
- `form.valor`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
