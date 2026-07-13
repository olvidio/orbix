---
id: "actividadestudios.e43_imprimir_mpdf.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Gestionar E43 Imprimir Mpdf"
capacidad: "actividadestudios.e43_imprimir_mpdf.gestionar"
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.e43_imprimir_mpdf"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/e43_imprimir_mpdf_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar E43 Imprimir Mpdf

Generación del HTML/PDF imprimible del certificado E43.

## Objetivo De Usuario

El usuario imprime el certificado E43 en formato PDF: el sistema obtiene los mismos datos
que la pantalla E43 y los renderiza en la plantilla imprimible (`e43_imprimir_mpdf.php` /
`e43_2_mpdf.php`).

## Punto De Entrada

Pantalla `e43_imprimir_mpdf` (`frontend/actividadestudios/controller/e43_imprimir_mpdf.php`,
incluida desde `e43_2_mpdf.php`): al abrir la ventana de impresión desde `e43.phtml`
llama a `e43_imprimir_mpdf_data` con `id_nom` e `id_activ` por GET.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.e43_imprimir_mpdf`
- `actividadestudios.pantalla.e43` (botón imprimir)

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. En la pantalla E43, pulsar **imprimir** (abre ventana con `e43_2_mpdf.php`).
2. El controlador consulta `e43_imprimir_mpdf_data`.
3. Se renderiza el certificado con estilos `e43_mpdf.css` listo para imprimir/exportar.

Endpoints asociados:
- `/src/actividadestudios/e43_imprimir_mpdf_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado (parámetros GET `id_nom`, `id_activ`).

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadestudios/e43_imprimir_mpdf_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Sin entrada de menú en el índice (subflujo desde pantalla `e43`).

- **Legacy:** vsm > ca > buscar ca (misma cadena que E43).
- **Pills2:** ACTIVIDADES > Buscar actividad > ca n.
