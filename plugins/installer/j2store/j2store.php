<?php
/**
 * @package J2Store
 * @copyright Copyright (c)2023 Ramesh Elamathi / J2Store.org
 * @license GNU GPL v3 or later
 */
\defined('_JEXEC') or die;

class PlgInstallerJ2Store extends \Joomla\CMS\Plugin\CMSPlugin
{
    function onInstallerBeforePackageDownload(&$url, &$headers)
    {
        $domain = 'dev.j2store.net';
        if (strpos($url, $domain) !== false) {
            if (stripos($url, '/plugin/') !== false) {
                $element = substr(substr($url, strrpos($url, "/") + 1), 0, -4);
                $folder = substr($url, strpos($url, '/plugin/') + 8);
                if (empty($folder)) return;
                $type = substr($folder, 0, strpos($folder, '/' . $element));
                if (!empty($type) && !empty($element)) {
                    $plugin = JPluginHelper::getPlugin($type, $element);
                    if (is_object($plugin) && isset($plugin->params)) {
                        $params = new \Joomla\Registry\Registry($plugin->params);
                        $license_key = $params->get('license_key', '');
                        /*$item_id = $params->get('item_id', 0);
                        $item_name = $params->get('item_name', '');*/
                        $baseURL = str_replace('/administrator', '', JURI::base());
                        $api_params = array(
                            'edd_action' => 'get_version',
                            'license' => $license_key,
                            /*'item_id' => $item_id,
                            'item_name' => rawurlencode($item_name),*/
                            'url' => $baseURL,
                            'environment' => ' ',
                        );
                        require_once(JPATH_ADMINISTRATOR . '/components/com_j2store/helpers/license.php');
                        $license_helper = J2License::getInstance();
                        $api_url = 'https://dev.j2store.net/joomla_release/edd-api';
                        $license = $license_helper->getVersion($api_url, $api_params);
                        if (is_array($license) && isset($license['download_link']) && !empty($license['download_link'])) {
                            $url = $license['download_link'];
                        }
                    }
                }
            }
        }
    }
}