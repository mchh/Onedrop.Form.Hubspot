<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Validation\Validator;

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
