---
id: "usuarios.preferencia_tabla.gestionar"
tipo: "capacidad"
modulo: "usuarios"
nombre: "Gestionar Preferencia Tabla"
entidades: ["PreferenciaTabla"]
acciones: ["obtener"]
endpoints: ["/src/usuarios/preferencia_tabla_get"]
pantallas: ["frontend/shared/web/Lista.php", "frontend/shared/web/TablaEditable.php"]
casos_uso: ["src\\usuarios\\application\\PreferenciaTablaData"]
tags: ["get", "preferencia", "preferencia_tabla", "tabla", "usuarios"]
estado_revision: "generado"
---

# Gestionar Preferencia Tabla

Propuesta generada automaticamente a partir de endpoints con prefijo comun `preferencia_tabla`.

## Objetivo Funcional

Gestiona PreferenciaTabla. Devuelve las preferencias de usuario necesarias para renderizar una tabla (HTML simple o SlickGrid) en el front. Entrada: - id_tabla (opcional): identificador del grid. Si viene vacío, no se devolverán preferencias específicas del grid (útil cuando sólo se necesita saber si el usuario prefiere HTML o SlickGrid). Salida: array asociativo con la forma: [ 'formato_tabla' => ''|'html'|'slickgrid', // prefs 'tabla_presentacion' 'slickgrid' => null|array, // prefs 'slickGrid_<id_tabla>_<idioma>' ] Para slickgrid se busca primero la preferencia del usuario actual; si no existe, se usa la del usuario 44 (default).

## Acciones Detectadas

- `obtener`

## Endpoints

- `/src/usuarios/preferencia_tabla_get`

## Pantallas Relacionadas

- `frontend/shared/web/Lista.php`
- `frontend/shared/web/TablaEditable.php`

## Casos De Uso Detectados

- `src\usuarios\application\PreferenciaTablaData`

## Pistas Desde Endpoints

- Devuelve las preferencias de usuario necesarias para renderizar una tabla (HTML simple o SlickGrid) en el front. Entrada: - `id_tabla` (opcional): identificador del grid. Si viene vacío, no se devolverán preferencias específicas del grid (útil cuando sólo se necesita saber si el usuario prefiere HTML o SlickGrid). Salida: array asociativo con la forma: [ 'formato_tabla' => ''|'html'|'slickgrid', // prefs 'tabla_presentacion' 'slickgrid' => null|array, // prefs 'slickGrid_<id_tabla>_<idioma>' ] Para slickgrid se busca primero la preferencia del usuario actual; si no existe, se usa la del usuario 44 (default).

## Errores Conocidos

No se han agregado errores desde el catalogo API.

## Revision Manual

- Confirmar si todos los endpoints pertenecen a la misma capacidad.
- Separar esta capacidad si mezcla procesos distintos.
- Marcar capacidades parecidas o duplicadas en otros modulos.
- Completar permisos, efectos sobre datos y ejemplos de uso.
