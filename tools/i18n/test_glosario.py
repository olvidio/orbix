"""Tests del glosario de traducciones.

Uso (desde la raíz del repo): tools/i18n/.venv/bin/python -m unittest tools/i18n/test_glosario.py
"""

import os
import sys
import unittest

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import glosario  # noqa: E402

G = {
    "siglas": ["ca", "dl", "n"],
    "terminos": [
        {"es": ["acta", "actas"], "en": ["grade record", "grade records"], "de": ["Notenprotokoll"]},
        {"es": ["asistente", "asistentes"], "de": ["Teilnehmer"], "nota": "no Assistent"},
        {"es": ["de paso"], "en": ["visiting"]},
    ],
}


def terminos(msgid: str, msgstr: str, idioma: str) -> list[str]:
    return [f.termino for f in glosario.incumplimientos(G, msgid, msgstr, idioma)]


class GlosarioTest(unittest.TestCase):
    def test_una_traduccion_con_el_termino_acordado_cumple(self):
        self.assertEqual([], terminos("borrar acta", "delete grade record", "en"))

    def test_una_traduccion_sin_el_termino_acordado_se_marca(self):
        self.assertEqual(["acta"], terminos("borrar acta", "delete proceedings", "en_US.UTF-8"))

    def test_el_plural_y_las_mayusculas_cuentan(self):
        self.assertEqual(["acta"], terminos("Actas del curso", "Course minutes", "en"))
        self.assertEqual([], terminos("Actas del curso", "Course Grade Records", "en"))

    def test_solo_cuenta_la_palabra_entera_en_espanol(self):
        self.assertEqual([], terminos("actualizar", "update", "en"))
        self.assertEqual([], terminos("contacta", "contact", "en"))

    def test_en_aleman_vale_dentro_de_una_palabra_compuesta(self):
        self.assertEqual([], terminos("lista de asistentes", "Teilnehmerliste", "de"))

    def test_un_termino_de_varias_palabras(self):
        self.assertEqual(["de paso"], terminos("persona de paso", "passing person", "en"))
        self.assertEqual([], terminos("persona de paso", "visiting person", "en"))

    def test_un_idioma_sin_formas_para_el_termino_no_se_comprueba(self):
        self.assertEqual([], terminos("asistentes", "attendees", "en"))

    def test_las_siglas_de_dos_o_mas_letras_se_conservan(self):
        self.assertEqual(["ca"], terminos("asistentes al ca", "Teilnehmer am AK", "de"))
        self.assertEqual([], terminos("asistentes al ca", "Teilnehmer am CA", "de"))
        self.assertEqual([], terminos("n.º de la dl: %s", "Nr. der DL: %s", "de"), "n (una letra) no se comprueba")

    def test_una_traduccion_vacia_no_se_marca(self):
        self.assertEqual([], terminos("borrar acta", "", "en"))

    def test_el_texto_del_prompt_lleva_los_terminos_del_idioma_y_las_siglas(self):
        texto = glosario.texto_para_prompt(G, "de_DE.UTF-8")
        self.assertIn("- asistente / asistentes → Teilnehmer (no Assistent)", texto)
        self.assertIn("- acta / actas → Notenprotokoll", texto)
        self.assertNotIn("de paso", texto)
        self.assertIn("ca, dl, n", texto)

    def test_un_idioma_desconocido_se_rechaza(self):
        with self.assertRaises(ValueError):
            glosario.idioma_de("fr_FR.UTF-8")

    def test_el_glosario_del_repo_es_valido(self):
        g = glosario.cargar()
        self.assertGreater(len(g["terminos"]), 0)
        for t in g["terminos"]:
            self.assertTrue(t["es"], t)
            for idioma in ("ca", "en", "de", "it"):
                self.assertTrue(t.get(idioma), f"{t['es'][0]}: falta {idioma}")
            self.assertNotIn("revisar", t, f"{t['es'][0]}: pendiente de revisar")


if __name__ == "__main__":
    unittest.main()
