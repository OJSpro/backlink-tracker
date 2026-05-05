<?php

/**
 * @file plugins/generic/backlinkTracker/BacklinkTrackerPlugin.inc.php
 *
 * Copyright (c) 2024
 * Distributed under the GNU GPL v3.
 *
 * @class BacklinkTrackerPlugin
 * @ingroup plugins_generic_backlinkTracker
 *
 * @brief Backlink Tracker plugin - Tracks backlinks from uploaded data files
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class BacklinkTrackerPlugin extends GenericPlugin {
    
    /**
     * @copydoc Plugin::register()
     */
    public function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled($mainContextId)) {
            // Hook into article view template
            HookRegistry::register('Templates::Article::Main', array($this, 'addBacklinkDisplay'));
            
            // Add handler for AJAX requests
            HookRegistry::register('LoadHandler', array($this, 'setupBacklinkHandler'));
        }
        return $success;
    }

    /**
     * Provide a name for this plugin
     */
    public function getDisplayName() {
        return __('plugins.generic.backlinkTracker.displayName');
    }

    /**
     * Provide a description for this plugin
     */
    public function getDescription() {
        return __('plugins.generic.backlinkTracker.description');
    }

    /**
     * Get the locale filename for this plugin
     */
    public function getLocaleFilename($locale) {
        // Try exact locale match first (e.g., en_US)
        $localeFilename = $this->getPluginPath() . "/locale/$locale/locale.xml";
        if (file_exists($localeFilename)) {
            return $localeFilename;
        }

        // Try language-only fallback (e.g., en for en_US)
        $language = substr($locale, 0, 2);
        $localeFilename = $this->getPluginPath() . "/locale/$language/locale.xml";
        if (file_exists($localeFilename)) {
            return $localeFilename;
        }

        return null;
    }

    /**
     * Get the plugin's actions
     */
    public function getActions($request, $actionArgs) {
        $actions = parent::getActions($request, $actionArgs);
        if (!$this->getEnabled()) {
            return $actions;
        }
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        $linkAction = new LinkAction(
            'settings',
            new AjaxModal(
                $router->url($request, null, null, 'manage', null, array('verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'generic')),
                $this->getDisplayName()
            ),
            __('manager.plugins.settings'),
            null
        );
        array_unshift($actions, $linkAction);
        return $actions;
    }

    /**
     * @copydoc Plugin::manage()
     */
    public function manage($args, $request) {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();
                $templateMgr = TemplateManager::getManager($request);

                $this->import('BacklinkTrackerSettingsForm');
                $form = new BacklinkTrackerSettingsForm($this, $context->getId());

                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new \PKP\core\JSONMessage(true);
                    }
                } else {
                    $form->initData();
                }
                return new \PKP\core\JSONMessage(true, $form->fetch($request));

            case 'uploadBacklinks':
                return $this->uploadBacklinks($request);

            case 'clearBacklinks':
                return $this->clearBacklinks($request);

            case 'uploadRedirectMapping':
                return $this->uploadRedirectMapping($request);

            case 'clearRedirectMapping':
                return $this->clearRedirectMapping($request);
        }
        return parent::manage($args, $request);
    }

    /**
     * Upload backlinks file
     */
    public function uploadBacklinks($request) {
        $context = $request->getContext();
        
        if (!isset($_FILES['backlinkFile'])) {
            return new \PKP\core\JSONMessage(false, 'No file uploaded');
        }
        
        $file = $_FILES['backlinkFile'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, array('xlsx', 'xls', 'csv'))) {
            return new \PKP\core\JSONMessage(false, 'Invalid file format');
        }
        
        try {
            $backlinks = $this->parseBacklinkFile($file['tmp_name'], $ext);
            $this->saveBacklinks($context->getId(), $backlinks);
            
            return new \PKP\core\JSONMessage(true, array('message' => 'Successfully imported ' . count($backlinks) . ' backlinks.'));
            
        } catch (Exception $e) {
            error_log('Backlink upload error: ' . $e->getMessage());
            return new \PKP\core\JSONMessage(false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Clear backlinks
     */
    public function clearBacklinks($request) {
        $context = $request->getContext();

        $this->updateEffectiveSetting($context->getId(), 'backlinkData', null);
        $this->updateEffectiveSetting($context->getId(), 'backlinkDataStatus', null);
        $this->updateEffectiveSetting($context->getId(), 'backlinkDataCount', 0);
        $this->updateEffectiveSetting($context->getId(), 'backlinkDataDate', null);

        return new \PKP\core\JSONMessage(true, 'Cleared');
    }

    /**
     * Upload redirect mapping file
     */
    public function uploadRedirectMapping($request) {
        $context = $request->getContext();

        if (!isset($_FILES['redirectMappingFile'])) {
            return new \PKP\core\JSONMessage(false, 'No file uploaded');
        }

        $file = $_FILES['redirectMappingFile'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($ext !== 'csv') {
            return new \PKP\core\JSONMessage(false, 'Invalid file format. Please upload a CSV file.');
        }

        try {
            $mappings = $this->parseRedirectMappingFile($file['tmp_name']);
            $this->saveRedirectMappings($context->getId(), $mappings);

            return new \PKP\core\JSONMessage(true, array('message' => 'Successfully imported ' . count($mappings) . ' URL redirect mappings.'));

        } catch (Exception $e) {
            error_log('Redirect mapping upload error: ' . $e->getMessage());
            return new \PKP\core\JSONMessage(false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Clear redirect mappings
     */
    public function clearRedirectMapping($request) {
        $context = $request->getContext();

        $this->updateEffectiveSetting($context->getId(), 'redirectMappingData', null);
        $this->updateEffectiveSetting($context->getId(), 'redirectMappingCount', 0);
        $this->updateEffectiveSetting($context->getId(), 'redirectMappingDate', null);

        return new \PKP\core\JSONMessage(true, 'Cleared');
    }

    /**
     * Parse redirect mapping CSV file
     */
    private function parseRedirectMappingFile($filePath) {
        $mappings = array();

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            throw new Exception('Unable to open file');
        }

        // Skip header row if exists
        $firstRow = fgetcsv($handle);

        // Check if first row is header (contains "old" or "new" keywords)
        if ($firstRow && (stripos($firstRow[0], 'old') !== false || stripos($firstRow[0], 'source') !== false)) {
            // It's a header, continue to next row
        } else {
            // Not a header, process it as data
            if (count($firstRow) >= 2 && !empty($firstRow[0]) && !empty($firstRow[1])) {
                $oldUrl = $this->normalizeUrl(trim($firstRow[0]));
                $newUrl = $this->normalizeUrl(trim($firstRow[1]));
                if ($oldUrl && $newUrl) {
                    $mappings[$oldUrl] = $newUrl;
                }
            }
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2 && !empty($row[0]) && !empty($row[1])) {
                $oldUrl = $this->normalizeUrl(trim($row[0]));
                $newUrl = $this->normalizeUrl(trim($row[1]));
                if ($oldUrl && $newUrl) {
                    $mappings[$oldUrl] = $newUrl;
                }
            }
        }
        fclose($handle);

        if (empty($mappings)) {
            throw new Exception('No valid URL mappings found in CSV. Expected format: old_url,new_url');
        }

        return $mappings;
    }

    /**
     * Save redirect mappings
     */
    private function saveRedirectMappings($contextId, $mappings) {
        $this->updateEffectiveSetting($contextId, 'redirectMappingData', json_encode($mappings), 'string');
        $this->updateEffectiveSetting($contextId, 'redirectMappingCount', count($mappings), 'int');
        $this->updateEffectiveSetting($contextId, 'redirectMappingDate', date('Y-m-d H:i:s'), 'string');
    }

    /**
     * Normalize URL for comparison
     */
    private function normalizeUrl($url) {
        if (empty($url)) {
            return null;
        }

        $parsed = parse_url($url);
        if (!isset($parsed['host'])) {
            return null;
        }

        $normalized = $parsed['scheme'] . '://' . $parsed['host'] . rtrim($parsed['path'] ?? '/', '/');
        return strtolower($normalized);
    }

    /**
     * Parse backlink file
     */
    private function parseBacklinkFile($filePath, $ext) {
        $backlinks = array();
        
        if ($ext === 'csv') {
            $handle = fopen($filePath, 'r');
            $headers = fgetcsv($handle);
            
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 5) {
                    $backlinks[] = array(
                        'source_url' => $row[2],
                        'target_url' => $row[3],
                        'anchor' => $row[4],
                        'source_title' => $row[1],
                        'ascore' => $row[0]
                    );
                }
            }
            fclose($handle);
            
        } else {
            // Use OJS's PhpSpreadsheet with correct path
            $phpSpreadsheetPath = dirname(__FILE__) . '/../../../lib/pkp/lib/vendor/phpoffice/phpspreadsheet/src/Bootstrap.php';
            
            if (!file_exists($phpSpreadsheetPath)) {
                // Try alternate path
                $phpSpreadsheetPath = dirname(__FILE__) . '/../../../lib/pkp/lib/vendor/autoload.php';
            }
            
            if (file_exists($phpSpreadsheetPath)) {
                require_once($phpSpreadsheetPath);
                
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
                $spreadsheet = $reader->load($filePath);
                $sheet = $spreadsheet->getActiveSheet();
                
                $rows = $sheet->toArray();
                array_shift($rows); // Remove header
                
                foreach ($rows as $row) {
                    if (count($row) >= 5 && !empty($row[3])) {
                        $backlinks[] = array(
                            'source_url' => $row[2],
                            'target_url' => $row[3],
                            'anchor' => $row[4],
                            'source_title' => $row[1],
                            'ascore' => $row[0]
                        );
                    }
                }
            } else {
                throw new Exception('PhpSpreadsheet library not found. Please convert Excel to CSV and upload.');
            }
        }
        
        return $backlinks;
    }

    /**
     * Save backlinks
     */
    private function saveBacklinks($contextId, $backlinks) {
        $this->updateEffectiveSetting($contextId, 'backlinkData', json_encode($backlinks), 'string');
        $this->updateEffectiveSetting($contextId, 'backlinkDataStatus', 'active', 'string');
        $this->updateEffectiveSetting($contextId, 'backlinkDataCount', count($backlinks), 'int');
        $this->updateEffectiveSetting($contextId, 'backlinkDataDate', date('Y-m-d H:i:s'), 'string');
    }

    /**
     * Setup handler for backlink operations
     */
    public function setupBacklinkHandler($hookName, $params) {
        $page =& $params[0];
        if ($page === 'backlink') {
            $this->import('BacklinkHandler');
            define('HANDLER_CLASS', 'BacklinkHandler');
            return true;
        }
        return false;
    }

    /**
     * Add backlink display to article page
     */
    public function addBacklinkDisplay($hookName, $params) {
        $templateMgr = $params[1];
        $output =& $params[2];
        
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        
        if (!$this->getEnabled($context->getId())) {
            return false;
        }

        // Get current article
        $article = $templateMgr->getTemplateVars('article');
        if (!$article) {
            return false;
        }

        // Prepare template data
        $templateMgr->assign(array(
            'articleId' => $article->getId(),
            'pluginUrl' => $request->getBaseUrl() . '/' . $this->getPluginPath(),
        ));

        $output .= $templateMgr->fetch($this->getTemplateResource('backlinkDisplay.tpl'));
        return false;
    }

    /**
     * Get article URLs (OJS and Repository)
     */
    public function getArticleUrls($article) {
        $urls = array();
        
        // Get OJS URL
        $request = Application::get()->getRequest();
        $dispatcher = $request->getDispatcher();
        $urls['ojs'] = $dispatcher->url(
            $request,
            ROUTE_PAGE,
            null,
            'article',
            'view',
            $article->getBestId()
        );
        
        // Get Repository URL from Galley using OJS 3.4 method
        $publication = $article->getCurrentPublication();
        if ($publication) {
            $galleys = $publication->getData('galleys');
            
            if ($galleys) {
                $repoLabel = $this->getSetting($request->getContext()->getId(), 'repoGalleyLabel') ?: 'REPO';
                
                foreach ($galleys as $galley) {
                    $label = $galley->getLabel();
                    
                    // Check if this is a repository galley
                    if (stripos($label, $repoLabel) !== false || 
                        stripos($label, 'repository') !== false ||
                        stripos($label, 'full text') !== false) {
                        
                        $remoteUrl = $galley->getRemoteURL();
                        if ($remoteUrl) {
                            $urls['repo'] = $remoteUrl;
                            break;
                        }
                    }
                }
            }
        }
        
        return $urls;
    }

    /**
     * Get effective setting (site-wide or per-journal)
     * Returns site-wide setting if useSiteWideData is enabled, otherwise per-journal setting
     */
    public function getEffectiveSetting($contextId, $settingName) {
        $useSiteWide = $this->getSetting($contextId, 'useSiteWideData');

        if ($useSiteWide) {
            // Use site-wide setting (context ID = 0)
            return $this->getSetting(0, $settingName);
        } else {
            // Use per-journal setting
            return $this->getSetting($contextId, $settingName);
        }
    }

    /**
     * Update effective setting (site-wide or per-journal)
     */
    public function updateEffectiveSetting($contextId, $settingName, $value, $type = null) {
        $useSiteWide = $this->getSetting($contextId, 'useSiteWideData');

        if ($useSiteWide) {
            // Update site-wide setting (context ID = 0)
            $this->updateSetting(0, $settingName, $value, $type);
        } else {
            // Update per-journal setting
            $this->updateSetting($contextId, $settingName, $value, $type);
        }
    }
}