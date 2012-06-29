<?php

use Heystack\Subsystem\Core\ServiceStore;

class ProductHolder implements PurchaseableHolderInterface
{
    
    private $stateService;
    
    public function __construct() {
        
        $this->stateService = ServiceStore::getService('state');
        
    }
    
    public function addPurchaseable(PurchaseableInterface $purchaseable)
    {
        
        $this->stateService->setObj($purchaseable->getIdentifier(), $purchaseable);
        
    }
    
    public function getPurchaseable(array $identifier)
    {
        
        return $this->stateService->getObj($identifier);
        
    }
    
    public function getPurchaseables(array $identifiers = null)
    {   
        
        $purchaseables = array();
        
        if (!is_null($identifiers)) {
            
            foreach ($identifiers as $identifier) {
                
                $purchaseables[] = $this->stateService->getObj($identifier);
                
            }      
        
        } else {
            
            $purchaseables = $this->stateService->getByLikeKey('Product');
            
        }
        
        return $purchaseables;
        
    }
    
    public function setPurchaseables(array $purchaseables)
    {
        foreach ($purchaseables as $purchaseable) {
            
            $this->addPurchaseable($purchaseable);
            
        }
    }
    
}