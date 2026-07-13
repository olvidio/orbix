---
id: "actividadestudios.pantalla.e43_2_mpdf"
tipo: "pantalla_frontend"
subtipo: "pantalla"
modulo: "actividadestudios"
nombre: "E43 2 Mpdf"
controller: "frontend/actividadestudios/controller/e43_2_mpdf.php"
vistas: []
fragmentos_frontend: ["frontend/actividadestudios/controller/e43_imprimir_mpdf.php"]
endpoints: ["/src/actividadestudios/e43_imprimir_mpdf_data"]
capacidades: ["actividadestudios.e43_imprimir_mpdf.gestionar"]
campos: ["get.id_activ", "get.id_nom"]
acciones: []
estado_revision: "revisado"
---

# E43 2 Mpdf

Generador de descarga PDF del formulario E43. No tiene vista propia: captura el HTML de
`e43_imprimir_mpdf.php`, lo convierte con mPDF y fuerza la descarga del fichero `e43(nom).pdf`.

## Tipo

- Subtipo: `pantalla`
- Controller: `frontend/actividadestudios/controller/e43_2_mpdf.php`

## Vistas Relacionadas

No se han detectado vistas PHTML relacionadas.

## Fragmentos Frontend Relacionados

- `frontend/actividadestudios/controller/e43_imprimir_mpdf.php`

## Endpoints Usados

- `/src/actividadestudios/e43_imprimir_mpdf_data` (vía include de `e43_imprimir_mpdf.php`)

## Capacidades Relacionadas

- `actividadestudios.e43_imprimir_mpdf.gestionar`

## Campos Detectados

- `get.id_activ`
- `get.id_nom`

## Acciones Detectadas

No se han detectado acciones.

## Manual De Usuario

Pantalla de salida directa (sin UI interactiva):

1. Recibe `id_nom` e `id_activ` por GET (copiados a `$_POST` para la cadena de seguridad).
2. Incluye `e43_imprimir_mpdf.php`, que consulta `e43_imprimir_mpdf_data` y emite HTML con estilos
   `e43_mpdf.css.php`.
3. mPDF genera un PDF A4 vertical y lo envía al navegador con `Output(..., 'D')`.

Se invoca desde el enlace PDF de `e43.phtml`.

## Ruta de menú

sin entrada de menú en el índice (descarga PDF desde `e43`)
