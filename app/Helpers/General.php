<?php
if (!function_exists('asset')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if (!function_exists('clean')) {
    function clean($value)
    {
        $value = trim(strtolower($value));

        if (strpos($value, ',') !== false) {
            return explode(",", $value);
        }

        return $value;
    }
}

if (!function_exists('getStatus')) {
    function getStatus($statusArray,$request)
    {
        $flipArr = array_flip(explode(',',$request));
        return array_intersect_key($statusArray,$flipArr);
    }
}

function getIds($value)
{
    return array_map('intval', explode(',', $value));
}

function pageLimit($request): int
{
    return ($request->filled('page_limit') && is_numeric($request->input('page_limit'))) ? $request->input('page_limit') : env('PAGE_LIMIT',20);
}


function stringToUpper($string)
{
    return trim(strtoupper($string));
}
