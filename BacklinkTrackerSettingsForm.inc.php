<?php

/**
 * @file plugins/generic/backlinkTracker/BacklinkTrackerSettingsForm.inc.php
 *
 * Copyright (c) 2024
 * Distributed under the GNU GPL v3.
 *
 * @class BacklinkTrackerSettingsForm
 * @ingroup plugins_generic_backlinkTracker
 *
 * @brief Form for journal managers to setup backlink tracking
 */

import('lib.pkp.classes.form.Form');

class BacklinkTrackerSettingsForm extends Form {

    /** @var int Context ID */
    var $_contextId;

    /** @var BacklinkTrackerPlugin Plugin */
    var $_plugin;

    /**
     * Constructor
     * @param $plugin BacklinkTrackerPlugin
     * @param $contextId int Context ID
     */
    function __construct($plugin, $contextId) {
        $this->_contextId = $contextId;
        $this->_plugin = $plugin;

        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
    }

    /**
     * Initialize form data
     */
    function initData() {
        $this->_data = array(
            'repoGalleyLabel' => $this->_plugin->getSetting($this->_contextId, 'repoGalleyLabel') ?: 'REPO',
            'blockedDomains' => $this->_plugin->getSetting($this->_contextId, 'blockedDomains') ?: '',
            'useSiteWideData' => $this->_plugin->getSetting($this->_contextId, 'useSiteWideData') ?: false,
            'backlinkDataStatus' => $this->_plugin->getEffectiveSetting($this->_contextId, 'backlinkDataStatus'),
            'backlinkDataCount' => $this->_plugin->getEffectiveSetting($this->_contextId, 'backlinkDataCount') ?: 0,
            'backlinkDataDate' => $this->_plugin->getEffectiveSetting($this->_contextId, 'backlinkDataDate'),
            'redirectMappingCount' => $this->_plugin->getEffectiveSetting($this->_contextId, 'redirectMappingCount') ?: 0,
            'redirectMappingDate' => $this->_plugin->getEffectiveSetting($this->_contextId, 'redirectMappingDate'),
        );
    }

    /**
     * Assign form data to user-submitted data
     */
    function readInputData() {
        $this->readUserVars(array(
            'repoGalleyLabel',
            'blockedDomains',
            'useSiteWideData',
        ));
    }

    /**
     * Fetch the form
     */
    function fetch($request, $template = null, $display = false) {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->_plugin->getName());
        return parent::fetch($request, $template, $display);
    }

    /**
     * Save settings
     */
    function execute(...$functionArgs) {
        $this->_plugin->updateSetting($this->_contextId, 'repoGalleyLabel', trim($this->getData('repoGalleyLabel')), 'string');
        $this->_plugin->updateSetting($this->_contextId, 'blockedDomains', trim($this->getData('blockedDomains')), 'string');

        // Handle checkbox: getData returns null if unchecked
        $useSiteWideData = $this->getData('useSiteWideData');
        $this->_plugin->updateSetting($this->_contextId, 'useSiteWideData', ($useSiteWideData ? true : false), 'bool');

        parent::execute(...$functionArgs);
    }
}