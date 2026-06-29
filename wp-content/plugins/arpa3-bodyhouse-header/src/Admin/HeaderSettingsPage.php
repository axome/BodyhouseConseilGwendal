<?php

namespace Arpa3\BodyhouseHeader\Admin;

final class HeaderSettingsPage
{
    private $optionProvider;

    public function __construct()
    {
        $this->optionProvider = new OptionProvider();

        add_action('admin_menu', [ $this, 'registerOptionPage' ]);
        add_action('admin_init', [ $this, 'registerSettings' ]);
    }

    public function registerOptionPage()
    {
        add_options_page('Settings Admin', 'Bodyhouse Header', 'manage_options', 'bh-header-settings', [ $this, 'renderPage' ]);
    }

    /**
     * Register settings and section used by the form ('do_settings_sections' in renderPage).
     */
    public function registerSettings()
    {
        register_setting( 'bh_header_option_group', OptionProvider::OPTION, [$this, 'sanitize']);
        add_settings_section('bh-header-settings-url', 'Configuration', null, 'bh-header-settings');

        add_settings_field(OptionProvider::MENU_URL, 'Menu API URL', [ $this, 'renderInput' ], 'bh-header-settings', 'bh-header-settings-url', ['name' => OptionProvider::MENU_URL]);
    }

    /**
     * Render the default settings page.
     */
    public function renderPage()
    {
        ?>
            <div class="wrap">
                <h1>My Settings</h1>
                <form method="post" action="options.php">
                    <?php
                        settings_fields('bh_header_option_group');
                        do_settings_sections('bh-header-settings');
                        submit_button();
                    ?>
                </form>
            </div>
        <?php
    }

    /**
     * Sanitize settings fields.
     *
     * @param array $input Settings inputs
     * @return array Input sanitized array
     */
    public function sanitize(array $input)
    {
        $newInput = [];

        if (isset($input[OptionProvider::MENU_URL])) {
            $newInput[OptionProvider::MENU_URL] = sanitize_text_field($input[OptionProvider::MENU_URL]);
        }

        return $newInput;
    }

    /**
     * Get the settings option array and print one of its values.
     * 
     * @param array $args
     */
    public function renderInput(array $args)
    {
        $option = $this->optionProvider->getOption($args['name']);
        printf('<input type="text" id="title" name="%s[%s]" value="%s" />', OptionProvider::OPTION, $args['name'], $option ?? '');
    }
}