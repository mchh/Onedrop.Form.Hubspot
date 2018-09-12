<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\FusionObjects;

use Neos\Fusion\Core\ExceptionHandlers\AbstractRenderingExceptionHandler;
use Onedrop\Form\Hubspot\Exception;

class ExceptionHandler extends AbstractRenderingExceptionHandler
{
    protected function handle($fusionPath, \Exception $exception, $referenceCode)
    {
        if (Exception::NO_FOM_SELECTED_CODE === $exception->getCode()) {
            return $this->runtime->render($fusionPath . '/noForm');
        }
    }
}
