<?php

namespace Meetanshi\SavedCards\Gateway\Http;

/**
 * Class TransferFactory
 */
class TransferFactory extends AbstractTransferFactory
{

    /**
     * @param array $request
     * @return \Magento\Payment\Gateway\Http\TransferInterface
     */
    public function create(array $request)
    {
        return $this->transferBuilder
            ->setBody($request)
            ->build();
    }
}
