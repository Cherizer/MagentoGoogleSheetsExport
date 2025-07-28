<?php
/**
 * @category    InGuru
 * @package     MagentoGoogleSheetsExport
 * @author      Einars <einars@inguru.lv>
 */

namespace InGuru\MagentoGoogleSheetsExport\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magecode\Horizon\Model\ResourceModel\View\CollectionFactory as AvailableInStoresCollectionFactory;
use InGuru\MagentoGoogleSheetsExport\Helper\GoogleClient;
use Google_Service_Sheets;

class SheetsExporter
{
    protected $productRepository;
    protected $searchCriteriaBuilder;
    protected $availableInStoresCollectionFactory;
    protected $googleClient;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        AvailableInStoresCollectionFactory $availableInStoresCollectionFactory,
        GoogleClient $googleClient
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->availableInStoresCollectionFactory = $availableInStoresCollectionFactory;
        $this->googleClient = $googleClient;
    }

    public function export($spreadsheetId, $warehouseCodes = [])
    {
        $criteria = $this->searchCriteriaBuilder
            ->addFilter('export_to_google_sheets', 1)
            ->create();
        $products = $this->productRepository->getList($criteria);

        $productSkus = [];
        foreach ($products->getItems() as $product) {
            $productSkus[] = $product->getSku();
        }

        $collection = $this->availableInStoresCollectionFactory->create();
        if (count($productSkus)) {
            $collection->addFieldToFilter('sku', ['in' => $productSkus]);
        }
        $collection->addFieldToSelect(['sku', 'stock_code', 'qty']);

        $warehouseData = [];
        foreach ($warehouseCodes as $warehouseCode) {
            $warehouseData[$warehouseCode][] = ['SKU', 'Quantity'];
        }

        foreach ($collection->getItems() as $item) {
            $sku = $item->getData('sku');
            $stockCode = $item->getData('stock_code');
            $qty = $item->getData('qty');

            if (isset($warehouseData[$stockCode])) {
                $warehouseData[$stockCode][] = [$sku, $qty];
            }
        }

        $client = $this->googleClient->getClient();
        $service = new Google_Service_Sheets($client);

        foreach ($warehouseData as $warehouseCode => $rows) {
            $body = new \Google_Service_Sheets_ValueRange(['values' => $rows]);
            $range = $warehouseCode . '!A1:B';
            $service->spreadsheets_values->update(
                $spreadsheetId,
                $range,
                $body,
                ['valueInputOption' => 'RAW']
            );
        }
    }
}
