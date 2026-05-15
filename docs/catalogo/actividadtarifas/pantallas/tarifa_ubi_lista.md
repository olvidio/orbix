---
id: "actividadtarifas.pantalla.tarifa_ubi_lista"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadtarifas"
nombre: "Tarifa Ubi Lista"
controller: "frontend/actividadtarifas/controller/tarifa_ubi_lista.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/actividadtarifas/tarifa_ubi_lista_data"]
capacidades: ["actividadtarifas.tarifa_ubi.gestionar"]
campos: ["post.id_ubi", "post.year"]
acciones: ["fnjs_copiar_tarifas", "fnjs_modificar"]
estado_revision: "generado"
---

# Tarifa Ubi Lista

Controlador AJAX HTML: listado de `TarifaUbi` por casa y año.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadtarifas/controller/tarifa_ubi_lista.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadtarifas/tarifa_ubi_lista_data`

## Capacidades Relacionadas

- `actividadtarifas.tarifa_ubi.gestionar`

## Campos Detectados

- `post.id_ubi`
- `post.year`

## Acciones Detectadas

- `fnjs_copiar_tarifas`
- `fnjs_modificar`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
