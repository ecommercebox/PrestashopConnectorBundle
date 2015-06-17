<?php

namespace Pim\Bundle\PrestashopConnectorBundle\Writer;

use Akeneo\Bundle\BatchBundle\Item\InvalidItemException;
use Pim\Bundle\PrestashopConnectorBundle\Guesser\WebserviceGuesser;
use Pim\Bundle\PrestashopConnectorBundle\Manager\DeltaProductExportManager;
use Pim\Bundle\PrestashopConnectorBundle\Webservice\PrestashopSoapClientParametersRegistry;
use Pim\Bundle\PrestashopConnectorBundle\Webservice\SoapCallException;

/**
 * Delta product association writer.
 *
 * @author    Willy Mesnage <willy.mesnage@akeneo.com>
 * @copyright 2015 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DeltaProductAssociationWriter extends ProductAssociationWriter
{
    /** @var DeltaProductExportManager */
    protected $productExportManager;

    /**
     * @param WebserviceGuesser                   $webserviceGuesser
     * @param PrestashopSoapClientParametersRegistry $clientParamsRegistry
     * @param DeltaProductExportManager           $productExportManager
     */
    public function __construct(
        WebserviceGuesser $webserviceGuesser,
        PrestashopSoapClientParametersRegistry $clientParamsRegistry,
        DeltaProductExportManager $productExportManager
    ) {
        parent::__construct($webserviceGuesser, $clientParamsRegistry);

        $this->productExportManager = $productExportManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function handleProductAssociationCalls(array $associationCalls)
    {
        foreach ($associationCalls['remove'] as $removeCall) {
            try {
                $this->webservice->removeProductAssociation($removeCall);
                $this->productExportManager->updateProductAssociationExport(
                    $this->getJobInstance(),
                    $removeCall['product']
                );
            } catch (SoapCallException $e) {
                throw new InvalidItemException(
                    sprintf(
                        'An error occured during a product association remove call. This may be due to a linked '.
                        'product that doesn\'t exist on Prestashop side. Error message : %s',
                        $e->getMessage()
                    ),
                    $removeCall
                );
            }
        }

        foreach ($associationCalls['create'] as $createCall) {
            try {
                $this->webservice->createProductAssociation($createCall);
                $this->productExportManager->updateProductAssociationExport(
                    $this->getJobInstance(),
                    $createCall['product']
                );
            } catch (SoapCallException $e) {
                throw new InvalidItemException(
                    sprintf(
                        'An error occured during a product association add call. This may be due to a linked '.
                        'product that doesn\'t exist on Prestashop side. Error message : %s',
                        $e->getMessage()
                    ),
                    $createCall
                );
            }
        }
    }

    /**
     * @return \Akeneo\Bundle\BatchBundle\Entity\JobInstance
     */
    protected function getJobInstance()
    {
        return $this->stepExecution->getJobExecution()->getJobInstance();
    }
}