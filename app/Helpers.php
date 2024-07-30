<?php


if (!function_exists('splitPascalCase')) {
    function splitPascalCase($string)
    {
        $word = '';
        // Use a regular expression to insert a space before each capital letter except the first one
        $splitString = preg_replace('/(?<!^)([A-Z])/', ' $1', $string);
        foreach (explode(' ', $splitString) as $index => $s) {
            if ($index == 0) {
                $word .= $s;

                continue;
            }
            $word .= '-' . $s;
        }

        return $word;
    }
}

if (!function_exists('formatIDR')) {
    function formatIDR($output)
    {
        return number_format($output, 0, ',', '.');
    }
}

if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        return \Illuminate\Support\Carbon::parse($date)->format('d-m-Y');
    }
}
