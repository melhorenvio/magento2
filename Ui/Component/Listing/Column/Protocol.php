<?php

namespace MelhorEnvio\Quote\Ui\Component\Listing\Column;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use MelhorEnvio\Quote\Api\PackageRepositoryInterface;

/**
 * Class Protocol
 * @package MelhorEnvio\Quote\Ui\Component\Listing\Column
 */
class Protocol extends Column
{
    /**
     * @var PackageRepositoryInterface
     */
    private $packageRepository;

    /**
     * Protocol constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PackageRepositoryInterface $packageRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PackageRepositoryInterface $packageRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->packageRepository = $packageRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $protocol = [];
                $packageCollection = $this->packageRepository->getListByParentShippingId($item['quote_id']);
                foreach ($packageCollection->getItems() as $package) {
                    if (!empty($package->getProtocol())) {
                        $protocol[] = $package->getProtocol();
                    }
                }

                $item['protocol'] = implode(' / ', $protocol);
            }
        }

        return $dataSource;
    }
}
