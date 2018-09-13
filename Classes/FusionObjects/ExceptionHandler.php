<?php
declare(strict_types=1);
namespace Onedrop\Form\Hubspot\FusionObjects;

use Neos\Fusion\Core\ExceptionHandlers\AbstractRenderingExceptionHandler;
use Onedrop\Form\Hubspot\Exception;

/**
 * Class ExceptionHandler
 */
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
