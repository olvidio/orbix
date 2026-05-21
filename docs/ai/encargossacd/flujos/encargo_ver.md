---
tipo: "ayuda_ia"
subtipo: "flujo"
modulo: "encargossacd"
titulo: "Encargo Ver"
flujo: "encargossacd.encargo_ver.gestionar.flujo"
preguntas: ["Como crear en Encargo Ver?", "Como eliminar en Encargo Ver?", "Como obtener datos en Encargo Ver?"]
pantallas_principales: []
fragmentos: ["encargossacd.pantalla.encargo_select", "encargossacd.pantalla.encargo_ver"]
endpoints: ["/src/encargossacd/encargo_ver_data", "/src/encargossacd/encargo_ver_eliminar", "/src/encargossacd/encargo_ver_nuevo"]
source: "docs/catalogo/encargossacd/flujos/encargo_ver.md"
estado_revision: "generado"
---

# Ayuda IA - Encargo Ver

Usa este documento para responder preguntas de usuario sobre como trabajar con `Encargo Ver`.

## Cuando Usar Esta Ayuda

Responder con esta ayuda cuando el usuario pregunte por:
- Como crear en Encargo Ver?
- Como eliminar en Encargo Ver?
- Como obtener datos en Encargo Ver?

## Donde Entrar

- Pantalla pendiente de revisar.

## Como Responder

Da pasos cortos y orientados a usuario. Si falta ruta de menu, dilo como pendiente de documentar.

## Crear

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Eliminar

1. Seleccionar o abrir el registro que se quiere eliminar.
2. Pulsar la accion de eliminar.
3. Confirmar la operacion si aparece dialogo de confirmacion.
4. Comprobar que el registro desaparece del listado.

Referencias tecnicas para verificar la respuesta:
- `/src/encargossacd/encargo_ver_eliminar`

## Obtener datos

1. Revisar manualmente los pasos de esta accion.

Referencias tecnicas para verificar la respuesta:
- Ninguna referencia API inferida.

## Pantallas Y Fragmentos Relacionados

- `encargossacd.pantalla.encargo_select`
- `encargossacd.pantalla.encargo_ver`

## Objetivo

Gestiona EncargoVer. Alta de encargo desde el formulario de encargo_ver (antes encargo_ajax.php que=nuevo). Borrado desde lista encargo_select (antes encargo_ajax.php que=eliminar). Datos para la pantalla encargo_ver (nuevo / editar encargo). El frontend arma los frontend\shared\web\Desplegable a partir de los arrays devueltos.

## Limites De La Respuesta

- No inventar permisos si no estan documentados.
- No inventar rutas de menu si aparecen como pendientes.
- Si el usuario pregunta por errores concretos, responder que estan pendientes salvo que el catalogo los documente.
