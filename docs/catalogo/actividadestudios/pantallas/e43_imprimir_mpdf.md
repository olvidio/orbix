---
id: "actividadestudios.pantalla.e43_imprimir_mpdf"
tipo: "pantalla_frontend"
subtipo: "fragmento_ajax"
modulo: "actividadestudios"
nombre: "E43 Imprimir Mpdf"
controller: "frontend/actividadestudios/controller/e43_imprimir_mpdf.php"
vistas: []
fragmentos_frontend: []
endpoints: ["/src/actividadestudios/e43_imprimir_mpdf_data"]
capacidades: ["actividadestudios.e43_imprimir_mpdf.gestionar"]
campos: ["get.id_activ", "get.id_nom"]
acciones: []
estado_revision: "revisado"
---

# E43 Imprimir Mpdf

Fragmento HTML del formulario E43 preparado para impresión/PDF. No es una pantalla autónoma: lo
incluye `e43_2_mpdf.php` mediante `ob_start`/`include`.

## Tipo

- Subtipo: `fragmento_ajax`
- Controller: `frontend/actividadestudios/controller/e43_imprimir_mpdf.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas (el HTML se genera en el controller).

## Fragmentos Frontend Relacionados

No se han detectado controladores frontend relacionados.

## Endpoints Usados

- `/src/actividadestudios/e43_imprimir_mpdf_data`

## Capacidades Relacionadas

- `actividadestudios.e43_imprimir_mpdf.gestionar`

## Campos Detectados

- `get.id_activ`
- `get.id_nom`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Tras arrancar sesión (`FrontBootstrap`), carga los datos del E43 con `e43_imprimir_mpdf_data` y
pinta la misma estructura que `e43.phtml` (cabecera DL, datos personales, tabla de asignaturas y
notas pie), con estilos de impresión embebidos. El resultado no se muestra al usuario: lo consume
mPDF en `e43_2_mpdf.php`.

## Ruta de menú

sin entrada de menú en el índice (include interno de `e43_2_mpdf`)
