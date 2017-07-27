<?php

class Magedev_Ordermanager_Block_Adminhtml_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{ 
     public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
		$this->setDefaultFilter(array('is_archieved'=>0));
        $this->setSaveParametersInSession(true);
		$this->setTemplate('ordermanager/grid.phtml');
    }
 
    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'sales/order_grid_collection';
    }

  protected function _prepareLayout()
    {
        if ( $enabledCustomGrid = Mage::getStoreConfig('orderview/general/enabled')) {
		
			if (Mage::getStoreConfig('orderview/general/fixed_header')) {
				$head = $this->getLayout()->getBlock('head');
				if ($head) {
					$this->getLayout()->getBlock('head')->addJs('ordermanager/jquery-1.10.2.min.js');
					$this->getLayout()->getBlock('head')->addJs('ordermanager/freezed/jquery.freezeheader.js');
				}
			}
        }

        return parent::_prepareLayout();
    }
 
    protected function _prepareCollection()
    {
		
		$enabledCustomGrid = Mage::getStoreConfig('orderview/general/enabled');
 		if(isset($enabledCustomGrid) && $enabledCustomGrid == 1)
		{
				$selectedColumns = Mage::getStoreConfig('orderview/general/order_grid_col');
				$selectedColumnsArray = explode(",",$selectedColumns);
			
				$collection = Mage::getResourceModel($this->_getCollectionClass());
		
				$collection->addFilterToMap('increment_id', 'main_table.increment_id');
				$collection->addFilterToMap('store_id', 'main_table.store_id');
				$collection->addFilterToMap('created_at', 'main_table.created_at');
				$collection->addFilterToMap('base_grand_total', 'main_table.base_grand_total');
				$collection->addFilterToMap('grand_total', 'main_table.grand_total');
				$collection->addFilterToMap('status', 'main_table.status');
				$collection->addFilterToMap('delivery_at', 'main_table.delivery_at');
				
				//$collection->addFilterToMap('weight', 'sales_flat_order.weight'); 
				$prefix = Mage::getConfig()->getTablePrefix();
			  
				$collection->join(array('payment'=>'sales/order_payment'),'main_table.entity_id=parent_id','method'); 
				
				$collection->getSelect()->join($prefix.'sales_flat_order_status_history', 'main_table.entity_id = '.$prefix.'sales_flat_order_status_history.parent_id',array('comment'));
				
				$collection->getSelect()->joinLeft($prefix.'sales_flat_shipment_track', 'main_table.entity_id = '.$prefix.'sales_flat_shipment_track.order_id',array('track_number'));
					
				$collection->getSelect()->join($prefix.'sales_flat_order', 'main_table.entity_id = '.$prefix.'sales_flat_order.entity_id',array('shipping_method','customer_email','coupon_code','weight','base_tax_amount','tax_amount','base_discount_amount','discount_amount','base_total_refunded','total_refunded','customer_group_id'));
				
				
			if(in_array('product_detail',$selectedColumnsArray)){
			
				$collection->getSelect()
							->join($prefix.'sales_flat_order_item', 'main_table.entity_id = '.$prefix.'sales_flat_order_item.order_id',array('sku'  => new Zend_Db_Expr('group_concat(DISTINCT `'.$prefix.'sales_flat_order_item`.sku SEPARATOR ", ")'),'name'  => new Zend_Db_Expr('group_concat(DISTINCT `'.$prefix.'sales_flat_order_item`.name SEPARATOR ", ")'),'product_options'  => new Zend_Db_Expr('group_concat(DISTINCT `'.$prefix.'sales_flat_order_item`.product_options SEPARATOR "|| ")'),'qty_ordered','qty_invoiced','qty_shipped','qty_canceled','qty_refunded'))->distinct(true);
			
			}
				
				//$collection->getSelect()->joinLeft(array('sfoas'=>'sales_flat_order_address'),'main_table.entity_id = sfoas.parent_id AND sfoas.address_type="shipping"',array('sfoas.postcode'));
			$collection->getSelect()->joinLeft(array('sfoab'=>$prefix.'sales_flat_order_address'),
		'main_table.entity_id = sfoab.parent_id AND sfoab.address_type="billing"',array('sfoab.street',
		'sfoab.city','sfoab.region','sfoab.postcode','sfoab.telephone','sfoab.company','sfoab.country_id'));
			$collection->getSelect()->joinLeft(array('sfoas'=>$prefix.'sales_flat_order_address'),
		'main_table.entity_id = sfoas.parent_id AND sfoas.address_type="shipping"',array('sfoas.street as shipstreet',
		'sfoas.city as shipcity','sfoas.region as shipregion','sfoas.postcode as shippostcode','sfoas.telephone as shiptelephone','sfoas.company as shipcompany','sfoas.country_id as shipcountry_id'));
			
		 
				// echo $collection->getSelect();die;   	 			   
				$collection->getSelect()->group('main_table.entity_id');
				//$countSelect->reset(Zend_Db_Select::GROUP);
				
				 $this->setCollection($collection);
				 
				return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
		}
		else
		{
			$collection = Mage::getResourceModel($this->_getCollectionClass());
			$this->setCollection($collection);
			return parent::_prepareCollection();
		}		
		
	}
 
    protected function _prepareColumns()
    {	
	
		$enabledCustomGrid = Mage::getStoreConfig('orderview/general/enabled');
 		if(isset($enabledCustomGrid) && $enabledCustomGrid == 1)
		{
			$selectedColumns = Mage::getStoreConfig('orderview/general/order_grid_col');
			$selectedColumnsArray = explode(",",$selectedColumns);
	
	/* Active payment methods*/
			$payments = Mage::getSingleton('payment/config')->getActiveMethods();
			$methods = array();
			foreach ($payments as $paymentCode=>$paymentModel)
			{
				$paymentTitle = Mage::getStoreConfig('payment/'.$paymentCode.'/title');
				$methods[$paymentCode] = $paymentTitle;
			}
	/* Active payment methods ends*/
			$isEnableOrdeMoreFields = Mage::getStoreConfig('orderview/general/enabled'); // check condition for order csv export
			if(isset($isEnableOrdeMoreFields) && $isEnableOrdeMoreFields == 1)
			{
				
				$path = $_SERVER['PATH_INFO'];
				$match = strstr($path, 'exportCsv');
				$match1 = strstr($path, 'exportExcel');
			}
			//echo '<pre>';print_r($selectedColumnsArray);die;
			foreach($selectedColumnsArray as $orderColumn)
			{
				switch ($orderColumn) {
		 
				case 'real_order_id':
					$this->addColumn('real_order_id', array(
						'header'=> Mage::helper('sales')->__('Order #'),
						'width' => '80px',
						'type'  => 'text',
						'index' => 'increment_id',
					));
				break;
				
				case 'store_id':
					if (!Mage::app()->isSingleStoreMode()) {
						$this->addColumn('store_id', array(
							'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
							'index'     => 'store_id',
							'type'      => 'store',
							'store_view'=> true,
							'display_deleted' => true,
						));
					}
				break;
				
				case 'created_at':
					$this->addColumn('created_at', array(
						'header' => Mage::helper('sales')->__('Purchased On'),
						'index' => 'created_at',
						'type' => 'datetime',
						'width' => '100px',
					));
				break;
				
				
				 case 'product_detail':
					$params = Mage::app()->getFrontController()->getAction()->getFullActionName();
				 	//echo '<pre>';print_r($params);die;
			 		if( $params == 'adminhtml_sales_order_exportCsv' || $params == 'adminhtml_sales_order_exportExcel'){}else{

				
						$this->addColumn('name', array(
						'header'    => Mage::helper('catalog')->__('Product Name'),
						'index'     => 'name',
						'type' => 'text',
						'renderer' => new Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Target(), // show products row
						)); 
					}
				break;
			
				case 'product_options':
					$this->addColumn('product_options', array(
						'header'    => Mage::helper('sales')->__('Product Option(s)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'),
						'type'  => 'text',
						'width'     => '1550px',
						'index'     => 'product_options',
						'renderer' => new Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Options()  // added this line
					));
				break;
	
				case 'product_sku':
					$this->addColumn('sku', array(
						'header' => Mage::helper('sales')->__('SKU'),
						'index' => 'sku',
						'type' => 'text',
					));
				break;
				
				case 'qty':
					$this->addColumn('qty', array(
						'header' => Mage::helper('sales')->__('Quantity&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'),
						'index' => 'qty',
						'type' => 'text',
						'filter' => false,
						'sortable' => false,
						'width'     => '150px',
						'renderer' => new Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Quantity()  // added this line
					));
				break;
				
				case 'weight':
					$this->addColumn('weight', array(
						'header' => Mage::helper('sales')->__('Weight'),
						'index' => 'weight',
						'type' => 'number',
						'filter_index' => 'sales_flat_order.weight',
					));
				break;
				
				case 'payment_method':
					$this->addColumn('method', array(
						'header' => Mage::helper('sales')->__('Payment Method'),
						'index' => 'method',
						'type'  => 'options',
						'width' => '70px',
						'options' => $methods,
					));
				break;
				 
			 
				case 'shipping_method':
						$this->addColumn('shipping_method', array(
							'type'  => 'options',
							'options' => $this->getAvailableShippingMethods(),
							'header' => Mage::helper('sales')->__('Shipping Method'),
							'index' => 'shipping_method',
							'align' => 'center',
							'filter_condition_callback' => array($this, '_shippingMethodFilter'),/*by default filter is eq and this method will add %like% filter*/
							'renderer' => new Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Shipping()  // added this line
							));
				break;
	 
							
				case 'customer_email':
						$this->addColumn('customer_email', array(
							'type'  => 'text',
							'header' => Mage::helper('sales')->__('Customer Email'),
							'index' => 'customer_email'
							));
				break;
				
				case 'customer_group':
						$this->addColumn('customer_group_id', array(
							'type'  => 'options',
							'options' => $this->getCustomerGroup(),
							'header' => Mage::helper('sales')->__('Customer Group'),
							'index' => 'customer_group_id'
							));
				break;
					
				case 'coupon_code':
	 
						$this->addColumn('coupon_code', array(
							'type'  => 'text',
							'header' => Mage::helper('sales')->__('Coupon Code'),
							'align' => 'center',
							'index' => 'coupon_code'       
							));
				break;
					
				 case 'billing_name':
					$this->addColumn('billing_name', array(
						'header' => Mage::helper('sales')->__('Bill to Name'),
						'index' => 'billing_name',
					));
				break;
				
				 case 'billing_company':
					$this->addColumn('company', array(
						'header' => Mage::helper('sales')->__('Bill Company'),
						'index' => 'company',
						'filter_index' => 'sfoab.company',	
					));
				break; 
				
				case 'billing_street':
					$this->addColumn('street', array(
						'header' => Mage::helper('sales')->__('Bill Street'),
						'index' => 'street',
						'filter_index' => 'sfoab.street',	
					));
				break;
				
				case 'billing_postcode':
					$this->addColumn('postcode', array(
						'header' => Mage::helper('sales')->__('Bill Postcode'),
						'index' => 'postcode',
						'filter_index' => 'sfoab.postcode',	
					));
				break;
				
				case 'billing_state':
					$this->addColumn('region', array(
						'header' => Mage::helper('sales')->__('Bill Region'),
						'index' => 'region',
						'filter_index' => 'sfoab.region',	
					));
				break;
				
				case 'billing_country':
						$this->addColumn('country_id', array(
							'type'  => 'country',
							'header' => Mage::helper('sales')->__('Bill Country'),
							'index' => 'country_id',
							'filter_index' => 'sfoab.country_id',	
							));
				break;
				
				case 'shipping_name':
					$this->addColumn('shipping_name', array(
						'header' => Mage::helper('sales')->__('Ship to Name'),
						'index' => 'shipping_name',
					));
				 break;
				
				case 'shipping_company':
					$this->addColumn('shipcompany', array(
						'header' => Mage::helper('sales')->__('Ship Company'),
						'index' => 'shipcompany',
						'filter_index' => 'sfoas.company',	
					));
				break;
				
				case 'shipping_street':
					$this->addColumn('shipstreet', array(
						'header' => Mage::helper('sales')->__('Ship Street'),
						'index' => 'shipstreet',
						'filter_index' => 'sfoas.street',	
					));
				break;
				
				case 'shipping_postcode':
					$this->addColumn('shippostcode', array(
						'header' => Mage::helper('sales')->__('Ship Postcode'),
						'index' => 'shippostcode',
						 'filter_index' => 'sfoas.postcode',	
					));
				break;
				
				case 'shipping_state':
					$this->addColumn('shipregion', array(
						'header' => Mage::helper('sales')->__('Ship Region'),
						'index' => 'shipregion',
						'filter_index' => 'sfoas.region',	
					));
				break;
				
				case 'shipping_country':
						$this->addColumn('shipcountry_id', array(
							'type'  => 'country',
							'header' => Mage::helper('sales')->__('Ship Country'),
							'index' => 'shipcountry_id',
							'filter_index' => 'sfoas.country_id',	
							));
				break;
				
				 case 'base_tax_amount':
					$this->addColumn('base_tax_amount', array(
						'header' => Mage::helper('sales')->__('Tax (Base)'),
						'index' => 'base_tax_amount',
						'type'  => 'currency',
						'currency' => 'base_currency_code',
					));
				 break;
				 
				 case 'tax_amount':
					$this->addColumn('tax_amount', array(
						'header' => Mage::helper('sales')->__('Tax (Purchased)'),
						'index' => 'tax_amount',
						'type'  => 'currency',
						'currency' => 'order_currency_code',
					));
				 break;
				 
				 case 'base_discount_amount':
					$this->addColumn('base_discount_amount', array(
						'header' => Mage::helper('sales')->__('Discount (Base)'),
						'index' => 'base_discount_amount',
						'type'  => 'currency',
						'currency' => 'base_currency_code',
					));
				 break;
				 
				 case 'discount_amount':
					$this->addColumn('discount_amount', array(
						'header' => Mage::helper('sales')->__('Discount (Purchased)'),
						'index' => 'discount_amount',
						'type'  => 'currency',
						'currency' => 'order_currency_code',
					));
				 break;
				 
				 case 'base_total_refunded':
					$this->addColumn('base_total_refunded', array(
						'header' => Mage::helper('sales')->__('Total Refunded (Base)'),
						'index' => 'base_total_refunded',
						'type'  => 'currency',
						'currency' => 'base_currency_code',
					));
				 break;
				 
				 case 'total_refunded':
					$this->addColumn('total_refunded', array(
						'header' => Mage::helper('sales')->__('Total Refunded (Purchased)'),
						'index' => 'total_refunded',
						'type'  => 'currency',
						'currency' => 'order_currency_code',
					));
				 break;
				 
				 case 'base_grand_total':
					$this->addColumn('base_grand_total', array(
						'header' => Mage::helper('sales')->__('G.T. (Base)'),
						'index' => 'base_grand_total',
						'type'  => 'currency',
						'currency' => 'base_currency_code',
					));
				 break;
				 
				 case 'grand_total':
					$this->addColumn('grand_total', array(
						'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
						'index' => 'grand_total',
						'type'  => 'currency',
						'currency' => 'order_currency_code',
					));
				 break;
				 
				 case 'status':
					$this->addColumn('status', array(
						'header' => Mage::helper('sales')->__('Status'),
						'index' => 'status',
						'type'  => 'options',
						'width' => '70px',
						'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
					));
				  break;
				  
				 case 'delivery_date':
					$this->addColumn('delivery_at', array(
						'header' => Mage::helper('sales')->__('Delivery At'),
						'index' => 'delivery_at',
						'type'  => 'datetime',
						'width' => '100px',
						'renderer' => new Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Deliveryat(), // show products row
					));
				  break;
	 
				case 'tracking_number':
					$this->addColumn('track_number', array(
				  'header'=> Mage::helper('catalog')->__('Tracking Number'),
				  'index' => 'track_number',
				  //'filter'    => false,
				  //'sortable'  => false,
				 // 'filter_condition_callback' => array($this, '_shippingTrackingFilter'),/* custom add filed is not in DB hence do not filer */
				  'renderer' => new Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Tracking(), // show products row
				));
				break;
				  
				case 'order_type':
					$orderGroups = array('1'=>'Archived','0'=>'Active');		
					$this->addColumn('is_archieved', array(                            
							'type'  => 'options',
							'options' => $orderGroups,
							'header' => Mage::helper('sales')->__('Order Type'), 
							'index'=>'is_archieved',
							'filter_index'=>'main_table.is_archieved', 
							'align' => 'center',
							));
				  break;
				 
				  /*case 'order_invoiced':
					$orderInvoiced = array('1'=>'Yes','0'=>'No');		
					$this->addColumn('total_invoiced', array(                            
							'type'  => 'options',
							'options' => $orderInvoiced,
							'header' => Mage::helper('sales')->__('Invoiced'), 
							'index'=>'total_invoiced',
							 'renderer' => new Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Invoiced(), // show comments row
							'align' => 'center',
							));
				  break;
				  
				  case 'order_refunded':
					$orderRefunded = array('1'=>'Yes','0'=>'No');	
					$this->addColumn('total_refunded', array(                            
							'type'  => 'options',
							'options' => $orderRefunded,
							'header' => Mage::helper('sales')->__('Refunded'), 
							'index'=>'total_refunded',
							'align' => 'center',
							));
				  break;*/
				  
				 case 'is_edited':
					$isEdited = array('1'=>'Yes','0'=>'No');		
					$this->addColumn('is_edit', array(                            
							'type'  => 'options',
							'options' => $isEdited,
							'header' => Mage::helper('sales')->__('Is Edited'), 
							'index'=>'is_edit',
							'filter_index'=>'main_table.is_edit', 
							'align' => 'center',
							));
				  break;	
				  
				  case 'comments':		
					$this->addColumn('comment', array(                            
							'type'  => 'text',
							'header' => Mage::helper('sales')->__('Comment(s)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'), 
							//'width'=>'500px',
							'index'=>'comment',
							//'filter_index'=>'main_table.comments', 
							//'align' => 'center',
							//'column_css_class' =>'width250',
							//'header_css_class'=>'width250',
							 'renderer' => new Magedev_Ordermanager_Block_Adminhtml_Ordermanager_Grid_Renderer_Comments(), // show comments row
							));
				  break;
				  
				  case 'edit_reason':		
					$this->addColumn('edit_comments', array(                            
							'type'  => 'text',
							'header' => Mage::helper('sales')->__('Order Note'), 
							'index'=>'edit_comments',
							'filter_index'=>'main_table.edit_comments', 
							'align' => 'center',
							));
				  break;
						 
				 case 'action':
					if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
						$this->addColumn('action',
							array(
								'header'    => Mage::helper('sales')->__('Action'),
								'width'     => '50px',
								'type'      => 'action',
								'getter'     => 'getId',
								'actions'   => array(
									array(
										'caption' => Mage::helper('sales')->__('View'),
										'url'     => array('base'=>'*/sales_order/view'),
										'field'   => 'order_id'
									)
								),
								'filter'    => false,
								'sortable'  => false,
								'index'     => 'stores',
								'is_system' => true,
						));
					}
					 break;
				}
		}
			 $this->addRssList('rss/order/new', Mage::helper('sales')->__('New Order RSS'));
			$this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
			$this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));
			$this->sortColumnsByOrder();
			return $this;
		
		}else{
				return parent::_prepareColumns();
		}
    }
  
 
    public function getRowUrl($row)
    {
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }
        return false;
    }
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
	
    public function getAvailableShippingMethods() {
        $carriers = Mage::getSingleton('shipping/config')->getActiveCarriers();        
        $methods = array();
		$newCode = '';
        foreach ($carriers as $code=>$carriersModel) {
			
			if($code == 'ups'){$newCode = $code.'_' ; }
			else{$newCode = $code.'_'.$code ; }
			
            $title = Mage::getStoreConfig('carriers/'.$code.'/title');
            if ($title) $methods[$newCode] = $title;
        }
        Mage::register('shipping_methods', $methods);
        //print_r($methods); exit;
        return $methods;
    }
	
	protected function _shippingTrackingFilter($collection, $column)
	{
		if (!$value = $column->getFilter()->getValue()) {
			return $this;
		}
 
        $this->getCollection()->getSelect()->where(
            "sales_flat_order.shipping_method like ?"
        , "$value%"); 
        return $this;
	
	}
	
	protected function _shippingMethodFilter($collection, $column)
    {
		/*Shipping method with options(ups,usps etc) contain custom code after _ so we need to add filter %like% to get the results */
        if (!$value = $column->getFilter()->getValue()) {
            return $this;
        }
 
        $this->getCollection()->getSelect()->where(
            "sales_flat_order.shipping_method like ?"
        , "$value%"); 
        return $this;
    }
	
	 public function getCustomerGroup() {
        return $groups = Mage::getResourceModel('customer/group_collection')
                    ->addFieldToFilter('customer_group_id', array('gt' => 0))
                    ->load()
                    ->toOptionHash();
    }	
}