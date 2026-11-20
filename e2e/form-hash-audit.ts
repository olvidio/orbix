import fs from 'node:fs';
import path from 'node:path';
import type { Page } from '@playwright/test';

/**
 * Auditoría de los hashes de formulario (`HashF::getCamposHtml()`), fase 6.4 del plan de tests.
 *
 * Un formulario firmado lleva `h`: el hash de **los nombres** de sus campos (los de `setCamposForm()`
 * más los hidden), sin valores. Al recibirlo, `HashF::validatePost()` vuelve a calcularlo con los
 * nombres que llegan en el POST; si llega un campo de más o de menos, la petición se rechaza (302 al
 * inicio y sesión cerrada) sin ningún mensaje. `tools/audit/audit_hash_campos.php` no cubre estos
 * formularios porque los campos no están en el código de la vista sino en el HTML que resulta.
 *
 * Aquí se hace con el HTML ya pintado: se toman los campos que enviaría el formulario tal como está
 * (lo mismo que `$(form).serialize()`), se les aplica lo que hace `validatePost()` y se compara el
 * hash con `h`. El hash lleva el id de la sesión, que el spec conoce por la cookie.
 *
 * Límites: no ve los campos que el JavaScript añade o quita al enviar (se le pueden indicar con
 * `anadidosPorJs`), ni los valores de los hidden que cambia (eso lo comprueba `hh`), ni sabe si un
 * formulario se envía entero o la vista monta los parámetros por su cuenta.
 */

/** Lo que se lee de un `<form>` firmado. */
export interface FormularioFirmado {
  /** Para localizarlo: id, name o action. */
  nombre: string;
  h: string;
  hno: string;
  hchk: string;
  /** `false` si el formulario está oculto en la pantalla (solo se sabe en una página cargada). */
  visible: boolean;
  /** Nombres que se enviarían ahora (como `serialize()`), ya como claves de `$_POST`. */
  enviados: string[];
  /**
   * Controles con nombre que ahora no se enviarían pero podrían: desplegables sin opciones (los
   * rellena una petición posterior), grupos de radio sin marcar y casillas sin marcar.
   */
  opcionales: { nombre: string; tipo: 'select-vacio' | 'radio' | 'checkbox' }[];
}

export interface ResultadoFormulario {
  nombre: string;
  /** `ok`: coincide tal cual. `condicional`: solo coincide si se rellenan los controles de `con`. `falla`: no coincide. */
  estado: 'ok' | 'condicional' | 'falla';
  /** Controles opcionales que hay que enviar para que coincida (estado `condicional`). */
  con: string[];
  enviados: string[];
  opcionales: string[];
  /** Estado `falla`: qué campos habría que quitar o añadir para que coincida, si se ha encontrado. */
  diagnostico?: string;
}

/** Campos que `validatePost()` / `ordenarArrayParam()` descartan antes de calcular el hash. */
const META = new Set(['PHPSESSID', 'atras', 'h', 'hc', 'horig', 'hh', 'hhc', 'hhorig', 'hno', 'hchk', 'hnov', 'id_sel', 'scroll_id', 'nav']);

/**
 * La lista de campos que `validatePost()` firmaría para un POST con esos nombres, en el mismo formato que
 * publica el formulario en `data-hc` del campo `h` (`a=&b=`). La firma en sí no se puede recalcular aquí:
 * es un HMAC con una clave que solo tiene el servidor. `idSesion` ya no interviene.
 */
export function hashDeCampos(nombres: Iterable<string>, hno: string, hchk: string, _idSesion: string): string {
  const fuera = new Set(hno.split('!').filter((c) => c !== ''));
  const claves = new Set<string>();
  for (const n of nombres) {
    if (!fuera.has(n) && !META.has(n) && !n.startsWith('scroll_id_')) {
      claves.add(n);
    }
  }
  for (const c of hchk.split('!')) {
    if (c !== '') {
      claves.add(c);
    }
  }
  // `ksort` + `http_build_query` con todos los valores vacíos + `rawurldecode`.
  return [...claves].sort((a, b) => (a < b ? -1 : a > b ? 1 : 0)).map((c) => `${c}=`).join('&');
}

