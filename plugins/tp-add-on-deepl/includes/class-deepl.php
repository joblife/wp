<?php

class TRP_DeepL {

    protected $loader;

    public function __construct() {
        $trp                 = TRP_Translate_Press::get_trp_instance();
        $this->loader        = $trp->get_component( 'loader' );

        $this->loader->add_action( 'trp_machine_translation_engines', $this, 'add_engine', 10, 1 );
        $this->loader->add_action( 'trp_machine_translation_extra_settings_middle', $this, 'add_settings', 10, 1 );
        $this->loader->add_action( 'trp_machine_translation_sanitize_settings', $this, 'sanitize_settings', 10, 1 );
        $this->loader->add_action( 'admin_enqueue_scripts', $this, 'add_scripts', 99, 1 );
        $this->loader->add_filter( 'trp_deepl_target_language', $this, 'configure_api_target_language', 10, 3 );
        $this->loader->add_filter( 'trp_deepl_source_language', $this, 'configure_api_source_language', 10, 3 );

        // Updater
        require_once(  TRP_DL_PLUGIN_DIR . 'includes/class-plugin-updater.php' );
        $this->plugin_updater = new TRP_DL_Plugin_Updater();
        $this->loader->add_action( 'admin_init', $this->plugin_updater, 'activate_license' );
        $this->loader->add_action( 'admin_init', $this->plugin_updater, 'deactivate_license' );
        $this->loader->add_action( 'admin_notices', $this->plugin_updater, 'admin_notices' );

        // Licence page
        global $trp_license_page;
        if( !isset( $trp_license_page )  ) {
            $trp_license_page = new TRP_LICENSE_PAGE();
            $this->loader->add_action( 'admin_menu', $trp_license_page, 'license_menu');
            $this->loader->add_action( 'admin_init', $trp_license_page, 'register_option' );
        }

        require_once TRP_DL_PLUGIN_DIR . 'includes/class-deepl-machine-translator.php';
    }

    public function add_scripts( $hook ){
        if( $hook == 'admin_page_trp_machine_translation' )
            wp_enqueue_script( 'trp-deepl-settings', TRP_DL_PLUGIN_URL . 'assets/js/trp-deepl-back-end.js', [ 'jquery' ], TRP_DL_PLUGIN_VERSION );
    }

    public function add_engine( $engines ){
        $engines[] = [ 'value' => 'deepl', 'label' => __( 'DeepL', 'translatepress-multilingual' ) ];

        return $engines;
    }

    public function add_settings( $settings ){
        $trp                = TRP_Translate_Press::get_trp_instance();
        $machine_translator = $trp->get_component( 'machine_translator' );
        ?>

        <tr>
            <th scope="row"><?php esc_html_e( 'DeepL API Key', 'translatepress-multilingual' ); ?> </th>
            <td>
                <input type="text" id="trp-deepl-key" class="trp-text-input" name="trp_machine_translation_settings[deepl-api-key]" value="<?php if( !empty( $settings['deepl-api-key'] ) ) echo esc_attr( $settings['deepl-api-key']);?>"/>
                <p class="description">
                    <?= wp_kses( sprintf( __( 'Visit <a href="%s" target="_blank">this link</a> to see how you can set up an API key and control API costs.', 'translatepress-multilingual' ), 'https://translatepress.com/docs/addons/deepl-automatic-translation/?utm_source=wpbackend&utm_medium=clientsite&utm_campaign=TRP&utm_content=deepl-api-key#generate-key' ), [ 'a' => [ 'href' => [], 'target'=> [] ] ] ) ?>
                </p>
            </td>

        </tr>

        <?php
    }

    public function sanitize_settings( $settings ){
        if( !empty( $settings['deepl-api-key'] ) )
            $settings['deepl-api-key'] = sanitize_text_field( $settings['deepl-api-key'] );

        return $settings;
    }

    /**
     * Particularities for source language in DeepL API.
     *
     * PT_BR is not treated in the same way as for the target language
     *
     * @param $source_language
     * @param $source_language_code
     * @param $target_language_code
     * @return string
     */
    public function configure_api_source_language($source_language, $source_language_code, $target_language_code ){
        $exceptions_source_mapping_codes = array(
            'zh_HK' => 'zh',
            'zh_TW' => 'zh',
            'zh_CN' => 'zh',
        );
        if ( isset( $exceptions_source_mapping_codes[$source_language_code] ) ){
            $source_language = $exceptions_source_mapping_codes[$source_language_code];
        }

        return $source_language;
    }

    /**
     * Particularities for target language in DeepL API
     *
     * @param $target_language
     * @param $source_language_code
     * @param $target_language_code
     * @return string
     */
    public function configure_api_target_language($target_language, $source_language_code, $target_language_code ){
        $exceptions_target_mapping_codes = array(
                'zh_HK' => 'zh',
                'zh_TW' => 'zh',
                'zh_CN' => 'zh',
                'pt_BR' => 'pt-br'
        );
        if ( isset( $exceptions_target_mapping_codes[$target_language_code] ) ){
            $target_language = $exceptions_target_mapping_codes[$target_language_code];
        }

        return $target_language;
    }
}
