---
id: "configuracion.periodo_calendario_escolar.gestionar.flujo"
tipo: "flujo_frontend"
modulo: "configuracion"
nombre: "Flujo - Periodo calendario escolar (interno)"
capacidad: "configuracion.periodo_calendario_escolar.gestionar"
pantallas_principales: []
fragmentos: []
acciones: ["obtener_datos"]
endpoints: ["/src/configuracion/periodo_calendario_escolar_data"]
estado_revision: "revisado"
---

# Flujo - Periodo calendario escolar (interno)

## Objetivo De Usuario

No hay pantalla de usuario: el frontend obtiene fechas de inicio/fin de curso STGR y CRT
(caché en sesión o BD) para que `Periodo` calcule rangos de fechas en listados y filtros
de calendario.

## Punto De Entrada

Consumo programático desde `frontend/shared/web/Periodo.php`:
`Periodo::conCalendarioDesdeBackend()` → POST a `periodo_calendario_escolar_data`.

Los valores editables por el administrador están en la pantalla **config esquema**
(`curso_stgr`, `curso_crt`); este endpoint solo los lee (vía `ConfigSnapshot` / sesión).

## Escenarios

### Obtener calendario

1. Si `$_SESSION['oConfig']` ya es `ConfigSnapshot`, no llama al backend.
2. Si no, una petición POST devuelve `dia_ini_stgr`, `mes_ini_stgr`, `dia_fin_stgr`,
   `mes_fin_stgr`, equivalentes CRT y `any_final_est` / `any_final_crt`.
3. Resultado cacheado estáticamente en la petición PHP.

## Errores Conocidos

Errores de `PostRequest` propagados como `RuntimeException` si `$throwOnError=true`; en modo
legacy pueden provocar `exit` con mensaje de error.

## Ruta de menú

Sin entrada de menú en el índice (flujo técnico transversal; configuración en «config esquema»).
