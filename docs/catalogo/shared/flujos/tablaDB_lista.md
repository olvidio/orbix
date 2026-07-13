---
id: "shared.tablaDB_lista.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "shared"
nombre: "Flujo - Listar y mantener tabla genérica"
capacidad: "shared.tablaDB_lista.gestionar"
pantallas_principales: ["shared.pantalla.tablaDB_lista_ver"]
fragmentos: ["shared.pantalla.tablaDB_formulario_ver"]
acciones: ["buscar", "listar", "nuevo", "editar", "eliminar"]
endpoints: ["/src/shared/tablaDB_buscar_datos", "/src/shared/tablaDB_lista_datos", "/src/shared/tablaDB_update"]
estado_revision: "revisado"
---

# Flujo - Listar y mantener tabla genérica

## Objetivo De Usuario

Consultar y mantener registros de tablas de configuración enlazadas desde el menú (asignaturas,
ubis, inventario, procesos, etc.) mediante el shell común `tablaDB`.

## Punto De Entrada

`frontend/shared/controller/tablaDB_lista_ver.php` con `clase_info` según la entrada de menú.

## Escenarios

### Buscar y listar

1. Abrir desde menú (pasa `clase_info` y contexto `pau`/`id_pau` si aplica).
2. Si el `Info*` define criterios, rellenar búsqueda → `tablaDB_buscar_datos`.
3. Enviar → `tablaDB_lista_datos` y tabla con ordenación múltiple.

### Alta / edición

1. «Nuevo» o seleccionar fila → `tablaDB_formulario_ver` (`mod=nuevo`/`editar`).
2. Guardar → `tablaDB_update` → volver al listado.

### Borrado

1. Marcar fila(s) y eliminar (`mod=eliminar`, `sel[]` o `s_pkey`).
2. `tablaDB_update` → refresco de listado.

## Errores Conocidos

- Errores de `tablaDB_update` (ver ficha API).
- Sin errores propios en builders de lista.

## Ruta de menú

Variante según `clase_info` — ver `docs/guias/_referencia_menus.md` (entradas `tablaDB_lista_ver.php`).
Ejemplos: `global > estudios > asignaturas`, `sistema > menus > meta menus`, `dre > zonas > zonas`.
