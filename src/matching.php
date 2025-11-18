<?php
// src/matching.php - simple TF-IDF like matching and cosine similarity

function tokenize($text) {
  $text = mb_strtolower($text);
  $text = preg_replace('/[^a-z0-9\s]/u', ' ', $text);
  $tokens = preg_split('/\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
  $stopwords = load_stopwords();
  $out = [];
  foreach ($tokens as $t) {
    if (strlen($t) < 2) continue;
    if (in_array($t, $stopwords)) continue;
    $out[] = $t;
  }
  return $out;
}

function load_stopwords() {
  return ['the','and','for','with','that','from','this','will','your','are','but','have','has','was','were','a','an','in','on','of','to','is','as','by','or'];
}

function tf($tokens) {
  $counts = array_count_values($tokens);
  $len = array_sum($counts);
  $tf = [];
  foreach ($counts as $k=>$v) $tf[$k] = $v / max(1,$len);
  return $tf;
}

function idf($docs) {
  $N = count($docs);
  $df = [];
  foreach ($docs as $doc) {
    $unique = array_unique($doc);
    foreach ($unique as $w) $df[$w] = ($df[$w] ?? 0) + 1;
  }
  $idf = [];
  foreach ($df as $w=>$d) $idf[$w] = log( ($N) / ($d + 1) + 1 );
  return $idf;
}

function build_vector($tf, $idf) {
  $vec = [];
  foreach ($tf as $w=>$v) $vec[$w] = $v * ($idf[$w] ?? 0.0);
  return $vec;
}

function cosine_similarity($v1, $v2) {
  $dot = 0; $n1 = 0; $n2 = 0;
  foreach ($v1 as $k=>$val) {
    $dot += $val * ($v2[$k] ?? 0);
    $n1 += $val*$val;
  }
  foreach ($v2 as $val) $n2 += $val*$val;
  if ($n1 == 0 || $n2 == 0) return 0;
  return $dot / (sqrt($n1)*sqrt($n2));
}

function match_job_and_resume($job_text, $resume_text, $all_resumes_texts = []) {
  $job_tokens = tokenize($job_text);
  $resume_tokens = tokenize($resume_text);
  $docs = array_map('tokenize', $all_resumes_texts);
  $docs_for_idf = array_merge([$job_tokens], $docs);
  $idf = idf($docs_for_idf);
  $job_tf = tf($job_tokens);
  $res_tf = tf($resume_tokens);
  $job_vec = build_vector($job_tf, $idf);
  $res_vec = build_vector($res_tf, $idf);
  $score = cosine_similarity($job_vec, $res_vec);
  $explain = [];
  foreach ($job_vec as $w=>$jw) {
    if (!empty($res_vec[$w])) $explain[$w] = $jw * $res_vec[$w];
  }
  arsort($explain);
  $top = array_slice($explain, 0, 8, true);
  return ['score'=>$score, 'explanation'=>$top];
}
?>