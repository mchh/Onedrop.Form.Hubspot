<?php
namespace Onedrop\Form\Hubspot;

class Exception extends \Neos\Flow\Exception
{
    const NO_FOM_SELECTED_CODE = 1536740278;
    const NO_FOM_SELECTED_MESSAGE = 'No form selected';

    /**
     * @return Exception
     */
    public static function noFormSelected(): Exception
    {
        return new self(self::NO_FOM_SELECTED_MESSAGE, self::NO_FOM_SELECTED_CODE);
    }
}
