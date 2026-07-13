---
id: "asistentes.pantalla.lista_est_ctr"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Lista Est Ctr"
controller: "frontend/asistentes/controller/lista_est_ctr.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/asistentes/lista_est_ctr_data"]
capacidades: ["asistentes.lista_est_ctr.gestionar"]
campos: ["post.empiezamax", "post.empiezamin", "post.id_ubi", "post.n_agd", "post.periodo", "post.year"]
acciones: []
estado_revision: "revisado"
---

# Lista Est Ctr

Resultado del filtro `que_ctr_lista` (`lista=list_est`): estudios/asignaturas por centro.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/lista_est_ctr.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/lista_est_ctr_data`

## Capacidades Relacionadas

- `asistentes.lista_est_ctr.gestionar`

## Campos Detectados

- `post.empiezamax`
- `post.empiezamin`
- `post.id_ubi`
- `post.n_agd`
- `post.periodo`
- `post.year`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- **Legacy:** vest/dagd > estudios x ctr
- **Pills2:** ACTIVIDADES > Listados > Mejores ca para n/agd
