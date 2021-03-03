<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class TRP_Deepl_Machine_Translator extends TRP_Machine_Translator {
    /**
     * Send request to Google Translation API
     *
     * @param string $source_language       Translate from language
     * @param string $language_code         Translate to language
     * @param array $strings_array          Array of string to translate
     *
     * @return array|WP_Error               Response
     */
    public function send_request( $source_language, $language_code, $strings_array ){
        /* build our translation request */
        $translation_request = 'auth_key='.$this->get_api_key();
        $translation_request .= '&source_lang='.$source_language;
        $translation_request .= '&target_lang='.$language_code;
        $translation_request .= '&split_sentences=1';
        foreach( $strings_array as $new_string ){
            $translation_request .= '&text='.rawurlencode(html_entity_decode( $new_string, ENT_QUOTES ));
        }
        $referer = $this->get_referer();

        /* Due to url length restrictions we need so send a POST request faked as a GET request and send the strings in the body of the request and not in the URL */
        $response = wp_remote_post( "https://api.deepl.com/v2/translate", array(
                'method'    => 'POST',
                'timeout'   => 45,
                'headers'   => [
                    'Referer'                => $referer,
                ],
                'body'      => $translation_request,
            )
        );

        return $response;
    }

    /**
     * Returns an array with the API provided translations of the $new_strings array.
     *
     * @param array $new_strings            array with the strings that need translation. The keys are the node number in the DOM so we need to preserve the m
     * @param string $target_language_code  wp language code of the language that we will be translating to. Not equal to the google language code
     * @param string $source_language_code  wp language code of the language that we will be translating from. Not equal to the google language code
     * @return array                        array with the translation strings and the preserved keys or an empty array if something went wrong
     */
    public function translate_array( $new_strings, $target_language_code, $source_language_code = null ){

        if ( $source_language_code == null )
            $source_language_code = $this->settings['default-language'];

        if( empty( $new_strings ) || !$this->verify_request_parameters( $target_language_code, $source_language_code ) )
            return [];

        $translated_strings = [];

        $source_language = apply_filters( 'trp_deepl_source_language', $this->machine_translation_codes[$source_language_code], $source_language_code, $target_language_code );
        $target_language = apply_filters( 'trp_deepl_target_language', $this->machine_translation_codes[$target_language_code], $source_language_code, $target_language_code );

        // split the strings array in 50 string parts;
        $new_strings_chunks = array_chunk( $new_strings, 50, true );

        foreach( $new_strings_chunks as $new_strings_chunk ){
            $response = $this->send_request( $source_language, $target_language, $new_strings_chunk );

            // this runs only if "Log machine translation queries." is set to Yes.
            $this->machine_translator_logger->log([
                'strings'     => serialize( $new_strings_chunk),
                'response'    => serialize( $response ),
                'lang_source' => $source_language,
                'lang_target' => $target_language,
            ]);

            if ( is_array( $response ) && ! is_wp_error( $response ) && isset( $response['response'] ) &&
                isset( $response['response']['code']) && $response['response']['code'] == 200 ) {

                $this->machine_translator_logger->count_towards_quota( $new_strings_chunk );

                $translation_response = json_decode( $response['body'] );

                /* if we have strings build the translation strings array and make sure we keep the original keys from $new_string */
                $translations = ( empty( $translation_response->translations ) )? array() : $translation_response->translations;
                $i            = 0;

                foreach( $new_strings_chunk as $key => $old_string ){

                    if( isset( $translations[$i] ) && ! empty( $translations[$i]->text ) ) {
                        $translated_strings[ $key ] = $translations[ $i ]->text;
                    }else{
                        /*  In some cases when API doesn't have a translation for a particular string,
                        translation is returned empty instead of same string. Setting original string as translation
                        prevents TP from keep trying to submit same string for translation endlessly.  */
                        $translated_strings[ $key ] = $old_string;
                    }

                    $i++;

                }

                if( $this->machine_translator_logger->quota_exceeded() )
                    break;

            }

        }

        return $translated_strings;
    }

    /**
     * Send a test request to verify if the functionality is working
     */
    public function test_request(){

        return $this->send_request( 'en', 'es', [ 'Where are you from ?' ] );

    }

    public function get_api_key(){

        return isset( $this->settings['trp_machine_translation_settings'], $this->settings['trp_machine_translation_settings']['deepl-api-key'] ) ? $this->settings['trp_machine_translation_settings']['deepl-api-key'] : false;

    }
}
