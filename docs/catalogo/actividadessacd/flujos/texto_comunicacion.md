---
id: "actividadessacd.texto_comunicacion.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Texto Comunicacion"
capacidad: "actividadessacd.texto_comunicacion.gestionar"
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_txt"]
acciones: ["guardar", "obtener_datos"]
endpoints: ["/src/actividadessacd/texto_comunicacion_data", "/src/actividadessacd/texto_comunicacion_guardar"]
estado_revision: "revisado"
---

# Flujo - Gestionar Texto Comunicacion

Lectura y guardado de textos de comunicación a los sacd.

## Objetivo De Usuario

El usuario edita los textos de la carta de comunicación: elige clave (comunicación general o títulos
de columna) e idioma, carga el texto guardado, lo modifica y guarda. Guardar con el textarea vacío
elimina el texto de ese `{clave, idioma}`.

## Punto De Entrada

Fragmento `com_sacd_txt` (`frontend/actividadessacd/controller/com_sacd_txt.php`):
- `fnjs_get_texto` consulta `texto_comunicacion_data` al cambiar clave/idioma.
- `fnjs_guardar` invoca `texto_comunicacion_guardar` al pulsar **guardar**.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.com_sacd_txt`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Elegir **clave** e **idioma** en los desplegables.
2. El sistema recarga el textarea con el texto guardado.

Endpoints asociados:
- `/src/actividadessacd/texto_comunicacion_data`

### Guardar

Pasos:
1. Editar el texto en el textarea.
2. Pulsar **guardar** (o **cancelar** para volver sin guardar).
3. El sistema hace upsert o elimina si el texto queda vacío.

Endpoints asociados:
- `/src/actividadessacd/texto_comunicacion_guardar`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/texto_comunicacion_data`
- `/src/actividadessacd/texto_comunicacion_guardar`

## Errores Conocidos

- ``faltan parametros clave / idioma``
- ``hay un error, no se ha eliminado el texto``
- ``hay un error, no se ha guardado el texto``

## Ruta de menú

- Sin entrada de menú en el índice: fragmento invocado desde "Comunicación a los sacd"
  (`com_sacd_activ_periodo`) cuando el usuario tiene permiso de edición (`perm_mod_txt`).
