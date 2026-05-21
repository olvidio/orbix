---
id: "inventario.pantalla.equipajes_imprimir"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "inventario"
nombre: "Equipajes Imprimir"
controller: "frontend/inventario/controller/equipajes_imprimir.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/cabecera_pie_txt", "/src/inventario/equipajes_doc_casa", "/src/inventario/equipajes_egm", "/src/inventario/equipajes_lista_activ_equipaje"]
capacidades: ["inventario.cabecera_pie_txt.gestionar", "inventario.equipajes_doc_casa.gestionar", "inventario.equipajes_egm.gestionar", "inventario.equipajes_lista_activ_equipaje.gestionar"]
campos: ["post.id_equipaje"]
acciones: ["fnjs_left_side_hide", "fnjs_mod_texto_equipaje"]
estado_revision: "generado"
---

# Equipajes Imprimir

Descripcion funcional pendiente de revisar.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_imprimir.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/cabecera_pie_txt`
- `/src/inventario/equipajes_doc_casa`
- `/src/inventario/equipajes_egm`
- `/src/inventario/equipajes_lista_activ_equipaje`

## Capacidades Relacionadas

- `inventario.cabecera_pie_txt.gestionar`
- `inventario.equipajes_doc_casa.gestionar`
- `inventario.equipajes_egm.gestionar`
- `inventario.equipajes_lista_activ_equipaje.gestionar`

## Campos Detectados

- `post.id_equipaje`

## Acciones Detectadas

- `fnjs_left_side_hide`
- `fnjs_mod_texto_equipaje`

## Manual De Usuario

Pendiente de redactar: objetivo de la pantalla, pasos habituales, validaciones y errores comunes.

## Revision Manual

- Confirmar si es pantalla principal o fragmento AJAX.
- Completar nombre funcional orientado a usuario.
- Revisar campos obligatorios y significado de cada accion.
- Confirmar si las capacidades relacionadas son correctas.
