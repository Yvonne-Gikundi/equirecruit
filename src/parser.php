<?php
// src/parser.php - simple fallback parser for TXT/DOCX/PDF (basic)

function extract_text_from_file($path) {
  $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
  if (in_array($ext, ['txt','csv'])) {
    return file_get_contents($path);
  } elseif ($ext === 'pdf') {
    // try shell pdftotext if available
    $out = null;
    @exec('pdftotext ' . escapeshellarg($path) . ' -', $out);
    if (!empty($out)) return is_array($out) ? implode("\n", $out) : $out;
    return "[PDF file uploaded; server lacks pdftotext - open file to read]";
  } elseif ($ext === 'docx') {
    return docx_to_text($path);
  }
  return '';
}

function docx_to_text($filename) {
  $zip = new ZipArchive;
  if ($zip->open($filename) === true) {
    if (($idx = $zip->locateName('word/document.xml')) !== false) {
      $xml = $zip->getFromIndex($idx);
      $zip->close();
      $xml = str_replace('</w:p>', "\n", $xml);
      $text = strip_tags($xml);
      return html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }
    $zip->close();
  }
  return '';
}
?>