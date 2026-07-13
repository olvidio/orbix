---
id: "procesos.pantalla.tipo_activ_proceso"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "procesos"
nombre: "Tipo Activ Proceso"
controller: "frontend/procesos/controller/tipo_activ_proceso.php"
vistas: ["frontend/procesos/view/tipo_activ_proceso.html.twig"]
fragmentos_frontend: ["frontend/procesos/controller/tipo_activ_proceso_lista.php", "frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php"]
endpoints: ["/src/procesos/tipo_activ_proceso_asignar", "/src/procesos/tipo_activ_proceso_lst_posibles"]
capacidades: ["procesos.tipo_activ_proceso_asignar.gestionar", "procesos.tipo_activ_proceso_lst_posibles.gestionar"]
campos: []
acciones: []
estado_revision: "revisado"
---

# Tipo Activ Proceso

Asignación de procesos a tipos de actividad: tabla con proceso propio (DL) y no propio; al pulsar un proceso se despliega la mini-tabla de procesos posibles para asignar.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/procesos/controller/tipo_activ_proceso.php`

## Vistas Relacionadas

- `frontend/procesos/view/tipo_activ_proceso.html.twig`

## Fragmentos Frontend Relacionados

- `frontend/procesos/controller/tipo_activ_proceso_lista.php`
- `frontend/procesos/controller/tipo_activ_proceso_lst_posibles.php`

## Endpoints Usados

- `/src/procesos/tipo_activ_proceso_asignar`
- `/src/procesos/tipo_activ_proceso_lst_posibles`

## Capacidades Relacionadas

- `procesos.tipo_activ_proceso_asignar.gestionar`
- `procesos.tipo_activ_proceso_lst_posibles.gestionar`

## Campos Detectados

No se han detectado campos de formulario.

## Acciones Detectadas

No se han detectado acciones.

## Ruta de menú

- **Legacy:** sistema > procesos activ. > tipo activ - proceso
- **Pills2:** ADMIN LOCAL > procesos activ. > tipo activ - proceso
