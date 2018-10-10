<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Validation\Validator;

/*
 * This file is part of the Onedrop.Form.Hubspot package.
 *
 * (c) 2018 Oliver Eglseder <oeglseder@1drop.de>, Onedrop GmbH & Co. KG
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Validation\Validator\AbstractValidator;

/**
 * Class FreeMailValidator
 */
class FreeEmailAddressProviderValidator extends AbstractValidator
{
    /**
     * @param mixed $value
     */
    protected function isValid($value)
    {
        $list = file(
            'resource://Onedrop.Form.Hubspot:/Private/Data/FreeMailProvider.txt',
            FILE_SKIP_EMPTY_LINES | FILE_IGNORE_NEW_LINES
        );
        $domain = substr($value, strrpos($value, '@') + 1);
        if (in_array($domain, $list)) {
            $this->addError('Free Mail Providers are not allowed', 1536243713);
        }
    }
}
