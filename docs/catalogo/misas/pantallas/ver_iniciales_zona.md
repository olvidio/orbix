---
id: "misas.pantalla.ver_iniciales_zona"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "misas"
nombre: "Ver Iniciales Zona"
controller: "frontend/misas/controller/ver_iniciales_zona.php"
vistas: ["frontend/misas/view/ver_iniciales_zona.phtml"]
fragmentos_frontend: []
endpoints: ["/src/misas/update_iniciales", "/src/misas/ver_iniciales_zona_data"]
capacidades: ["misas.update_iniciales.gestionar", "misas.ver_iniciales_zona.gestionar"]
campos: ["form.color", "form.id_sacd", "form.iniciales", "post.id_zona"]
acciones: ["fnjs_generarNomEnc"]
estado_revision: "revisado"
---

# Ver iniciales zona

Fragmento SlickGrid con sacds de la zona; edición inline que postea a `update_iniciales`.

## Tipo

- Subtipo: `fragmento_ajax`


- Controller: `frontend/misas/controller/ver_iniciales_zona.php`

## Vistas Relacionadas

- `frontend/misas/view/ver_iniciales_zona.phtml`

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/misas/update_iniciales`
- `/src/misas/ver_iniciales_zona_data`

## Capacidades Relacionadas

- `misas.update_iniciales.gestionar`
- `misas.ver_iniciales_zona.gestionar`

## Campos Detectados

- `form.color`
- `form.id_sacd`
- `form.iniciales`
- `post.id_zona`

## Acciones Detectadas

- `fnjs_generarNomEnc`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
