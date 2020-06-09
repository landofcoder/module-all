<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_All
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\All\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $setup->getConnection()->dropTable($setup->getTable('lof_all_license'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('lof_all_license')
            )
        ->addColumn(
            'license_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'License ID'
            )
        ->addColumn(
            'extension_code',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Extension Code'
            )
        ->addColumn(
            'extension_name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Extension Name'
            )
        ->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Status'
            )
        ->addIndex(
            $setup->getIdxName('lof_all_license', ['license_id']),
            ['license_id']
            );
        $installer->getConnection()->createTable($table);
        
        
        $installer->endSetup();
    }
}