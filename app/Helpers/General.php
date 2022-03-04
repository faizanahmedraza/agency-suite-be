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

if (!function_exists('getPrefix')) {
    function getPrefix($value, $prefix)
    {
        if (!(str_contains($value, $prefix) && strpos($value, $prefix) === 0)) {
            return true;
        }
        return false;
    }
}

function pageLimit($request): int
{
    return ($request->filled('page_limit') && is_numeric($request->input('page_limit'))) ? $request->input('page_limit') : config("bionic_customer.page_limit");
}


function stringToUpper($string)
{
    return trim(strtoupper($string));
}
