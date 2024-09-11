<?php
namespace Magenest\FullPageCache\Plugin\InventoryCatalogFrontendUi;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\InventoryCatalogFrontendUi\Model\GetProductQtyLeft;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magenest\FullPageCache\Block\Product\AdditionalInformation;
use Magento\InventorySalesApi\Api\StockResolverInterface;

class GetQtyPlugin
{
    /**
     * @var GetProductSalableQtyInterface
     */
    protected $getProductSalableQty;

    /**
     * @var AdditionalInformation
     */
    protected $additionalInformation;

    /**
     * @var ResultFactory
     */
    private $resultPageFactory;

    /**
     * @var GetProductQtyLeft
     */
    private $productQty;

    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var MessageManagerInterface
     */
    protected $messageManager;

    /**
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param AdditionalInformation $additionalInformation
     * @param ResultFactory $resultPageFactory
     * @param GetProductQtyLeft $productQty
     * @param StockResolverInterface $stockResolver
     * @param MessageManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        GetProductSalableQtyInterface $getProductSalableQty,
        AdditionalInformation $additionalInformation,
        ResultFactory $resultPageFactory,
        GetProductQtyLeft $productQty,
        StockResolverInterface $stockResolver,
        MessageManagerInterface $messageManager,
        RequestInterface $request
    )
    {
        $this->getProductSalableQty = $getProductSalableQty;
        $this->additionalInformation = $additionalInformation;
        $this->resultPageFactory = $resultPageFactory;
        $this->productQty = $productQty;
        $this->stockResolver = $stockResolver;
        $this->messageManager = $messageManager;
        $this->_request = $request;
    }

    /**
     * Around plugin to modify execute method and add current_qty_stock to result JSON
     *
     * @param \Magento\InventoryCatalogFrontendUi\Controller\Product\GetQty $subject
     * @param \Closure $proceed
     * @return ResultInterface
     */
    public function aroundExecute(
        \Magento\InventoryCatalogFrontendUi\Controller\Product\GetQty $subject,
        \Closure $proceed
    ) {
        $sku = $this->_request->getParam('sku');
        $salesChannel = $this->_request->getParam('channel');
        $salesChannelCode = $this->_request->getParam('salesChannelCode');
        $resultJson = $this->resultPageFactory->create(ResultFactory::TYPE_JSON);

        if (!$sku || $salesChannel==null || $salesChannelCode==null) {
            $resultJson->setData(
                [
                    'qty' => null,
                    'current_qty_stock' => null
                ]
            );
        } else {
            try {
                $stockId = $this->stockResolver->execute($salesChannel, $salesChannelCode)->getStockId();
                $qty = $this->productQty->execute($sku, (int)$stockId);
                $currentQtyStock = $this->additionalInformation->getCurrentQty($sku);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $qty = null;
                $currentQtyStock = null;
            }

            $resultJson->setData(
                [
                    'qty' => $qty,
                    'current_qty_stock' => $currentQtyStock
                ]
            );
        }

        return $resultJson;
    }
}
