<?php
/**
 * @category    InGuru
 * @package     MagentoGoogleSheetsExport
 * @author      Einars <einars@inguru.lv>
 */

namespace InGuru\MagentoGoogleSheetsExport\Helper;

use Google_Client;
use Google_Service_Sheets;

class GoogleClient
{
    public function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('InGuru Google Sheets Export');
        $client->setScopes([Google_Service_Sheets::SPREADSHEETS]);
        $client->setAuthConfig(BP . '/var/google-credentials.json');
        $client->setAccessType('offline');
        return $client;
    }
}
