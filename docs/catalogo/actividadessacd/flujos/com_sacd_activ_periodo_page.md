---
id: "actividadessacd.com_sacd_activ_periodo_page.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "actividadessacd"
nombre: "Flujo - Gestionar Com Sacd Activ Periodo Page"
capacidad: "actividadessacd.com_sacd_activ_periodo_page.gestionar"
pantallas_principales: []
fragmentos: ["actividadessacd.pantalla.com_sacd_activ_periodo"]
acciones: ["obtener_datos"]
endpoints: ["/src/actividadessacd/com_sacd_activ_periodo_page_data"]
estado_revision: "revisado"
---

# Flujo - Gestionar Com Sacd Activ Periodo Page

Configuración inicial de la pantalla de comunicación a los sacd.

## Objetivo De Usuario

Al cargar la pantalla de comunicación, el sistema determina si el usuario puede editar los textos
base (`perm_mod_txt`). Los usuarios con rol `p-sacd` no tienen permiso de edición.

## Punto De Entrada

Pantalla `com_sacd_activ_periodo` (`frontend/actividadessacd/controller/com_sacd_activ_periodo.php`):
el controller consulta este endpoint al renderizar la página para decidir si muestra el enlace al
fragmento de edición de textos (`com_sacd_txt`).

## Fragmentos O Pantallas Auxiliares

- `actividadessacd.pantalla.com_sacd_activ_periodo`

## Escenarios Inferidos

### Obtener Datos

Pasos:
1. Abrir la pantalla de comunicación a los sacd.
2. El sistema resuelve `perm_mod_txt` según el rol del usuario.
3. Si hay permiso, se muestra el enlace para editar textos.

Endpoints asociados:
- `/src/actividadessacd/com_sacd_activ_periodo_page_data`

## Campos Y Acciones Detectadas En Pantalla

Campos:
- Ninguno detectado.

Acciones JavaScript:
- Ninguna detectada.

## Endpoints Del Flujo

- `/src/actividadessacd/com_sacd_activ_periodo_page_data`

## Errores Conocidos

No se han documentado errores en la capacidad.

## Ruta de menú

Se accede desde la pantalla `com_sacd_activ_periodo`:

- **Legacy:** dre > actividades > comunic. sacd · exterior > sacd > atención actividades
- **Pills2:** ATENCIÓN SACD > Actividades > Comunicación a los sacd

Con `propuesta=true`: dre > propuestas > lista activ. sacd.
