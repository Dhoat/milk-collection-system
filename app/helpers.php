<?php

use App\Models\Setting;

if (!function_exists('setting')) {
    /**
     * Helper function to quickly retrieve setting values.
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return Setting::getAllFromCache();
        }

        $defaults = Setting::getDefaults();
        $fallback = $default ?? ($defaults[$key] ?? null);

        return Setting::get($key, $fallback);
    }
}
