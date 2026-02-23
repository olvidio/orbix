<?php

$csv_file = __DIR__ . '/aux_metamenus.csv';
$generar_mapa_script = __DIR__ . '/generar_mapa.php';

if (!file_exists($csv_file)) {
    die("❌ Error: No s'ha trobat el fitxer $csv_file\n");
}

if (!file_exists($generar_mapa_script)) {
    die("❌ Error: No s'ha trobat el script $generar_mapa_script\n");
}

echo "🚀 Processant fitxer CSV: $csv_file\n\n";

$handle = fopen($csv_file, 'r');
if (!$handle) {
    die("❌ Error: No s'ha pogut obrir el fitxer CSV\n");
}

// Saltar la capçalera (primera línia)
$header = fgets($handle);

$total = 0;
$processats = 0;

while (($line = fgets($handle)) !== false) {
    $line = trim($line);
    if (empty($line)) continue;

    // Separar per punt i coma
    $parts = explode(';', $line);
    if (count($parts) < 3) continue;

    $url = trim($parts[2]);

    // Només processar URLs que comencin per "apps" o "frontend"
    if (strpos($url, 'apps') === 0 || strpos($url, 'frontend') === 0) {
        $total++;
        echo "[$total] Processant: $url\n";

        // Executar el script generar_mapa.php
        $command = "php " . escapeshellarg($generar_mapa_script) . " " . escapeshellarg($url) . " 2>&1";
        $output = [];
        $return_var = 0;

        exec($command, $output, $return_var);

        if ($return_var === 0) {
            $processats++;
            echo "   ✅ " . implode("\n   ", $output) . "\n";
        } else {
            echo "   ⚠️  Error en processar: " . implode("\n   ", $output) . "\n";
        }

        echo "\n";
    }
}

fclose($handle);

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "✅ Procés completat!\n";
echo "📊 Total URLs trobades (apps o frontend): $total\n";
echo "✔️  Processades correctament: $processats\n";
if ($total > $processats) {
    echo "⚠️  Amb errors: " . ($total - $processats) . "\n";
}
