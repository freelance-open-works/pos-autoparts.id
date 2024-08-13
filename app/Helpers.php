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

if (!function_exists('formatDateString')) {
    function formatDateString($date)
    {
        return \Illuminate\Support\Carbon::parse($date)->translatedFormat('d F Y');
    }
}

if (!function_exists('formatNumZero')) {
    function formatNumZero($n)
    {
        $max = 6; // 100000

        $number = '';
        foreach (range(0, $max - strlen($n)) as $_) {
            $number .= '0';
        }

        return $number . $n;
    }
}
