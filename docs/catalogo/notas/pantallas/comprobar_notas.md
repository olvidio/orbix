---
id: "notas.pantalla.comprobar_notas"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "notas"
nombre: "Comprobar Notas"
controller: "frontend/notas/controller/comprobar_notas.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/notas/comprobar_notas_page_data"]
capacidades: ["notas.comprobar_notas_page.gestionar"]
campos: []
acciones: []
estado_revision: "generado"
---

# Comprobar Notas

Pantalla “comprobar notas”: el SQL y mutaciones corren en {@see src/notas/infrastructure/ui/http/controllers/comprobar_notas_page_data.php}.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/notas/controller/comprobar_notas.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/notas/comprobar_notas_page_data`

## Capacidades Relacionadas

- `notas.comprobar_notas_page.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
