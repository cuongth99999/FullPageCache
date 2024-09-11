<?php

namespace Magenest\FullPageCache\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\RequestInterface;
use Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite;
use Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;

class AdditionalInformation extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite
     */
    protected GetStockIdForCurrentWebsite                     $getStockIdForCurrentWebsite;
    /**
     * @var \Magento\InventoryReservationsApi\Model\GetReservationsQuantityInterface
     */
    protected GetReservationsQuantityInterface                $getReservationsQuantity;
    /**
     * @var \Magenest\Marketplace\Helper\Data
     */
    protected \Magenest\Marketplace\Helper\Data               $helper;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected RequestInterface                                $request;

    /**
     * @var StockItemRepository
     */
    protected $stockItemRepository;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite,
        GetReservationsQuantityInterface $getReservationsQuantity,
        RequestInterface $request,
        StockItemRepository $stockItemRepository,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
        $this->resourceConnection = $resourceConnection;
        $this->getStockIdForCurrentWebsite = $getStockIdForCurrentWebsite;
        $this->getReservationsQuantity = $getReservationsQuantity;
        $this->request = $request;
        $this->stockItemRepository = $stockItemRepository;
    }

    /**
     * @return false|mixed
     */
    public function getCurrentQty($sku = null)
    {
        if ($sku) {
            $product = $this->productRepository->get($sku);
        } else {
            $product = $this->getCurrentProduct();
        }
        $productStock = $this->stockItemRepository->get($product->getId());
        return $productStock->getQty();
    }

    /**
     * @return \Magento\Catalog\Model\Product|mixed
     */
    protected function getCurrentProduct()
    {
        $product = $this->getProduct();
        if ($product->getTypeId() == 'configurable') {
            $childProducts = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($childProducts as $childProduct) {
                if (!$product->isSalable()) {
                    return $childProduct;
                }
                if (!$childProduct->isSalable()) {
                    continue;
                }
                return $childProduct;
            }
        }
        return $product;
    }
}
