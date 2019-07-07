# Sales project

Test environment: http://104.251.219.141:8000
# The project is developed with:

PHP (Yii2 Framework), Mysql (Database) and Vuejs (Frontend)

How I calculate the prices?

I used stored procedures on Mysql to do it.

# Database:
   
```

    Products: {
        IdProduct,
        Product,
        Description,
        State 
    }

    The idea is to have a price history
    Prices: {
        IdPrice,
        IdProduct,
        Price,
        Date,
        State,
    }

    
    Discounts: {
        IdDiscountm, // Id
        Discount, // Name of the discount
        Description, // A user friendly description.
        Priority, // The discounts are processed by their order priority.
        StartDate, 
        EndDate, // The discount is available from StartDate to EndDate
        Function, // It is the function called to process the discount. "It's the discount type"
        State
    }

    The idea of this table is to have flexibilty by mixing different products in a same discount.
    And a same product can be in more than one discount.
    ProductsDiscount{
        IdProduct,
        IdDiscount
    }
```


## And now. How I did it?

I did a stored procedure called  **nsp_calculate_price**
Where:
The temporary table tmp_products is responsible for storing the purchase discounts
```
DROP TEMPORARY TABLE IF EXISTS tmp_products;
	CREATE TEMPORARY TABLE tmp_products
		(IdProduct int,
		NumberItems int,
		WithPromotion int,
        Price 	decimal(10,2),
        PriceDiscount 	decimal(10,2)
    ) ENGINE = MEMORY;
```
And tmp_discounts is the temporary table that store all available discounts
```
DROP TEMPORARY TABLE IF EXISTS tmp_discounts;
CREATE TEMPORARY TABLE tmp_discounts
    (IdDiscount int,
    Function varchar(45),
    Priority int
    ) ENGINE = MEMORY;
    
INSERT INTO tmp_discounts
SELECT	IdDiscount, Function, Priority
FROM 		Discounts 
WHERE 		State = 'A' AND EndDate IS NULL
ORDER BY Priority ASC;
```

After that, process all discounts by calling their respectives functions.
```
WHILE pIndex < pNumDiscounts DO
		SET pIdDiscount = (SELECT IdDiscount FROM tmp_discounts ORDER BY 1 asc LIMIT 1);
		SET pFunction = (SELECT Function FROM tmp_discounts WHERE IdDiscount = pIdDiscount);
		CALL ssp_eval(CONCAT('CALL ', pFunction, '(', pIdDiscount,');'));        
        DELETE FROM tmp_discounts WHERE IdDiscount = pIdDiscount;
		SET pIndex = pIndex + 1;
END WHILE;
```

e.g Function 2 for 1 **nsp_dis_2_for_1**
```
WHILE pIndex < pNumProd DO
		SET pIdProduct = (SELECT IdProduct FROM tmp_products LIMIT pIndex, 1);
		IF EXISTS (SELECT IdProduct FROM ProductsDiscount WHERE IdProduct = pIdProduct AND IdDiscount = pIdDiscount) THEN
			UPDATE tmp_products 
			SET WithPromotion = (SELECT FLOOR((NumberItems - WithPromotion)/2) * 2), PriceDiscount = (SELECT Price/2) 
			WHERE IdProduct = pIdProduct;
		END IF;
		SET pIndex = pIndex + 1; 
END WHILE;
```

Finally, the formula to calculate the final price with discount is :
```
SUM((NumberItems - WithPromotion) * Price + WithPromotion * PriceDiscount)
Where: 
NumberItems: Item numbers of the same product of the purchase.
WithPromotion: Item numbers with discount of the same product of the purchase.
PriceDiscount is the price of the product with the discount applied.
Price: Price of the product.
```