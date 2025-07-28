# MagentoGoogleSheetsExport

MagentoGoogleSheetsExport has been tailored for the sole purpose of exporting product SKU's along with the remaining product quanities, which are split into multiple pages each representing a separate warehouse.

For this to work, you need to create a Google Service account in Google Cloud, enable Google Sheets API, and retrieve google-credentials.json file, which you place in your Magento's root var folder (don't mistake it for server's var folder).
In the Magento Admin Dashboard, you need to create an attribute with the id of export_to_google_sheets and Input type Yes/No.

Additionally, you will need to run this terminal command:
composer require google/apiclient:^2.0
