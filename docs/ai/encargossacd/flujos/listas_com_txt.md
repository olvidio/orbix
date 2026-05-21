---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Listas Com Txt"
flujo: "encargossacd.listas_com_txt.gestionar.flujo"
preguntas: ["Como crear o modificar en Listas Com Txt?", "Como obtener en Listas Com Txt?", "Como obtener datos en Listas Com Txt?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.listas_com_txt", "encargossacd.pantalla.listas_com_txt_get", "encargossacd.pantalla.listas_com_txt_update"]
endpoints: ["/src/encargossacd/listas_com_txt_data", "/src/encargossacd/listas_com_txt_get", "/src/encargossacd/listas_com_txt_update"]
source: "docs/catalogo/encargossacd/flujos/listas_com_txt.md"
estado_revision: "generado"
---

# Ayuda IA - Listas Com Txt

Usa este documento para responder preguntas de usuario sobre como trabajar con `Listas Com Txt`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Listas Com Txt?
- Como obtener en Listas Com Txt?
- Como obtener datos en Listas Com Txt?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear o modificar

1. Abrir el formulario de alta o modificacion.
2. Rellenar o corregir los campos requeridos.
3. Guardar los cambios.
4. Comprobar que la pantalla vuelve al listado y refleja el cambio.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/listas_com_txt_update`

## Obtener

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.listas_com_txt`
- `encargossacd.pantalla.listas_com_txt_get`
- `encargossacd.pantalla.listas_com_txt_update`

## Objetivo

Gestiona ListasComTxt, ListasComTxtGet. Datos para la pantalla de textos de comunicacion (frontend/encargossacd/controller/listas_com_txt.php). Devuelve las opciones de idiomas configurados y el texto inicial correspondiente a la clave/idioma por defecto (com_sacd / es). Lectura del texto de comunicacion para un par (clave, idioma). Extraido de EncargoTextoListasComAjax (rama que=get_texto) para eliminar el dispatcher multiproposito (criterio refactor.md). Mutacion del texto de comunicacion para un par (clave, idioma). Si el texto llega vacio, se elimina la fila. Extraido de EncargoTextoListasComAjax (rama que=update) para eliminar el dispatcher multiproposito (criterio refactor.md).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
