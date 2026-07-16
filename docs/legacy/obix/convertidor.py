import pandas as pd
import re
import io

def transformar_a_ods(fitxer_md, fitxer_ods):
    with open(fitxer_md, 'r', encoding='utf-8') as f:
        contingut = f.read()

    # Busquem cada secció de "Grupo X" i la seva taula
    # El patró busca "Grupo X" i tot el contingut fins al següent "Grupo" o final de fitxer
    grups = re.split(r'#+\s*(Grupo\s+\d+)', contingut)
    
    # El primer element sol ser el títol general, el descartem si no té taula
    writer = pd.ExcelWriter(fitxer_ods, engine='odf')
    
    found_any = False
    for i in range(1, len(grups), 2):
        nom_grup = grups[i]
        bloc_text = grups[i+1]
        
        # Busquem la taula dins del bloc de text
        if '|' in bloc_text:
            # Netegem les línies de separació de Markdown (--- | ---)
            linies = [l.strip() for l in bloc_text.strip().split('\n') if '|' in l and not re.match(r'^[\s|:-]+$', l)]
            
            if linies:
                # Convertim el text de la taula a una llista de llistes
                dades = []
                for l in linies:
                    # Traiem la primera i última barra vertical i separem
                    fila = [cel.strip() for cel in l.strip('|').split('|')]
                    dades.append(fila)
                
                # Creem el DataFrame (agafant la primera fila com a capçalera)
                df = pd.DataFrame(dades[1:], columns=dades[0])
                
                # Afegim columnes buides per QA si no existeixen
                if 'Resultado' not in df.columns:
                    df['Resultado'] = ""
                if 'Observaciones' not in df.columns:
                    df['Observaciones'] = ""
                
                # Guardem a una pestanya nova
                df.to_excel(writer, sheet_name=nom_grup, index=False)
                found_any = True

    if found_any:
        writer.close()
        print(f"✅ Fet! S'ha creat '{fitxer_ods}' amb totes les pestanyes.")
    else:
        print("❌ No s'han trobat taules vàlides per processar.")

# Execució
transformar_a_ods('Checklist QA.md', 'Checklist.ods')
