# Criterios para la siguiente refactorizacion (misma linea que `profesores` lote 1)

Este documento resume el patron acordado para seguir migrando pantallas desde `apps/` hacia `frontend/` + `src/`, sin mezclar responsabilidades ni romper convivencia con URLs antiguas.

## Orden de trabajo

1. **Baseline breve** antes de tocar codigo: que pantalla, que parametros GET/POST, que salida HTML o JSON, casos `rstgr` / permisos si aplican. Anotarlo en `documentacion/` junto al modulo (por ejemplo `documentacion/<modulo>_migracion_baseline.md`).
2. **Separar capas primero**; dejar refactors finos (SRP, tests unitarios) para despues de que la pantalla ya viva en `frontend` + `src`.
3. **Un vertical slice por PR o por commit logico** (una pantalla o un flujo filtro+ajax), no mezclar varios modulos.

## Capas y responsabilidades

| Capa | Ruta / carpeta | Responsabilidad |
|------|----------------|-----------------|
| Backend API | `src/<modulo>/infrastructure/ui/http/controllers/*.php` | Solo orquestacion HTTP minima: leer input, llamar a `application`, responder con `ContestarJson::send`. **Sin** `echo` de HTML ni `Lista` aqui. |
| Caso de uso | `src/<modulo>/application/*.php` | Montar arrays de datos (`a_cabeceras`, `a_valores`, ids de tabla, etc.) usando repositorios/servicios del contenedor. Devolver datos listos para serializar con `ContestarJson::respuestaPhp($error, $data)`. |
| Rutas HTTP | `src/<modulo>/config/routes.php` | Registrar `/src/<modulo>/<nombre>` con GET y POST si hace falta (compatibilidad). |
| Frontend controlador | `frontend/<modulo>/controller/*.php` | `require_once("frontend/shared/global_header_front.inc")`, llamadas `PostRequest::getDataFromUrl('/src/...', $campos)`, construir `web\Lista` u otros componentes UI, pasar variables a la vista. |
| Frontend vista | `frontend/<modulo>/view/*.phtml` | Presentacion: HTML, scripts, `mostrar_tabla()`, sin consultas a BD ni contenedor. |
| Compatibilidad legacy | `apps/<modulo>/controller/*.php` | Opcional: un `require` al controlador `frontend` equivalente. Marcar en comentario que la URL `apps/...` esta **deprecada** para enlaces nuevos. |

## Patron de llamada backend desde frontend

Referencia: `frontend/usuarios/controller/usuario_lista.php`.

- URL backend: cadena que empiece por `/src/<modulo>/...` (sin host; `PostRequest` anade `ConfigGlobal::getWeb()`).
- Parametros: array asociativo; el hash de seguridad lo genera `PostRequest` internamente.
- Respuesta: `json_decode` del campo `data`; comprobar `error` en el array devuelto si se maneja sin `exit`.

## URLs canonicas y menus

- **Enlaces y menus nuevos:** siempre rutas bajo `frontend/.../controller/....php`.
- **Actualizar plantillas** donde existan (`documentacion/Documentacion_Obix/menus.csv`, `proves/aux_metamenus.csv`, seeds SQL si el repo los usa como referencia).
- **Bases ya en produccion:** si los menus estan en tablas con paths `apps/...`, planificar un UPDATE SQL acorde; el repo solo documenta el destino deseado.

## Validacion antes de dar por cerrado un slice

- `php -l` en todos los ficheros nuevos o tocados.
- Comparar salida relevante (ids de tabla, columnas, cardinalidad de filas) con el baseline.
- Probar al menos un caso con datos y uno vacio si aplica.
- Si la pantalla depende de ambito (`rstgr`, etc.), probar ambas ramas o documentar riesgo.

## Que evitar en esta fase

- No mover logica de negocio a `.phtml`.
- No hacer que `src` renderice HTML de aplicacion (salvo respuestas realmente API-only que ya sean JSON).
- No eliminar de golpe los wrappers `apps/` hasta que no queden referencias (grep en repo y, si aplica, datos en BD).

## Siguiente refactor sugerido en `profesores`

1. `lista_por_departamentos.php` (mismo patron: `application` + JSON + `frontend` + vista).
2. `ficha_profesor_stgr.php` / vistas asociadas (slice mas grande; posible division en sub-flujos).

Tras estabilizar capas, una **fase 2** puede extraer clases mas pequeñas en `application`, inyectar dependencias en lugar de `$GLOBALS['container']` en sitios calientes, y añadir tests sobre los casos de uso con dobles de repositorio.
