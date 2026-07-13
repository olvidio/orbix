---
id: "inventario.pantalla.equipajes_casas_posibles"
tipo: "pantalla_frontend"
subtipo: "fragmento"
modulo: "inventario"
nombre: "Casas posibles"
controller: "frontend/inventario/controller/equipajes_casas_posibles.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/inventario/lista_casas_posibles_periodo"]
capacidades: ["inventario.lista_casas_posibles_periodo.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.fin", "post.inicio", "post.periodo", "post.year"]
acciones: ["fnjs_ver_actividades_casa"]
estado_revision: "revisado"
---

# Casas posibles

Desplegable casas en periodo.


## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/inventario/controller/equipajes_casas_posibles.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/inventario/lista_casas_posibles_periodo`

## Capacidades Relacionadas

- `inventario.lista_casas_posibles_periodo.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.fin`
- `post.inicio`
- `post.periodo`
- `post.year`

## Acciones Detectadas

- `fnjs_ver_actividades_casa`

## Manual De Usuario

Ver [`manual/inventario.md`](../../../manual/inventario.md). Desplegable casas en periodo.

## Ruta de menú

- **Legacy:** sin entrada de menú
- **Pills2:** —
