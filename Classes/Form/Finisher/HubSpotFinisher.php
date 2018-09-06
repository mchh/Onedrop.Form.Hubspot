<?php
namespace Onedrop\Form\Hubspot\Form\Finisher;

use Neos\Form\Core\Model\AbstractFinisher;

/**
 * Class HubSpotFinisher
 */
class HubSpotFinisher extends AbstractFinisher
{
    /**
     * This method is called in the concrete finisher whenever self::execute() is called.
     *
     * Override and fill with your own implementation!
     *
     * @return void
     * @api
     */
    protected function executeInternal()
    {
        $formRuntime = $this->finisherContext->getFormRuntime();
        $variables = $formRuntime->getFormState()->getFormValues();

        \Neos\Flow\var_dump($variables);
        \Neos\Flow\var_dump($formRuntime);
        die();

        $hubspotutk = $_COOKIE['hubspotutk'];
        $ip_addr = $_SERVER['REMOTE_ADDR'];
        $hs_context = [
            'hutk'      => $hubspotutk,
            'ipAddress' => $ip_addr,
            'pageUrl'   => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
            'pageName'  => $variables['page'] ?? '',
        ];
        $hs_context_json = json_encode($hs_context);

        $urlParams = '';
        foreach ($this->parseOption('fieldMapping') as $neosFormField => $hubSpotField) {
            if (isset($variables[$neosFormField]) && !empty($variables[$neosFormField])) {
                $urlParams .= (empty($urlParams) ? '' : '&') . $hubSpotField . '=' . urlencode($variables[$neosFormField]);
            }
        }
        $urlParams .= '&hs_context=' . urlencode($hs_context_json);

        $endpoint = 'https://forms.hubspot.com/uploads/form/v2/' . $this->parseOption('portalId') . '/' . $this->parseOption('formGuid');

        $ch = @curl_init();
        @curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $urlParams);
        @curl_setopt($ch, CURLOPT_URL, $endpoint);
        @curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded',
        ]);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = @curl_exec($ch);
        $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
        @curl_close($ch);
    }
}
