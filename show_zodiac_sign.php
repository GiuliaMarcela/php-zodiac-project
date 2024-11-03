<!DOCTYPE html>
<html lang="en">
<?php
include('layout/header.php');

$birth_date = $_POST['birth'];
$html = file_get_contents('https://pt.wikipedia.org/wiki/Signo_astrol%C3%B3gico');

function extractSignsFromTable($html)
{
    $dom = new DOMDocument();
    $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED);

    $xpath = new DOMXPath($dom);
    $nodes = $xpath->query('//table[@class="wikitable sortable"]');

    if ($nodes->length === 0) {
        throw new Exception('Tabela não encontrada.');
    }

    $tabela = $nodes->item(0);

    $rows = $tabela->getElementsByTagName('tr');

    foreach ($rows as $row) {
        $cols = explode("\n", trim($row->textContent));

        if ($cols[4] == 'Nomes dos Signos') {
            continue;
        }

        $normalizeDates = preg_replace('/\s+–\s+/', ' - ', $cols[4]);
        $dates = preg_split('/\s*[-–\/]\s*/', $normalizeDates);


        $sign = [
            "symbol" => $cols[0],
            "name" => $cols[2],
            "startDate" => $dates[0],
            "endDate" => $dates[1]
        ];

        $signs[] = $sign;
    }

    return $signs;
}

function normalizeDate($str_date)
{
    $str_date = strtolower(trim($str_date));
    $pattern_main = '/(\d+) de (\w{3})/';
    $pattern_without_preposition = '/(\d+) (\w{3})/';

    $months = [
        'jan'   => '01',
        'fev' => '02',
        'mar'     => '03',
        'abr'     => '04',
        'mai'      => '05',
        'jun'     => '06',
        'jul'     => '07',
        'ago'    => '08',
        'set'  => '09',
        'out'   => '10',
        'nov'  => '11',
        'dez'  => '12'
    ];

    if (preg_match($pattern_main, $str_date, $matches)) {
        $day = $matches[1];
        $month = $matches[2];
    } elseif (preg_match($pattern_without_preposition, $str_date, $matches)) {
        $day = $matches[1];
        $month = $matches[2];
    } else {
        return 'invalid_date';
    }

    return $day . '-' . $months[$month] . '-2000';
}

function findSignFromBirthDate($birthDate, $signs)
{
    $dateTimeBirth = new DateTime($birthDate);

    foreach ($signs as $sign) {
        $startDate = new DateTime(normalizeDate($sign["startDate"]));
        $endDate = new DateTime(normalizeDate($sign["endDate"]));

        $startMonth = $startDate->format('m');
        $startDay = $startDate->format('d');
        $endMonth = $endDate->format('m');
        $endDay = $endDate->format('d');

        $isInRange = ($dateTimeBirth->format('m') == $startMonth && $dateTimeBirth->format('d') >= $startDay) ||
            ($dateTimeBirth->format('m') == $endMonth && $dateTimeBirth->format('d') <= $endDay) ||
            ($dateTimeBirth->format('m') > $startMonth && $dateTimeBirth->format('m') < $endMonth);

        if ($isInRange) {
            return $sign;
        }
    }

    return 'Signo não encontrado.';
}

function removeAccents($text)
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
    $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);

    return $text;
}

function getDailyHoroscope($signName)
{
    $signName = strtolower(removeAccents($signName));
    $url = "https://www.terra.com.br/vida-e-estilo/horoscopo/signos/$signName/";

    $html = file_get_contents($url);

    if (!$html) {
        return "Horóscopo não disponível no momento.";
    }

    $dom = new DOMDocument;
    @$dom->loadHTML($html);

    $xpath = new DOMXPath($dom);
    $horoscopeNode = $xpath->query("//div[@id='horoscope_1']/p");

    if ($horoscopeNode->length > 0) {
        return $horoscopeNode->item(0)->nodeValue;
    }

    return "Horóscopo não encontrado.";
}

$signs = extractSignsFromTable($html);
$signoEncontrado = findSignFromBirthDate($birth_date, $signs);
$horoscopoDoDia = getDailyHoroscope($signoEncontrado['name']);
?>

<body>
    <main class="container align-content-center w-max min-vh-100 p-4">
        <div class="mb-4">
            <a class="btn btn-back gap-2" href="index.php" target="_self">
                <i class="ph ph-arrow-left"></i>
                <span>Voltar</span>
            </a>
        </div>

        <div class="card--custom">
            <?php
            echo '<div class="d-flex align-items-center gap-2 justify-content-between mb-3 flex-wrap">';
            echo '<div class="d-flex align-items-center gap-3">';
            echo "<span class='fs-2'>" . $signoEncontrado['symbol'] . "</span>";
            echo "<h1 class='fs-4 mb-0'>" . $signoEncontrado['name'] . "</h1>";
            echo "</div>";
            echo "<p class='text-muted fs-6 fw-light m-0'>" . $signoEncontrado['startDate'] . " - " . $signoEncontrado['endDate'] . "</p>";
            echo "</div>";
            echo "<p>" . $horoscopoDoDia . "</p>";
            ?>
        </div>

        <?php include('layout/footer.php') ?>
    </main>
</body>

</html>