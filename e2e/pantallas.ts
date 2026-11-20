import { sqlDev } from './orbix-app';

/**
 * Qué pantallas de `frontend/` puede abrir un spec que las recorre todas (`pantallas-sin-parametros`,
 * `formularios-del-menu`) y cómo dejar las preferencias del usuario como estaban.
 */

/** Controladores que por su nombre ejecutan una acción (guardar, eliminar, enviar…): no se abren. */
export const PATRON_ACCION =
  /(update|guardar|eliminar|delete|borrar|nuevo|nueva|crear|aprobar|importar|exportar|enviar|resincronizar|aplicar|upload|subir|download|descargar|mover|copiar|duplicar|publicar|cambiar|marcar|asignar|quitar|add|del|insert|generar|grabar|recuperar|restablecer|login|session|logout|ejecutar|absorber|renombrar|trasladar|cerrar|abrir|anular|firmar|imprimir|sincro|calcular|actualizar|reset|bytea|mpdf|pdf)/i;

/** Módulos que no se abren: DDL sobre la BD y generación de ficheros de código. */
export const MODULOS_EXCLUIDOS = new Set(['devel_db_admin']);

/** Controladores concretos (`modulo/controller/fichero.php`) que no se abren, con el motivo. */
export const EXCLUIDOS: Record<string, string> = {
  'shared/controller/nav_atras.php': 'navegación: cambia la pila de la sesión',
  'shared/controller/nav_state.php': 'navegación: cambia la pila de la sesión',
  'shared/controller/src_ajax.php': 'proxy de /src: no es una pantalla',
  'actividadestudios/controller/matriculas_pendientes.php': 'borra las matrículas pendientes de personas que ya no están (duda 18)',
};

/** ¿Se puede abrir `modulo/controller/fichero.php` sin que haga nada? */
export function sePuedeAbrir(rel: string): boolean {
  const [modulo, , fichero] = rel.split('/');
  return fichero !== undefined && fichero.endsWith('.php') && !MODULOS_EXCLUIDOS.has(modulo) && !PATRON_ACCION.test(fichero) && !(rel in EXCLUIDOS);
}

/**
 * Varias pantallas guardan «la última búsqueda» como preferencia del usuario al abrirse: guarda las
 * preferencias del usuario de E2E (un usuario real de la BD de desarrollo), ejecuta `fn` y las repone.
 */
export async function conPreferenciasDelUsuario(fn: () => Promise<void>): Promise<void> {
  const T = `"${process.env.E2E_ESQUEMA ?? 'H-dlbv'}"`;
  const idUsuario = sqlDev('sv-e', `SELECT id_usuario FROM ${T}.aux_usuarios WHERE usuario = '${(process.env.E2E_USER ?? '').replace(/'/g, "''")}'`);
  if (!/^\d+$/.test(idUsuario)) {
    throw new Error('el usuario de E2E no está en aux_usuarios');
  }
  sqlDev('sv-e', `DROP TABLE IF EXISTS public.zz_e2e_preferencias; CREATE TABLE public.zz_e2e_preferencias AS SELECT * FROM ${T}.web_preferencias WHERE id_usuario = ${idUsuario}`);
  try {
    await fn();
  } finally {
    sqlDev('sv-e', `DELETE FROM ${T}.web_preferencias WHERE id_usuario = ${idUsuario}; INSERT INTO ${T}.web_preferencias SELECT * FROM public.zz_e2e_preferencias; DROP TABLE public.zz_e2e_preferencias`);
  }
}
