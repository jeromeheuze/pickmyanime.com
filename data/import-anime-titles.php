<?php
// import_anime_titles.php — Parse anime-titles.xml and upsert English names into anime_titles

// --- Configuration: adjust to your environment ---
require_once '../includes/dbconnect.php';
$xmlFile  = 'anime-titles.xml';

// --- Load and parse the XML file ---
if (!file_exists($xmlFile)) {
    die("<strong>XML file not found:</strong> $xmlFile");
}

$xml = simplexml_load_file($xmlFile);
if (!$xml) {
    die("<strong>Failed to parse XML:</strong> $xmlFile");
}

// --- Prepare upsert statement ---
$sql = "
    INSERT INTO anime_titles (aid, title_short, title_official)
    VALUES (?, ?, ?)
    ON DUPLICATE KEY UPDATE
      title_short    = VALUES(title_short),
      title_official = VALUES(title_official)
";
$stmt = $DBcon->prepare($sql);
if (!$stmt) {
    die("<strong>SQL prepare failed:</strong> " . $DBcon->error);
}

// --- Iterate through <anime> nodes ---
foreach ($xml->anime as $anime) {
    $aid           = (int) $anime['aid'];
    $title_short   = null;
    $title_official = null;

    // Each <title> element may have xml:lang and type attributes
    foreach ($anime->title as $t) {
        $attrs = $t->attributes('http://www.w3.org/XML/1998/namespace');
        $lang  = (string) $attrs['lang'];
        if ($lang === 'en') {
            $type = (string) $t['type'];
            if ($type === 'short') {
                $title_short = trim((string) $t);
            } elseif ($type === 'official') {
                $title_official = trim((string) $t);
            }
        }
    }

    // Execute the upsert
    $stmt->bind_param('iss', $aid, $title_short, $title_official);
    if (!$stmt->execute()) {
        error_log("Failed upsert for aid={$aid}: " . $stmt->error);
    }
}

// --- Cleanup ---
$stmt->close();
$DBcon->close();

echo "<strong>Import complete.</strong>\n";
