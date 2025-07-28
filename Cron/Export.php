<?php
/**
 * @category    InGuru
 * @package     MagentoGoogleSheetsExport
 * @author      Einars <einars@inguru.lv>
 */

namespace InGuru\MagentoGoogleSheetsExport\Cron;

use InGuru\MagentoGoogleSheetsExport\Model\SheetsExporter;
use Psr\Log\LoggerInterface;

class Export
{
    protected $sheetsExporter;
    protected $logger;

    public function __construct(
        SheetsExporter $sheetsExporter,
        LoggerInterface $logger
    ) {
        $this->sheetsExporter = $sheetsExporter;
        $this->logger = $logger;
    }

    public function execute()
    {
        $spreadsheetId = 'YOURSPREADSHEETURL';
        $warehouseCodes = ['VAL', 'RI'];

        try {
            $this->sheetsExporter->export($spreadsheetId, $warehouseCodes);
            $this->logger->info('Google Sheets export: Success');
        } catch (\Exception $e) {
            $this->logger->critical('Google Sheets export FAILED: ' . $e->getMessage());
        }

        return $this;
    }
}
