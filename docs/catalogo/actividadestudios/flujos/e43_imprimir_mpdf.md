---
id: "actividadestudios.e43_imprimir_mpdf.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadestudios"
nombre: "Flujo - Imprimir el certificado E43"
capacidad: "actividadestudios.e43_imprimir_mpdf.gestionar"
pantallas_principales: ["actividadestudios.pantalla.e43_imprimir_mpdf"]
fragmentos: ["actividadestudios.pantalla.e43_imprimir_mpdf"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadestudios/e43_imprimir_mpdf_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar E43 Imprimir Mpdf

Generación del HTML/PDF imprimible del certificado E43.

## Objetivo De Usuario

Imprimir el certificado E43 de un alumno en PDF, con los mismos datos que se ven en pantalla.

## Punto De Entrada

Botón **imprimir** de la pantalla del certificado E43.

## Fragmentos O Pantallas Auxiliares

- `actividadestudios.pantalla.e43_imprimir_mpdf`
- `actividadestudios.pantalla.e43` (botón imprimir)

## Escenarios Inferidos

### Imprimir el certificado E43

Pasos propuestos:
1. En la pantalla del certificado E43, pulsar **imprimir**.
2. Se abre una ventana con el certificado listo para imprimir o guardar como PDF.

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
