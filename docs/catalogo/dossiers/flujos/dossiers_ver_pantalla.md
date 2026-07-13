---
id: "dossiers.dossiers_ver_pantalla.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "dossiers"
nombre: "Flujo - Gestionar Dossiers Ver Pantalla"
capacidad: "dossiers.dossiers_ver_pantalla.gestionar"
pantallas_principales: []
fragmentos: ["dossiers.pantalla.dossiers_ver"]
acciones: ["obtener_datos"]
endpoints: ["/src/dossiers/dossiers_ver_pantalla_data"]
estado_revision: "revisado"
---

# Flujo - Dossiers Ver Pantalla

## Objetivo De Usuario

Abrir y navegar los dossiers de una persona, actividad o ubi: cabecera, relación de carpetas o ficha con widgets embebidos (matrículas, asistentes, certificados, tablas genéricas). Reutilizado desde `home_persona`, `home_ubis`, `actividad_ver` y otras pantallas vía `fnjs_update_div`.

## Punto De Entrada

Sin entrada de menú directa; acceso embebido desde home persona/ubi, actividad u otras pantallas que enlazan `dossiers_ver.php`.

## Escenarios

### Abrir relación de dossiers

1. Desde el home de persona/ubi o la cabecera de actividad, pulsar el icono/enlace de dossiers.
2. El controller carga `dossiers_ver_pantalla_data` con `pau` + `id_pau` (y opcionalmente `obj_pau`).
3. Si `id_dossier` está vacío, se muestra la cabecera (`dossiers_ver_top`) y la tabla `lista_dossiers`.
4. Cada fila con permiso abre la ficha vía `fnjs_update_div` y `href_ver`.

### Ver o editar una ficha de dossier

1. Pulsar una carpeta con permiso de lectura/escritura.
2. El backend devuelve `modo=ficha` y `ficha_segmentos` (`select_*` o `datos_tabla`).
3. El frontend renderiza cada segmento (widgets de otros módulos o tabla genérica `DossiersVerFichaDatosTabla`).
4. La cabecera permite volver a la relación (`go_dossiers`) o al home del sujeto (`go_home`).

### Reutilización desde otras vistas (`queSel`)

1. Pantallas de asistentes, matrículas, cargos, etc. invocan `dossiers_ver` con `queSel`/`que` (`activ`, `matriculas`, `asis`, `asig`, `carg`).
2. El caso de uso fuerza `pau`, `permiso` e `id_dossier` según el contexto.

## Fragmentos O Pantallas Auxiliares

- `dossiers.pantalla.dossiers_ver`

## Endpoints Del Flujo

- `/src/dossiers/dossiers_ver_pantalla_data`

## Errores Conocidos

- `clase_info invalida`
- `No encuentro a nadie con id_nom: <id>`
- `ubi no encontrada`
- `actividad no encontrada`
- `pau desconocido`
- `El dossier <id> no está disponible (sin widget ni datos configurados en d_tipos_dossiers).`

## Ruta de menú

- **Legacy:** sin entrada de menú en el índice
- **Pills2:** sin entrada de menú en el índice
