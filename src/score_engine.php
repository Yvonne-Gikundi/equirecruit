<?php

function extract_keywords($text) {
    // Remove punctuation & lowercase
    $text = strtolower(preg_replace('/[^a-z0-9 ]/', ' ', $text));

    // Common stopwords to remove
    $stopwords = ['the','and','or','with','to','from','of','in','a','an','for','on','is','are','was','were'];

    $words = array_filter(explode(" ", $text), function($w) use ($stopwords) {
        return strlen($w) > 2 && !in_array($w, $stopwords);
    });

    return array_values($words);
}

function skill_match_score($job_description, $cv_text) {
    $jobWords = extract_keywords($job_description);
    $cvWords = extract_keywords($cv_text);

    $intersection = array_intersect($jobWords, $cvWords);

    if (count($jobWords) == 0) return 0;

    return (count($intersection) / count($jobWords)) * 100;
}

function experience_score($cv_text) {
    // Extract years of experience from CV
    preg_match('/(\d+)\s+years?/', strtolower($cv_text), $matches);
    $years = isset($matches[1]) ? (int)$matches[1] : 0;

    if ($years >= 5) return 80 + ($years * 2);
    if ($years >= 2) return 60 + ($years * 5);
    if ($years == 1) return 40;

    return 20;
}

function inferred_potential_score($cv_text) {
    $positiveTraits = ['leadership','communication','teamwork','initiative','problem','adaptability','fast learner'];
    $cvWords = extract_keywords($cv_text);

    $found = array_intersect($positiveTraits, $cvWords);

    return min(100, count($found) * 15);
}

function top_matching_keywords($job_description, $cv_text) {
    $jobWords = extract_keywords($job_description);
    $cvWords = extract_keywords($cv_text);

    $intersection = array_intersect($jobWords, $cvWords);

    return implode(", ", array_slice($intersection, 0, 10));
}

function calculate_total_score($skill, $exp, $potential) {
    // Weighted model
    return ($skill * 0.5) + ($exp * 0.3) + ($potential * 0.2);
}

?>
