<?php

namespace BlueMedia\OnlinePayments\Util;

/**
 * Class EnvironmentRequirements.
 *
 * @package BlueMedia\OnlinePayments\Util
 */
class EnvironmentRequirements
{

    /**
     * @return bool
     */
    public static function hasSupportedPhpVersion()
    {
        return (!PHP_VERSION_ID) >= 70000;
    }

    /**
     * @param string $extensionName
     *
     * @return bool
     */
    public static function hasPhpExtension($extensionName)
    {
        return extension_loaded($extensionName);
    }
}
