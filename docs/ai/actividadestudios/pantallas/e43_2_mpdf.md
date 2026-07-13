---
tipo: "ayuda_ia"
subtipo: "pantalla"
modulo: "actividadestudios"
titulo: "E43 2 Mpdf"
pantalla: "actividadestudios.pantalla.e43_2_mpdf"
preguntas: ["Que se puede hacer en E43 2 Mpdf?", "Que campos tiene E43 2 Mpdf?", "Que acciones hay en E43 2 Mpdf?"]
capacidades: ["actividadestudios.e43_imprimir_mpdf.gestionar"]
endpoints: ["/src/actividadestudios/e43_imprimir_mpdf_data"]
source: "docs/catalogo/actividadestudios/pantallas/e43_2_mpdf.md"
estado_revision: "generado"
---

# Ayuda IA Pantalla - E43 2 Mpdf

## Resumen

Generador de descarga PDF del formulario E43. No tiene vista propia: captura el HTML de `e43_imprimir_mpdf.php`, lo convierte con mPDF y fuerza la descarga del fichero `e43(nom).pdf`.

## Uso En Ayuda

Usar esta ficha cuando el usuario pregunte por una pantalla concreta, sus campos o sus acciones.

## Campos Detectados

- `get.id_activ`
- `get.id_nom`

## Acciones Detectadas

- No hay acciones detectadas.

## Capacidades Relacionadas

- `actividadestudios.e43_imprimir_mpdf.gestionar`

## Endpoints Relacionados

- `/src/actividadestudios/e43_imprimir_mpdf_data`

## Precauciones

- Esta ficha puede contener nombres tecnicos. Para respuesta final, convertirlos a lenguaje de usuario cuando sea posible.
