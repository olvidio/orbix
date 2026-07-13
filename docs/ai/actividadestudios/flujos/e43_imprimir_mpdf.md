---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "actividadestudios"
titulo: "E43 Imprimir Mpdf"
flujo: "actividadestudios.e43_imprimir_mpdf.gestionar.flujo"
preguntas: ["Como obtener datos en E43 Imprimir Mpdf?"]
pantallas_principales: []
fragmentos: ["actividadestudios.pantalla.e43_imprimir_mpdf"]
endpoints: ["/src/actividadestudios/e43_imprimir_mpdf_data"]
source: "docs/catalogo/actividadestudios/flujos/e43_imprimir_mpdf.md"
estado_revision: "generado"
---

# Ayuda IA - E43 Imprimir Mpdf

Usa este documento para responder preguntas de usuario sobre como trabajar con `E43 Imprimir Mpdf`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como obtener datos en E43 Imprimir Mpdf?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Obtener datos

1. En la pantalla E43, pulsar **imprimir** (abre ventana con `e43_2_mpdf.php`).
2. El controlador consulta `e43_imprimir_mpdf_data`.
3. Se renderiza el certificado con estilos `e43_mpdf.css` listo para imprimir/exportar.

Referencias tecnicas para verificar la respuesta:
- `/src/actividadestudios/e43_imprimir_mpdf_data`

## Pantallas Y Fragmentos Relacionados

- `actividadestudios.pantalla.e43_imprimir_mpdf`

## Objetivo

El usuario imprime el certificado E43 en formato PDF: el sistema obtiene los mismos datos que la pantalla E43 y los renderiza en la plantilla imprimible (`e43_imprimir_mpdf.php` / `e43_2_mpdf.php`).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
