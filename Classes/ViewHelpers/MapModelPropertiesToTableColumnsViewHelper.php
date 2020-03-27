<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class MapModelPropertiesToTableColumnsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager
     */
    protected $configurationManager;

    /**
     * @var array
     */
    protected $configuration;

    /**
     * Arguments initialization
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'class',
            'string',
            'class',
            true
        );
        $this->registerArgument(
            'table',
            'string',
            'table',
            true
        );
        $this->registerArgument(
            'data',
            'string',
            'data',
            false
        );
    }

    /**
     * render
     *
     * @return array
     */
    public function render()
    {
        $class = $this->arguments['class'];
        $table = $this->arguments['table'];
        $data = $this->arguments['data'];

        $this->getConfiguration();

        if (isset($this->configuration['persistence']['classes'][$class]['mapping']) &&
            $this->configuration['persistence']['classes'][$class]['mapping']['tableName'] == $table
        ) {
            $mapping = [];
            foreach ($this->configuration['persistence']['classes'][$class]['mapping']['columns'] as $tableColumn => $modelPropertyData) {
                $modelProperty = $modelPropertyData['mapOnProperty'];
                $mapping[$modelProperty] = $tableColumn;
            }

            $data = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getGettableProperties($data);

            foreach ($data as $key => $value) {
                if (isset($mapping[$key])) {
                    unset($data[$key]);
                    $data[$mapping[$key]] = $value;
                }
            }

            return $data;
        } else {
            return $data;
        }
    }

    protected function getConfiguration()
    {
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Object\ObjectManager::class
        );
        $this->configurationManager = $this->objectManager->get(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class
        );
        $this->configuration = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
    }
}
