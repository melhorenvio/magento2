<?php

namespace MelhorEnvio\Quote\Setup;

    use Magento\Framework\DB\Ddl\Table;
    use Magento\Framework\Setup\ModuleContextInterface;
    use Magento\Framework\Setup\SchemaSetupInterface;
    use Magento\Framework\Setup\UpgradeSchemaInterface;

    /**
     * Class UpgradeSchema
     * @package MelhorEnvio\Quote\Setup
     */
    class UpgradeSchema implements UpgradeSchemaInterface
    {
        /**
         * @param SchemaSetupInterface $setup
         * @param ModuleContextInterface $context
         */
        public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
        {
            $connection = $setup->getConnection();
            if (version_compare($context->getVersion(), '1.0.2') < 0) {
                $connection->addColumn(
                    $setup->getTable('sales_order'),
                    'melhorenvio_shipping',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => '',
                        'nullable' => true,
                        'comment' => 'Melhor Envio Shipping'
                    ]
                );
                $connection->modifyColumn(
                    $setup->getTable('melhorenvio_quote'),
                    'cost',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '10,2'
                    ]
                );
            }

            if (version_compare($context->getVersion(), '1.0.2') < 0) {
                $connection->addColumn(
                    $setup->getTable('melhorenvio_quote'),
                    'origin',
                    [
                        'type' => Table::TYPE_TEXT,
                        'length' => 10,
                        'nullable' => true,
                        'comment' => 'Quote Origin'
                    ]
                );
            }

            if (version_compare($context->getVersion(), '1.0.3') < 0) {
                if (!$setup->tableExists('melhor_store')) {
                    $table = $setup->getConnection()->newTable(
                        $setup->getTable('melhor_store')
                    )
                        ->addColumn(
                            'melhor_store_id',
                            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            null,
                            [
                                'identity' => true,
                                'nullable' => false,
                                'primary'  => true,
                                'unsigned' => true,
                            ],
                            'Store ID'
                        )
                        ->addColumn(
                            'melhor_store_key',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            ['nullable => false'],
                            'Store Store Key'
                        )
                        ->addColumn(
                            'melhor_store_name',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            ['nullable => false'],
                            'Store Name'
                        )
                        ->addColumn(
                            'melhor_store_postcode',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Postcode'
                        )
                        ->addColumn(
                            'melhor_store_street',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            '64k',
                            [],
                            'Street'
                        )
                        ->addColumn(
                            'melhor_store_number',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            20,
                            [],
                            'Number'
                        )
                        ->addColumn(
                            'melhor_store_complement',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'Complement'
                        )
                        ->addColumn(
                            'melhor_store_district',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'District'
                        )
                        ->addColumn(
                            'melhor_store_city',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'City'
                        )
                        ->addColumn(
                            'melhor_store_state',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            255,
                            [],
                            'State'
                        )
                        ->addColumn(
                            'created_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                            'Created At'
                        )
                        ->addColumn(
                            'updated_at',
                            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                            null,
                            ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                            'Updated At'
                        )
                        ->setComment('Melhor Envio Stores');
                    $setup->getConnection()->createTable($table);

                    $setup->getConnection()->addIndex(
                        $setup->getTable('melhor_store'),
                        $setup->getIdxName(
                            $setup->getTable('melhor_store'),
                            ['melhor_store_name', 'melhor_store_postcode', 'melhor_store_city', 'melhor_store_state', 'melhor_store_district'],
                            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                        ),
                        ['melhor_store_name', 'melhor_store_postcode', 'melhor_store_city', 'melhor_store_state', 'melhor_store_district'],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                    );
                }
            }
        }
    }
