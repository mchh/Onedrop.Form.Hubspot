<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\FusionObjects;

/*
 * This file is part of the Onedrop.Form.Hubspot package.
 *
 * (c) 2018 Oliver Eglseder <oeglseder@1drop.de>, Onedrop GmbH & Co. KG
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Fusion\Core\ExceptionHandlers\AbstractRenderingExceptionHandler;
use Onedrop\Form\Hubspot\Exception;

class ExceptionHandler extends AbstractRenderingExceptionHandler
{
    /**
     * @param string $fusionPath
     * @param \Exception $exception
     * @param int $referenceCode
     * @return string
     * @throws \Neos\Flow\Security\Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function handle($fusionPath, \Exception $exception, $referenceCode):string
    {
        if (Exception::NO_FOM_SELECTED_CODE === $exception->getCode()) {
            return $this->runtime->render($fusionPath . '/noForm');
        }
        return (string)$exception;
    }
}
