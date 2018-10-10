<?php
namespace Onedrop\Form\Hubspot;

/*
 * This file is part of the Onedrop.Form.Hubspot package.
 *
 * (c) 2018 Oliver Eglseder <oeglseder@1drop.de>, Onedrop GmbH & Co. KG
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Exception as FlowException;

class Exception extends FlowException
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
