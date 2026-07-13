---
id: "zonassacd.pantalla.zona_sacd_lista_ajax"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "zonassacd"
nombre: "Zona Sacd Lista Ajax"
controller: "frontend/zonassacd/controller/zona_sacd_lista_ajax.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/zonassacd/zona_sacd_lista"]
capacidades: ["zonassacd.zona_sacd.gestionar"]
campos: ["post.id_zona"]
acciones: []
estado_revision: "revisado"
---

# Zona Sacd Lista Ajax

Fragmento AJAX: tabla sacd (`id_zona`) desde zona_sacd, o listado global si `que=get_lista_tot` (menú Lista sacd-zona).

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/zonassacd/controller/zona_sacd_lista_ajax.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/zonassacd/zona_sacd_lista`

## Capacidades Relacionadas

- `zonassacd.zona_sacd.gestionar`

## Campos Detectados

- `post.id_zona`

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** dre > zonas > lista sacd-zona
- **Pills2:** ATENCIÓN SACD > Gestión de zonas > Lista sacd-zona
