INSERT INTO CachedPurchasable (ID, Created, LastEdited, Title, UnitPrice, Quantity, Total, Category, ParentID, ProductCode)
  SELECT ID, Created, LastEdited, Title, UnitPrice, Quantity, Total, Category, ParentID, ProductCode FROM CachedProduct;

INSERT INTO CachedPurchasableHolder (ID, Created, LastEdited, Total, NoOfItems, ParentID)
  SELECT ID, Created, LastEdited, Total, NoOfItems, ParentID FROM CachedProductHolder;
