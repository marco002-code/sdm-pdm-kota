<?php

if (!function_exists('mb_strimwidth')) {
    function mb_strimwidth(string $string, int $start, int $width, string $trim_marker = "", ?string $encoding = null): string
    {
        $sub = mb_substr($string, $start, $width);
        if (mb_strlen($string) > ($start + $width)) {
            $markerLen = mb_strlen($trim_marker);
            if ($markerLen < $width) {
                return mb_substr($string, $start, $width - $markerLen) . $trim_marker;
            }
            return $trim_marker;
        }
        return $sub;
    }
}

// Polyfill for mb_split and regex functions when mbstring regex is not compiled into PHP
if (!function_exists('mb_split')) {
    function mb_split(string $pattern, string $string, int $limit = -1): array|false
    {
        $delimiter = '/';
        $escaped = str_replace($delimiter, '\\' . $delimiter, $pattern);
        $result = @preg_split($delimiter . $escaped . $delimiter . 'u', $string, $limit);
        if ($result === false) {
            $result = @preg_split($delimiter . $escaped . $delimiter, $string, $limit);
        }
        return $result;
    }
}

if (!function_exists('mb_ereg')) {
    function mb_ereg(string $pattern, string $string, ?array &$regs = null): int|false
    {
        $delimiter = '/';
        $escaped = str_replace($delimiter, '\\' . $delimiter, $pattern);
        $res = preg_match($delimiter . $escaped . $delimiter . 'u', $string, $matches);
        if ($res === false) {
            $res = preg_match($delimiter . $escaped . $delimiter, $string, $matches);
        }
        if ($res) {
            if ($regs !== null) {
                $regs = $matches;
            }
            return 1;
        }
        return false;
    }
}

if (!function_exists('mb_eregi')) {
    function mb_eregi(string $pattern, string $string, ?array &$regs = null): int|false
    {
        $delimiter = '/';
        $escaped = str_replace($delimiter, '\\' . $delimiter, $pattern);
        $res = preg_match($delimiter . $escaped . $delimiter . 'ui', $string, $matches);
        if ($res === false) {
            $res = preg_match($delimiter . $escaped . $delimiter . 'i', $string, $matches);
        }
        if ($res) {
            if ($regs !== null) {
                $regs = $matches;
            }
            return 1;
        }
        return false;
    }
}

if (!function_exists('mb_ereg_replace')) {
    function mb_ereg_replace(string $pattern, string $replacement, string $string, ?string $options = null): string|false
    {
        $flags = 'u';
        if ($options && str_contains($options, 'i')) {
            $flags .= 'i';
        }
        $delimiter = '/';
        $escaped = str_replace($delimiter, '\\' . $delimiter, $pattern);
        $res = @preg_replace($delimiter . $escaped . $delimiter . $flags, $replacement, $string);
        if ($res === null) {
            $res = @preg_replace($delimiter . $escaped . $delimiter . str_replace('u', '', $flags), $replacement, $string);
        }
        return $res ?? false;
    }
}
