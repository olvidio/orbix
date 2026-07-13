---
id: "actividadplazas.pantalla.incorporar_peticion"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadplazas"
nombre: "Incorporar Peticion"
controller: "frontend/actividadplazas/controller/incorporar_peticion.php"
vistas: ["frontend/actividadplazas/view/incorporar_peticion.phtml"]
fragmentos_frontend: []
endpoints: ["/src/actividadplazas/peticiones_incorporar"]
capacidades: ["actividadplazas.peticiones_incorporar.gestionar"]
campos: ["form.sactividad", "form.sasistentes", "post.sactividad", "post.sasistentes"]
acciones: ["fnjs_incorporar_peticiones", "fnjs_left_side_hide"]
estado_revision: "revisado"
---

# Incorporar Peticion

Pantalla que dispara la incorporación de las primeras peticiones de plaza como asistencia (acción
contra `/src/actividadplazas/peticiones_incorporar`).

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadplazas/controller/incorporar_peticion.php`

## Vistas Relacionadas

- `frontend/actividadplazas/view/incorporar_peticion.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadplazas/peticiones_incorporar`

## Capacidades Relacionadas

- `actividadplazas.peticiones_incorporar.gestionar`

## Campos Detectados

- `form.sactividad`
- `form.sasistentes`
- `post.sactividad`
- `post.sasistentes`

## Acciones Detectadas

- `fnjs_incorporar_peticiones`
- `fnjs_left_side_hide`

## Manual De Usuario

Pantalla con un texto explicativo y un botón **Continuar** (`fnjs_incorporar_peticiones`) que lanza el
proceso contra `peticiones_incorporar`. Al terminar muestra en `#resultado` cuántas peticiones se
incorporaron y el aviso de que no se incorporan personas que ya tienen una actividad propia en el
periodo. El botón se deshabilita mientras se ejecuta para evitar dobles envíos.

## Ruta de menú

- **Legacy:** vsm > ca > Incorporar 1ª petición (y variantes por perfil/tipo: dagd, crt…)
- **Pills2:** ACTIVIDADES > Gestión de plazas y peticiones > Incorporar 1ª petición > ca n (y variantes por tipo/colectivo)
