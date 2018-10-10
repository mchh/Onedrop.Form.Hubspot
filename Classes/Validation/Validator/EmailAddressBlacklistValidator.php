<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Validation\Validator;

use Neos\Flow\Validation\Validator\AbstractValidator;

/**
 * Class EmailAddressBlacklistValidator
 */
class EmailAddressBlacklistValidator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $supportedOptions = [
        'blacklist' => ['', 'Comma separated email address domains to block', 'string', true],
    ];

    /**
     * @param mixed $value
     */
    protected function isValid($value)
    {
        $blacklist = array_map('trim', explode(',', $this->options['blacklist']));
        $domain = substr($value, strrpos($value, '@') + 1);
        if (in_array($domain, $blacklist)) {
            $this->addError('This mail address is blacklisted', 1536243708);
        }
    }
}
