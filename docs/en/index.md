# Ecommerce Purchasable

The ecommerce purchasable module provides a basic implementation of the Purchasable Holder Interface found in the ecommerce core module. It also includes subscribers, events and input/output processors that facilitate the Purchasable Holder's functionality.

## Requirements
* Heystack
* Ecommerce Core

## Purchasable Holder `Heystack\Purchasable\PurchasableHolder\PurchasableHolder`

The Purchasable Holder class implements the Purchasable Holder Interface (`Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface`). The class is a Transaction Modifier whose main function is to keep track of all the purchasables that the end-user has selected for purchase. This class usually acts as a shopping cart.

### Purchasable

The Purchasable Interface (`Heystack\Ecommerce\Purchasable\Interfaces\PurchasableInterface`) is defined in the ecommerce core module but is used extensively in the ecommerce purchasable module. The main purpose of the Purchasable Holder is to keep track of the purchasables involved in the transaction. 

The Purchasable Interface is not implemented in any of the modules. It is left to the developer to implement the interface and customise the class as needed. This is intentional to provide maximum flexibility. It could be a descendant of SiteTree, or DataObject, or both in the same application, or any class for that matter as long as it implements the interface.

### Subscriber and Events

The Purchasable Holder's Subscriber `Heystack\Purchasable\PurchasableHolder\Subscriber` is notified whenever one of the events it subscribes to is dispatched. A method is called that corresponds to each subscribed event.

#### Change Events

Whenever a new purchasable is added, removed or its quantity changed on the Purchasable Holder a corresponding event is dispatched by the Purchasable Holder that tells the Transaction Service to update itself.

When the Currency Services dispatches an event that says the active currency has changed, then the Purchasable Holder is told to updated itself by the Subscriber.

List of Events:

* Purchasable Added
* Purchasable Changed
* Purchasable Removed
* Currency Changed
* Updated

#### Storage Events

There are two events that the Purchasable Holder's Subscriber is listening for:

* Transaction Stored
* Purchasable Holder Stored

The 'Transaction Stored' event triggers the storage of the Purchasable Holder itself. 

Once the Purchasable Holder has been stored and the Subscriber is notified of the event a method is called on the Subscriber that triggers the storage of each 'Purchasable' that is currently on the Purchasable Holder.