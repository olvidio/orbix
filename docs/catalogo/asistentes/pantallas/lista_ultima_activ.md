---
id: "asistentes.pantalla.lista_ultima_activ"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "asistentes"
nombre: "Lista Ultima Activ"
controller: "frontend/asistentes/controller/lista_ultima_activ.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/asistentes/lista_ultima_activ_data"]
capacidades: ["asistentes.lista_ultima_activ.gestionar"]
campos: []
acciones: []
estado_revision: "revisado"
---

# Lista Ultima Activ

Tabla de personas s pendientes de asistir según el informe (`que`) y centro elegido.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/asistentes/controller/lista_ultima_activ.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/asistentes/lista_ultima_activ_data`

## Capacidades Relacionadas

- `asistentes.lista_ultima_activ.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pantalla revisada contra `frontend/asistentes/`.
## Ruta de menú

- **Legacy:** Destino del submit de lista_ultim_que_ctr
- **Pills2:** vsg > crt/cv > informes de seguimiento
