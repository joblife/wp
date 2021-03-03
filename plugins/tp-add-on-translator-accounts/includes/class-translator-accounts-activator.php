<?php
/**
 * Fired during plugin activation
 *
 * @link       https://translatepress.com
 * @since      1.0.0
 *
 * @package    TranslatePress - Translator Accounts Add-on
 * @subpackage TranslatePress - Translator Accounts Add-on/includes
 */
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    TranslatePress - Translator Accounts Add-on
 * @subpackage TranslatePress - Translator Accounts Add-on/includes
 * @author     Cristian Antohe
 */
class TRP_Translator_Accounts_Activator {
    /**
     * Create the translator user role.
     *
     * The translator user role is similar to a subscriber, with the extra capabilities of translate_strings and upload_files.
     *
     * @since    1.0.0
     */
    public static function activate() {
        $role = get_role( 'translator' );

        if ( $role ){
            $role->add_cap( 'translate_strings' );
            $role->add_cap( 'upload_files' );
        } else {
            add_role(
                'translator',
                __( 'Translator' ),
                array(
                    'read' => true,
                    'translate_strings' => true,
                    'upload_files' => true,
                )
            );
        }
    }

}