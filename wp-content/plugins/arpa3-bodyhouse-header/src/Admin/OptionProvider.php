<?php

namespace Arpa3\BodyhouseHeader\Admin;

final class OptionProvider
{
    const OPTION = 'bh-header-option';
    const MENU_URL = 'menu_url';

    private $options = null;

    /**
     * Retrieve options from the database and cache it 
     * so we don't refetch it everytime.
     * 
     * @return array Options
     */
    public function getOptions()
    {
        if ($this->options != null) {
            return $this->options;
        }

        $this->options = get_option(self::OPTION);
        return $this->options;
    }

    /**
     * Retrieve a specific field in options.
     * 
     * @param string $field
     * @return mixed
     */
    public function getOption(string $field)
    {
        if ($this->options == null) {
            $this->getOptions();
        }

        return $this->options[$field];
    }
}