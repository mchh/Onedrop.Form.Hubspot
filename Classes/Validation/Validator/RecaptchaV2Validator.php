<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\Validation\Validator;

/*
 * This file is part of the Onedrop.Form.Hubspot package.
 *
 * (c) 2019 Stefan Thor <s.thor@logikwerk.com>, Logikwerk GmbH
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Validation\Validator\AbstractValidator;

/**
 * Class RecaptchaV2Validator
 */
class RecaptchaV2Validator extends AbstractValidator
{
    /**
     * @var array
     */
    protected $supportedOptions = [
        'secretkey' => ['', 'Secretkey needed', 'string', true]
    ];

    /**
     * @param mixed $value
     */
    protected function isValid($value)
    {

        if (!is_string($value)) {
            $this->addError('The given value was not a valid string.', 1450180930);
            return;
        }

        $recaptcha = new \ReCaptcha\ReCaptcha($this->options['secretkey']);
        $resp = $recaptcha->verify($value, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess() === false) {
            $this->addError('Error validating recaptcha token.', 1536243701);
        }

    }
}
