---
id: "inventario.pantalla.equipajes_desplegable"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Desplegable equipajes"
controller: "frontend/inventario/controller/equipajes_desplegable.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_equipajes_desde_fecha"]
capacidades: ["inventario.lista_equipajes_desde_fecha.gestionar"]
campos: ["post.eliminar", "post.filtro", "post.imprimir"]
acciones: ["fnjs_ver_1", "fnjs_ver_2"]
estado_revision: "revisado"
---

# Desplegable equipajes

Opciones equipajes desde fecha.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_desplegable.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_equipajes_desde_fecha`

## Capacidades Relacionadas

- `inventario.lista_equipajes_desde_fecha.gestionar`

## Campos Detectados

- `post.eliminar`
- `post.filtro`
- `post.imprimir`

## Acciones Detectadas

- `fnjs_ver_1`
- `fnjs_ver_2`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Opciones equipajes desde fecha.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
