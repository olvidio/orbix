---
id: "actividadessacd.locales_desplegable.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Locales Desplegable"
capacidad: "actividadessacd.locales_desplegable.gestionar"
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_txt"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/locales_desplegable_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Locales Desplegable

Poblado del desplegable de idiomas en la edición de textos.

## Objetivo De Usuario

Al abrir el fragmento de edición de textos, el sistema carga la lista de idiomas/locales disponibles
para poblar el desplegable `idioma` del formulario de comunicación.

## Punto De Entrada

Fragmento `com_sacd_txt` (`frontend/actividadessacd/controller/com_sacd_txt.php`): el controller
consulta este endpoint al renderizar el fragmento para construir el desplegable de idiomas.

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.com_sacd_txt`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Abrir el fragmento de edición de textos desde la pantalla de comunicación.
2. El sistema rellena el desplegable de idiomas con `a_locales`.

Endpoints asociados:
- `/src/actividadessacd/locales_desplegable_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/locales_desplegable_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

- Sin entrada de menú en el índice: fragmento invocado desde "Comunicación a los sacd"
  (`com_sacd_activ_periodo`) cuando el usuario tiene permiso de edición (`perm_mod_txt`).
