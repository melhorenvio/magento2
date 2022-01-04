<?php

namespace MelhorEnvio\Quote\Setup;

    use Magento\Framework\DB\Ddl\Table;
    use Magento\Framework\Setup\InstallSchemaInterface;
    use Magento\Framework\Setup\ModuleContextInterface;
    use Magento\Framework\Setup\SchemaSetupInterface;

    /**
     * Class InstallSchema
     * @package MelhorEnvio\Quote\Setup
     */
    class InstallSchema implements InstallSchemaInterface
    {
        /**
         * Installs DB schema for a module
         *
         * @param SchemaSetupInterface $setup
         * @param ModuleContextInterface $context
         * @return void
         */
        public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
        {
            $installer = $setup;

            $installer->startSetup();
            if (version_compare($context->getVersion(), '1.0.1') < 0) {
                $tableQuote = $installer->getConnection()
                    ->newTable($installer->getTable('melhorenvio_quote'))
                    ->addColumn(
                        'quote_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'ID'
                    )
                    ->addColumn(
                        'cost',
                        Table::TYPE_DECIMAL,
                        '10,2',
                        ['nullable' => false],
                        'Cost'
                    )
                    ->addColumn(
                        'order_id',
                        Table::TYPE_TEXT,
                        '255',
                        ['nullable' => false],
                        'Order ID'
                    )
                    ->addColumn(
                        'order_increment_id',
                        Table::TYPE_TEXT,
                        '255',
                        ['nullable' => false],
                        'Order Increment ID'
                    )
                    ->addColumn(
                        'description',
                        Table::TYPE_TEXT,
                        '255',
                        ['nullable' => true],
                        'Description'
                    )
                    ->addColumn(
                        'service',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false, 'identity' => false],
                        'Service'
                    )
                    ->addColumn(
                        'status',
                        Table::TYPE_TEXT,
                        '20',
                        ['nullable' => true],
                        'Status'
                    )
                    ->addColumn(
                        'nf_key',
                        Table::TYPE_TEXT,
                        '255',
                        ['nullable' => true],
                        'NF Key'
                    )
                    ->addColumn(
                        'validate',
                        Table::TYPE_TEXT,
                        '11',
                        ['nullable' => true],
                        'Validate'
                    )
                    ->addColumn(
                        'created_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'on_update' => false, 'default' => 'CURRENT_TIMESTAMP'],
                        'Created At'
                    )
                    ->addColumn(
                        'updated_at',
                        Table::TYPE_TIMESTAMP,
                        null,
                        ['nullable' => false, 'on_update' => true, 'default' => 'CURRENT_TIMESTAMP'],
                        'Updated At'
                    )
                    ->addColumn(
                        'additional_data',
                        Table::TYPE_TEXT,
                        null,
                        ['nullable' => true],
                        'Additional Data'
                    )
                    ->addColumn(
                        'quote_reverse',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => true, 'default' => '0'],
                        'Quote Reverse'
                    )
                    ->setComment('About Your Table');
                $installer->getConnection()->createTable($tableQuote);


                $tablePackage = $installer->getConnection()
                    ->newTable($installer->getTable('melhorenvio_package'))
                    ->addColumn(
                        'id',
                        Table::TYPE_INTEGER,
                        null,
                        ['identity' => true, 'nullable' => false, 'primary' => true],
                        'ID'
                    )
                    ->addColumn(
                        'quote_id',
                        Table::TYPE_INTEGER,
                        null,
                        ['nullable' => false],
                        'Quote Id'
                    )
                    ->addColumn(
                        'packages',
                        Table::TYPE_TEXT,
                        '',
                        ['nullable' => true],
                        'Packages'
                    )
                    ->addColumn(
                        'code',
                        Table::TYPE_TEXT,
                        '255',
                        ['nullable' => true],
                        'Code'
                    )
                    ->addColumn(
                        'tracking',
                        Table::TYPE_TEXT,
                        '20',
                        ['nullable' => true],
                        'Tracking'
                    )
                    ->addColumn(
                        'protocol',
                        Table::TYPE_TEXT,
                        '50',
                        ['nullable' => true],
                        'Protocol'
                    )
                    ->addForeignKey(
                        $installer->getFkName('melhorenvio_package', 'quote_id', 'melhorenvio_quote', 'quote_id'),
                        'quote_id',
                        $installer->getTable('melhorenvio_quote'),
                        'quote_id',
                        \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                    )
                    ->setComment('Melhor Envio Package');

                $installer->getConnection()->createTable($tablePackage);
            }
            $installer->endSetup();
        }
    }
