INSERT INTO CachedPurchasable (ID, ClassName, Created, LastEdited, Title, UnitPrice, Quantity, Total, Category, ParentID, ProductCode)
  SELECT ID, 'StoredPurchasable', Created, LastEdited, Title, UnitPrice, Quantity, Total, Category, ParentID, ProductCode FROM CachedProduct;

INSERT INTO CachedPurchasableHolder (ID, ClassName, Created, LastEdited, Total, NoOfItems, ParentID)
  SELECT ID, 'StoredPurchasableHolder', Created, LastEdited, Total, NoOfItems, ParentID FROM CachedProductHolder;
