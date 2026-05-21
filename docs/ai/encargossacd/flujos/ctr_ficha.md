---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Ctr Ficha"
flujo: "encargossacd.ctr_ficha.gestionar.flujo"
preguntas: ["Como crear o modificar en Ctr Ficha?", "Como obtener datos en Ctr Ficha?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.ctr_ficha", "encargossacd.pantalla.ctr_ficha_update"]
endpoints: ["/src/encargossacd/ctr_ficha_data", "/src/encargossacd/ctr_ficha_update"]
source: "docs/catalogo/encargossacd/flujos/ctr_ficha.md"
estado_revision: "generado"
---

# Ayuda IA - Ctr Ficha

Usa este documento para responder preguntas de usuario sobre como trabajar con `Ctr Ficha`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear o modificar en Ctr Ficha?
- Como obtener datos en Ctr Ficha?

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
- `/src/encargossacd/ctr_ficha_update`

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.ctr_ficha`
- `encargossacd.pantalla.ctr_ficha_update`

## Objetivo

Gestiona CtrFicha. Datos de la pantalla ctr_ficha: - calcula el filtro_ctr efectivo a partir del centro (cuando no viene del POST) - devuelve las opciones_seccion para el desplegable de grupo de ctrs. Reemplaza la lectura directa de repos y el acceso a EncargoAplicacionService que el frontend hacia en ctr_ficha.php. Mutacion de la ficha de atencion sacerdotal de un centro. Puerto de frontend/encargossacd/controller/ctr_ficha_update.php. Devuelve siempre ['error' => string] (vacio = exito). El controlador HTTP convierte ese resultado en JSON {success, mensaje} (el proxy legacy en frontend/ preserva el contrato "alert(rta_txt)" reemitiendo mensaje).

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
