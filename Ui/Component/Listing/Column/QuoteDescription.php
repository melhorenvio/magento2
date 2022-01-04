<?php

namespace MelhorEnvio\Quote\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class QuoteDescription
 * @package MelhorEnvio\Quote\Ui\Component\Listing\Column
 */
class QuoteDescription extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['description'])) {
                    $descriptionList = explode('-', $item['description']);
                    $item['description'] = strpos($descriptionList[0], 'via') === 0
                        ? $descriptionList[1]
                        : $descriptionList [0];
                }
            }
        }

        return $dataSource;
    }
}
