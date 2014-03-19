#!/usr/bin/php
<?php
/**
 * LICENSE
 *
 * 2012 Quim Blanch
 * threep@gmail.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at

 *
 *	 http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
*/

if ($argc != 3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
    ?>

    This script allows you to check the validity of an EU VAT code against the VIES system

    Usage:
    <?php echo $argv[0]; ?> <country_code> <VAT_number>

    --help, -h, -help, -? Prints out this help
    <country_code> two letter country code (eg. RO, GB).
    <VAT_number> the VAT number of the company

    Example:
    php vies.php GB 786876768

    <?php
} else {
    class VatValidator
    {
        public $response;
        protected $soap;

        // WSDL VIES Url Service.
        protected static $url_vies 
            = 'http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl';

        // Valid european countries ISO codes.
        protected static $european_countries = array(
            'AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 
            'GB', 'GR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 
            'PT', 'RO', 'SE', 'SI', 'SK'
            );

        public function __construct()
        {
            $this->soap = new SoapClient(self::$url_vies);
        }

        /**
        * Check if it's a valid vat number.
        */
        public function checkVat($country, $number)
        {
            $response = array( 'is_valid' => false );
            $vat = $this->prepareVat($country, $number);
            if ($vat) {
                $this->response = array(
                    'is_valid' => $this->soap->checkVat($vat)->valid
                );
            }

            return json_encode($this->response);
        }

        /**
        * Checks that there are all needed params ( Code Country and number );
        */
        protected function prepareVat($country, $number)
        {
            try {
                if ( empty( $country ) || empty( $number ) ) {
                    throw new Exception("Both 'country' and 'number' params are mandatory");
                }

                if ( !in_array($country, self::$european_countries)) {
                    throw new Exception("Invalid country");
                }

                $vat = array(
                    'vatNumber'	=> $number,
                    'countryCode'	=> $country,

                    );

                return $vat;
            } catch ( Exception $e ) {
                $this->response = array( 'error_message' => $e->getMessage() );

                return false;
            }
        }
    }

    // API Call
    $vies = new VatValidator();
    $vies->checkVat(strtoupper($argv[1]), $argv[2]);
    if (array_key_exists('error_response', $vies->response))
        echo $vies->response['error_response'];
    else
        echo $vies->response['is_valid'] ? 'Code is valid' : 'Code is not valid';

    echo "\n";
}