/** Función que se ejecuta en el navegador: lee los formularios firmados de un documento. */
function leerFormularios(raiz: Document | Element): FormularioFirmado[] {
  // Clave de `$_POST` de un control: PHP corta en el primer `[` y cambia `.` y espacio por `_`.
  const clave = (name: string): string => name.replace(/\[.*$/, '').replace(/[. ]/g, '_');
  const out: FormularioFirmado[] = [];
  raiz.querySelectorAll('form').forEach((form) => {
    const valor = (n: string): string => (form.querySelector(`input[name="${n}"]`) as HTMLInputElement | null)?.value ?? '';
    if (form.querySelector('input[name="h"]') === null || form.querySelector('input[name="hh"]') === null) {
      return;
    }
    const enviados = new Set<string>();
    const posibles = new Map<string, 'select-vacio' | 'radio' | 'checkbox'>();
    Array.from(form.elements).forEach((el) => {
      const c = el as HTMLInputElement | HTMLSelectElement | HTMLTextAreaElement;
      if (!c.name || c.disabled || c.closest('fieldset[disabled]') !== null) {
        return;
      }
      const tipo = (c.getAttribute('type') ?? c.tagName).toLowerCase();
      if (['submit', 'button', 'image', 'reset', 'file'].includes(tipo) || c.tagName === 'BUTTON' || c.tagName === 'FIELDSET' || c.tagName === 'OBJECT') {
        return;
      }
      const k = clave(c.name);
      if (tipo === 'checkbox' || tipo === 'radio') {
        if ((c as HTMLInputElement).checked) {
          enviados.add(k);
        } else {
          posibles.set(k, tipo);
        }
      } else if (c.tagName === 'SELECT') {
        const s = c as HTMLSelectElement;
        if (s.multiple ? s.selectedOptions.length > 0 : s.options.length > 0) {
          enviados.add(k);
        } else {
          posibles.set(k, 'select-vacio');
        }
      } else {
        enviados.add(k);
      }
    });
    out.push({
      nombre: form.id || form.getAttribute('name') || form.getAttribute('action') || '(sin nombre)',
      visible: form.ownerDocument.defaultView === null || form.getClientRects().length > 0,
      // No la firma, sino la lista de campos firmados que el servidor publica junto a ella.
      h: (form.querySelector('input[name="h"]') as HTMLInputElement | null)?.dataset.hc ?? '',
      hno: valor('hno'),
      hchk: valor('hchk'),
      enviados: [...enviados],
      opcionales: [...posibles].filter(([k]) => !enviados.has(k)).map(([nombre, tipo]) => ({ nombre, tipo })),
    });
  });
  return out;
}

/** Formularios firmados de un HTML (sin ejecutar sus scripts). */
export async function formulariosDeHtml(page: Page, html: string): Promise<FormularioFirmado[]> {
  return page.evaluate(
    ([fn, texto]) => {
      const doc = new DOMParser().parseFromString(texto, 'text/html');
      // eslint-disable-next-line no-new-func -- la función viaja como texto al navegador
      return new Function(`return (${fn})(arguments[0])`)(doc);
    },
    [leerFormularios.toString(), html] as const,
  ) as Promise<FormularioFirmado[]>;
}

/** Formularios firmados de la pantalla cargada (por defecto, dentro de `#main`). */
export async function formulariosDePagina(page: Page, selector = '#main'): Promise<FormularioFirmado[]> {
  return page.evaluate(
    ([fn, sel]) => {
      const raiz = document.querySelector(sel);
      // eslint-disable-next-line no-new-func -- la función viaja como texto al navegador
      return raiz === null ? [] : new Function(`return (${fn})(arguments[0])`)(raiz);
    },
    [leerFormularios.toString(), selector] as const,
  ) as Promise<FormularioFirmado[]>;
}

/** Id de la sesión PHP del contexto del navegador. */
export async function idSesion(page: Page): Promise<string> {
  const cookie = (await page.context().cookies()).find((c) => c.name === 'PHPSESSID');
  return cookie?.value ?? '';
}

/** Compara cada formulario con su `h`. */
export function comprobarFormularios(formularios: FormularioFirmado[], sesion: string, anadidosPorJs: (f: FormularioFirmado) => string[] = () => []): ResultadoFormulario[] {
  return formularios.map((original) => {
    const f = { ...original, enviados: [...original.enviados, ...anadidosPorJs(original)] };
    const base = { nombre: f.nombre, enviados: [...f.enviados].sort(), opcionales: f.opcionales.map((o) => `${o.nombre} (${o.tipo})`) };
    if (hashDeCampos(f.enviados, f.hno, f.hchk, sesion) === f.h) {
      return { ...base, estado: 'ok' as const, con: [] };
    }
    // ¿Coincide enviando algún subconjunto de los controles opcionales? (pocos: se prueban todos)
    // `sel` es la fila marcada de una lista: la rejilla la pinta después y sin fila no se envía nada.
    const ops = [...new Set([...f.opcionales.map((o) => o.nombre), ...(f.enviados.includes('sel') ? [] : ['sel'])])].slice(0, 12);
    for (let mascara = 1; mascara < 1 << ops.length; mascara++) {
      const con = ops.filter((_, i) => (mascara >> i) & 1);
      if (hashDeCampos([...f.enviados, ...con], f.hno, f.hchk, sesion) === f.h) {
        return { ...base, estado: 'condicional' as const, con };
      }
    }
    return { ...base, estado: 'falla' as const, con: [] };
  });
}

/**
 * Nombres de campo que firma el código de `frontend/` (`setCamposForm`, `setCamposChk`, claves de
 * `setArrayCamposHidden`), para explicar un hash que no coincide. Sin argumento, los de todo
 * `frontend/`; con un controlador (`modulo/controller/x.php`), los de ese fichero y los de su módulo.
 */
export function camposFirmadosEnElCodigo(raizRepo: string, controlador?: string): string[] {
  const ficheros: string[] = [];
  const recorrer = (dir: string): void => {
    for (const e of fs.readdirSync(dir, { withFileTypes: true })) {
      const ruta = path.join(dir, e.name);
      if (e.isDirectory()) {
        recorrer(ruta);
      } else if (e.name.endsWith('.php')) {
        ficheros.push(ruta);
      }
    }
  };
  recorrer(controlador === undefined ? path.join(raizRepo, 'frontend') : path.join(raizRepo, 'frontend', controlador.split('/')[0]));
  const campos = new Set<string>();
  for (const f of ficheros) {
    const php = fs.readFileSync(f, 'utf8');
    for (const m of php.matchAll(/set[Cc]ampos(?:Form|Chk|No)\(\s*(['"])([^'"$]*)\1/g)) {
      m[2].split('!').forEach((c) => c !== '' && campos.add(c));
    }
    for (const m of php.matchAll(/(['"])(\w+)\1\s*=>/g)) {
      campos.add(m[2]);
    }
    for (const m of php.matchAll(/\bname\s*=\s*["']?(\w+)/g)) {
      campos.add(m[1]);
    }
  }
  return [...campos];
}

/**
 * Busca qué cambio mínimo en los campos enviados hace coincidir el hash: quitar campos enviados
 * («sobra») o añadir campos del diccionario («falta»), hasta `maxCambios` en total.
 */
export function diagnosticar(f: FormularioFirmado, sesion: string, diccionario: string[], maxCambios: number): string | null {
  const enviados = [...new Set(f.enviados)].filter((n) => !META.has(n) && !n.startsWith('scroll_id_'));
  const fuera = new Set(f.hno.split('!'));
  const quitar = enviados.filter((n) => !fuera.has(n)).map((n) => ({ n, quita: true }));
  const anadir = [...new Set(diccionario)].filter((n) => !enviados.includes(n) && !fuera.has(n) && !META.has(n)).map((n) => ({ n, quita: false }));
  const todos = [...quitar, ...anadir];
  const elegidos: { n: string; quita: boolean }[] = [];
  const probar = (): boolean => {
    const sobran = new Set(elegidos.filter((e) => e.quita).map((e) => e.n));
    const nombres = [...enviados.filter((n) => !sobran.has(n)), ...elegidos.filter((e) => !e.quita).map((e) => e.n)];
    return hashDeCampos(nombres, f.hno, f.hchk, sesion) === f.h;
  };
  const buscar = (desde: number, faltan: number): boolean => {
    if (faltan === 0) {
      return probar();
    }
    for (let i = desde; i < todos.length; i++) {
      elegidos.push(todos[i]);
      if (buscar(i + 1, faltan - 1)) {
        return true;
      }
      elegidos.pop();
    }
    return false;
  };
  for (let k = 1; k <= maxCambios; k++) {
    if (buscar(0, k)) {
      const sobra = elegidos.filter((e) => e.quita).map((e) => e.n);
      const falta = elegidos.filter((e) => !e.quita).map((e) => e.n);
      return [falta.length > 0 ? `firmado y no enviado: ${falta.join(', ')}` : '', sobra.length > 0 ? `enviado y no firmado: ${sobra.join(', ')}` : ''].filter((t) => t !== '').join('; ');
    }
  }
  return null;
}
