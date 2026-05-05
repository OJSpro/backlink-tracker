<?php

/**
 * @file plugins/generic/backlinkTracker/BacklinkHandler.inc.php
 *
 * Copyright (c) 2024
 * Distributed under the GNU GPL v3.
 *
 * @class BacklinkHandler
 * @ingroup plugins_generic_backlinkTracker
 *
 * @brief Handle AJAX requests for backlink operations
 */

import('classes.handler.Handler');

class BacklinkHandler extends Handler {

    /** @var BacklinkTrackerPlugin */
    var $_plugin;

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->_plugin = PluginRegistry::getPlugin('generic', 'backlinktrackerplugin');
    }

    /**
     * Fetch backlinks for an article
     */
    function fetch($args, $request) {
        $articleId = $request->getUserVar('articleId');
        $context = $request->getContext();

        if (!$articleId || !$context) {
            return new \PKP\core\JSONMessage(false, 'Invalid request');
        }

        // Get article
        $article = \APP\facades\Repo::submission()->get($articleId);

        if (!$article) {
            return new \PKP\core\JSONMessage(false, 'Article not found');
        }

        // Get URLs
        $urls = $this->_plugin->getArticleUrls($article);

        // Get backlinks from database (with redirect mapping applied)
        $backlinks = $this->getBacklinksForUrls($context->getId(), $urls);

        // Add target type to each backlink
        $backlinks = $this->addTargetType($backlinks, $urls);

        // Apply domain filtering
        $backlinks = $this->applyDomainFiltering($context->getId(), $backlinks);

        // Group by domain (includes deduplication)
        $grouped = $this->groupBacklinksByDomain($backlinks);

        // Calculate total deduplicated backlinks
        $totalDeduplicated = 0;
        foreach ($grouped as $group) {
            $totalDeduplicated += $group['count'];
        }

        // Prepare response
        $data = array(
            'total' => $totalDeduplicated,
            'domains' => count($grouped),
            'grouped_links' => $grouped,
            'urls' => $urls,
            'last_updated' => $this->_plugin->getEffectiveSetting($context->getId(), 'backlinkDataDate')
        );

        return new \PKP\core\JSONMessage(true, $data);
    }

    /**
     * Upload backlink file
     */
    function upload($args, $request) {
        $context = $request->getContext();
        
        if (!isset($_FILES['backlinkFile'])) {
            return new \PKP\core\JSONMessage(false, 'No file uploaded');
        }
        
        $file = $_FILES['backlinkFile'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($ext, array('xlsx', 'xls', 'csv'))) {
            return new \PKP\core\JSONMessage(false, 'Invalid file format. Please upload Excel or CSV file.');
        }
        
        try {
            $backlinks = $this->parseBacklinkFile($file['tmp_name'], $ext);
            $this->saveBacklinks($context->getId(), $backlinks);
            
            $message = 'Successfully imported ' . count($backlinks) . ' backlinks.';
            return new \PKP\core\JSONMessage(true, array('message' => $message));
            
        } catch (Exception $e) {
            error_log('Backlink upload error: ' . $e->getMessage());
            return new \PKP\core\JSONMessage(false, 'Error processing file: ' . $e->getMessage());
        }
    }

    /**
     * Clear all backlinks
     */
    function clear($args, $request) {
        $context = $request->getContext();

        $this->_plugin->updateEffectiveSetting($context->getId(), 'backlinkData', null);
        $this->_plugin->updateEffectiveSetting($context->getId(), 'backlinkDataStatus', null);
        $this->_plugin->updateEffectiveSetting($context->getId(), 'backlinkDataCount', 0);
        $this->_plugin->updateEffectiveSetting($context->getId(), 'backlinkDataDate', null);

        return new \PKP\core\JSONMessage(true, 'Cleared');
    }

    /**
     * Parse backlink file (Excel or CSV)
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
            // Excel parsing (not implemented - ask user to convert to CSV)
            throw new Exception('Please convert Excel file to CSV format before uploading.');
        }
        
        return $backlinks;
    }

    /**
     * Save backlinks to plugin settings
     */
    private function saveBacklinks($contextId, $backlinks) {
        $this->_plugin->updateEffectiveSetting($contextId, 'backlinkData', json_encode($backlinks), 'string');
        $this->_plugin->updateEffectiveSetting($contextId, 'backlinkDataStatus', 'active', 'string');
        $this->_plugin->updateEffectiveSetting($contextId, 'backlinkDataCount', count($backlinks), 'int');
        $this->_plugin->updateEffectiveSetting($contextId, 'backlinkDataDate', date('Y-m-d H:i:s'), 'string');
    }

    /**
     * Get backlinks for specific URLs
     */
    private function getBacklinksForUrls($contextId, $urls) {
        $allBacklinks = json_decode($this->_plugin->getEffectiveSetting($contextId, 'backlinkData'), true);

        if (!$allBacklinks) {
            return array();
        }

        // Load redirect mappings
        $redirectMappings = json_decode($this->_plugin->getEffectiveSetting($contextId, 'redirectMappingData'), true);
        if (!$redirectMappings) {
            $redirectMappings = array();
        }

        $matchedBacklinks = array();

        // Normalize URLs for comparison
        $normalizedUrls = array();
        foreach ($urls as $key => $url) {
            // Remove trailing slashes, query strings, and fragments
            $parsed = parse_url($url);
            $normalized = $parsed['scheme'] . '://' . $parsed['host'] . rtrim($parsed['path'], '/');
            $normalizedUrls[$key] = strtolower($normalized);
        }

        foreach ($allBacklinks as $backlink) {
            $targetUrl = $backlink['target_url'];

            // Normalize target URL
            $parsed = parse_url($targetUrl);
            if (!isset($parsed['host'])) {
                continue;
            }

            $normalizedTarget = $parsed['scheme'] . '://' . $parsed['host'] . rtrim($parsed['path'], '/');
            $normalizedTarget = strtolower($normalizedTarget);

            // Apply redirect mapping if exists
            if (isset($redirectMappings[$normalizedTarget])) {
                $normalizedTarget = $redirectMappings[$normalizedTarget];
                $backlink['_redirected_from'] = $backlink['target_url'];
                $backlink['target_url'] = $normalizedTarget;
            }

            // Check for exact match
            foreach ($normalizedUrls as $key => $url) {
                if ($normalizedTarget === $url) {
                    $backlink['matched_url_type'] = $key;
                    $matchedBacklinks[] = $backlink;
                    break;
                }
            }
        }

        return $matchedBacklinks;
    }

    /**
     * Add target type (Abstract Page or Full Text) to backlinks
     */
    private function addTargetType($backlinks, $urls) {
        foreach ($backlinks as &$backlink) {
            if (isset($backlink['matched_url_type'])) {
                if ($backlink['matched_url_type'] === 'ojs') {
                    $backlink['target_type'] = 'Abstract Page';
                } elseif ($backlink['matched_url_type'] === 'repo') {
                    $backlink['target_type'] = 'Full Text';
                } else {
                    $backlink['target_type'] = 'Article';
                }
            }
        }
        return $backlinks;
    }

    /**
     * Apply domain filtering
     */
    private function applyDomainFiltering($contextId, $backlinks) {
        $blockedDomainsStr = $this->_plugin->getEffectiveSetting($contextId, 'blockedDomains');

        if (empty($blockedDomainsStr)) {
            return $backlinks;
        }

        // Parse blocked domains (one per line)
        $blockedDomains = array_filter(array_map('trim', explode("\n", $blockedDomainsStr)));

        if (empty($blockedDomains)) {
            return $backlinks;
        }

        // Normalize blocked domains
        $normalizedBlockedDomains = array_map('strtolower', $blockedDomains);

        // Filter out backlinks from blocked domains
        $filteredBacklinks = array();
        foreach ($backlinks as $backlink) {
            $domain = parse_url($backlink['source_url'], PHP_URL_HOST);
            if (!$domain) {
                continue;
            }

            $domain = strtolower($domain);

            // Check if domain is blocked (exact match or subdomain)
            $isBlocked = false;
            foreach ($normalizedBlockedDomains as $blockedDomain) {
                // Check for exact match or subdomain match
                if ($domain === $blockedDomain || str_ends_with($domain, '.' . $blockedDomain)) {
                    $isBlocked = true;
                    break;
                }
            }

            if (!$isBlocked) {
                $filteredBacklinks[] = $backlink;
            }
        }

        return $filteredBacklinks;
    }

    /**
     * Group backlinks by domain
     */
    private function groupBacklinksByDomain($backlinks) {
        $grouped = array();

        foreach ($backlinks as $backlink) {
            $domain = parse_url($backlink['source_url'], PHP_URL_HOST);
            if (!$domain) continue;

            if (!isset($grouped[$domain])) {
                $grouped[$domain] = array(
                    'domain' => $domain,
                    'count' => 0,
                    'links' => array(),
                    'uniqueUrls' => array() // Track unique URLs without query strings
                );
            }

            // Get URL without query string and normalize protocol for deduplication
            $parsed = parse_url($backlink['source_url']);
            // Always use https for comparison to deduplicate http/https variants
            $baseUrl = 'https://' . $parsed['host'] . ($parsed['path'] ?? '/');
            $baseUrl = strtolower($baseUrl);

            // Only add if this base URL hasn't been added yet
            if (!isset($grouped[$domain]['uniqueUrls'][$baseUrl])) {
                $grouped[$domain]['count']++;
                $grouped[$domain]['links'][] = array(
                    'url' => $backlink['source_url'],
                    'anchor' => $backlink['anchor'],
                    'title' => $backlink['source_title'],
                    'target_type' => $backlink['target_type'] ?? 'Article'
                );
                $grouped[$domain]['uniqueUrls'][$baseUrl] = true;
            }
        }

        // Remove temporary uniqueUrls tracking
        foreach ($grouped as &$group) {
            unset($group['uniqueUrls']);
        }

        // Sort by count (descending)
        uasort($grouped, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        return array_values($grouped);
    }
}