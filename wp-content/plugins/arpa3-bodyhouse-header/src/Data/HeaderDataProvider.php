<?php

namespace Arpa3\BodyhouseHeader\Data;

use Arpa3\BodyhouseHeader\Admin\OptionProvider;
use Arpa3\BodyhouseHeader\Data\Exception\EmptyDataException;

final class HeaderDataProvider 
{
    private const CACHE_KEY = 'bh-header';

    private $optionProvider;

    public function __construct()
    {
        $this->optionProvider = new OptionProvider();
    }

    /**
     * Retrieve header data saved in cache or retrieve it
     * directly from the URL if nothing is found.
     * 
     * @return array Header data
     */
    public function getHeaderData() 
    {
        $headerData = get_transient(self::CACHE_KEY);

        if (!empty($headerData)) {
            return $headerData;
        }

        $headerData = $this->fetchHeaderData();
        set_transient(self::CACHE_KEY, $headerData);

        return $headerData;
    }

    /**
     * Fetch header data from the OPTION 'MENU_URL'.
     * 
     * @return array Header data
     */
    protected function fetchHeaderData() 
    {
        $optionMenuUrl = $this->optionProvider->getOption(OptionProvider::MENU_URL);

        if ($optionMenuUrl == null) {
            return null;
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $optionMenuUrl);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, 'A:Z');

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            return null;
        }

        return json_decode($response, true);
    }

    /**
     * Refresh the WordPress cache with new PrestaShop data, 
     * it's mainly used for the endpoint to force the clear of cache.
     *
     * @return array State
     */
    public function clearCache()
    {
        $headerData = $this->fetchHeaderData();

        if ($headerData === null) {
            throw new EmptyDataException('No data has been received from the PrestaShop endpoint.');
        }

        return set_transient(self::CACHE_KEY, $headerData);
    }

    /**
     * Get the menu HTML content.
     * 
     * @return string Menu
     */
    public function getMenuContent()
    {
        return $this->getHeaderData()['content'] ?? '';
    }

    public function getDoofinderContent()
    {
        return $this->getHeaderData()['doofinder'] ?? '';
    }
}