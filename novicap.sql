CREATE DATABASE  IF NOT EXISTS `Novicap` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `Novicap`;
-- MySQL dump 10.13  Distrib 5.7.17, for macos10.12 (x86_64)
--
-- Host: localhost    Database: Novicap
-- ------------------------------------------------------
-- Server version	5.7.26-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Discounts`
--

DROP TABLE IF EXISTS `Discounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Discounts` (
  `IdDiscount` int(11) NOT NULL AUTO_INCREMENT,
  `Discount` varchar(45) NOT NULL,
  `Description` varchar(200) NOT NULL,
  `Priority` int(11) NOT NULL,
  `StartDate` datetime NOT NULL,
  `EndDate` datetime DEFAULT NULL,
  `Function` varchar(45) DEFAULT NULL,
  `State` char(1) NOT NULL,
  PRIMARY KEY (`IdDiscount`),
  UNIQUE KEY `Priority_UNIQUE` (`Priority`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Prices`
--

DROP TABLE IF EXISTS `Prices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Prices` (
  `IdPrice` int(11) NOT NULL AUTO_INCREMENT,
  `IdProduct` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Date` datetime NOT NULL,
  `State` char(1) NOT NULL,
  PRIMARY KEY (`IdPrice`),
  KEY `FK_Prices_Products_idx` (`IdProduct`),
  CONSTRAINT `FK_Prices_Products` FOREIGN KEY (`IdProduct`) REFERENCES `Products` (`IdProduct`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `Products`
--

DROP TABLE IF EXISTS `Products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Products` (
  `IdProduct` int(11) NOT NULL,
  `Product` varchar(45) NOT NULL,
  `Description` varchar(200) NOT NULL,
  `State` char(1) NOT NULL,
  PRIMARY KEY (`IdProduct`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ProductsDiscount`
--

DROP TABLE IF EXISTS `ProductsDiscount`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProductsDiscount` (
  `IdProduct` int(11) NOT NULL,
  `IdDiscount` int(11) NOT NULL,
  PRIMARY KEY (`IdProduct`,`IdDiscount`),
  KEY `FK_ProductsDiscount_Discounts_idx` (`IdDiscount`),
  CONSTRAINT `FK_ProductsDiscount_Discounts` FOREIGN KEY (`IdDiscount`) REFERENCES `Discounts` (`IdDiscount`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_ProductsDiscount_Products` FOREIGN KEY (`IdProduct`) REFERENCES `Products` (`IdProduct`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping events for database 'Novicap'
--

--
-- Dumping routines for database 'Novicap'
--
/*!50003 DROP FUNCTION IF EXISTS `f_dis_2_for_1` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `f_dis_2_for_1`(pIdDiscount int) RETURNS int(11)
BEGIN
	DECLARE pNumProd, pIndex, pIdProduct int;
    SET pIndex = 0;
    SET pNumProd = (SELECT COUNT(IdProduct) FROM tmp_products);
    
	WHILE pIndex < pNumProd DO
		SET pIdProduct = (SELECT IdProduct FROM tmp_products LIMIT pIndex, 1);
		IF EXISTS (SELECT IdProduct FROM ProductsDiscount WHERE IdProduct = pIdProduct AND IdDiscount = pIdDiscount) THEN
			UPDATE tmp_products 
			SET WithPromotion = (SELECT FLOOR((NumberItems - WithPromotion)/2) * 2), PriceDiscount = (SELECT Price/2) 
			WHERE IdProduct = pIdProduct;
		END IF;
		SET pIndex = pIndex + 1; 
	END WHILE;
	-- UPDATE tmp_products SET WithPromotion = 2;
RETURN 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `f_dis_3_same_prod` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `f_dis_3_same_prod`(pIdDiscount int) RETURNS int(11)
BEGIN
	DECLARE pNumProd, pIndex, pIdProduct, pNumber int;
    SET pIndex = 0;
    SET pNumProd = (SELECT COUNT(IdProduct) FROM tmp_products);
    
	WHILE pIndex < pNumProd DO
		SET pIdProduct = (SELECT IdProduct FROM tmp_products LIMIT pIndex, 1);
        SET pNumber = (SELECT NumberItems - WithPromotion FROM tmp_products WHERE IdProduct = pIdProduct);
		IF EXISTS (SELECT IdProduct FROM ProductsDiscount WHERE IdProduct = pIdProduct AND IdDiscount = pIdDiscount AND pNumber >= 3) THEN
			UPDATE tmp_products 
			SET WithPromotion = pNumber, PriceDiscount = Price * 0.95
			WHERE IdProduct = pIdProduct;
		END IF;
		SET pIndex = pIndex + 1; 
	END WHILE;
	RETURN 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `nsp_calculate_price` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `nsp_calculate_price`(pProducts json)
EXT:BEGIN
/*
	Calculate the final price (with discounts if exists) of the purchase.
*/
	DECLARE pIndex, pIdProduct, pNumber, pNumProd, pNumDiscounts, pIdDiscount int;
    DECLARE pPrice, pFinalPrice, pDiscountPrice decimal(10,2);
    DECLARE pFunction varchar(45);
    -- Handler error
	 DECLARE EXIT HANDLER FOR SQLEXCEPTION
	 BEGIN
		ROLLBACK;
	END;
    /*
		tmp_products is used to calculate the final price.
        The formula to calculate the final price with discount is :
        SUM((NumberItems - WithPromotion) * Price + WithPromotion * PriceDiscount)
        Where: 
        NumberItems: Item numbers of the same product of the purchase.
        WithPromotion: Item numbers with discount of the same product of the purchase.
        PriceDiscount is the price of the product with the discount applied.
        Price: Price of the product.
    */
    DROP TEMPORARY TABLE IF EXISTS tmp_products;
	CREATE TEMPORARY TABLE tmp_products
		(IdProduct int,
		NumberItems int,
		WithPromotion int,
        Price 	decimal(10,2),
        PriceDiscount 	decimal(10,2)
		) ENGINE = MEMORY;
        
    /*
		Every discount active with State = 'A' (active) and EndDate null in the system.
        It's sorted by the Priority. So the discounts will be applied by this sort.
    */
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
                
    SET pNumProd = (SELECT JSON_LENGTH(pProducts->'$.Products'));
    SET pIndex = 0;
    /*
		In this while, I'm inserting all of the products with their respectives prices and numbers in tmp_products. .
        This temporary table will be used in the future to be sended to the discount stored procedures.
    */
    WHILE pIndex < pNumProd DO
		SET pIdProduct = JSON_EXTRACT(pProducts, CONCAT('$.Products[', pIndex, '].IdProduct'));
        IF pIdProduct > 0 THEN
			SET pNumber = JSON_EXTRACT(pProducts, CONCAT('$.Products[', pIndex, '].Number'));
			SET pPrice = ( SELECT Price from Prices where IdProduct = pIdProduct AND State = 'A');
			INSERT INTO tmp_products
			SELECT pIdProduct, pNumber, 0, pPrice, pPrice;
        END IF;
        SET pIndex = pIndex + 1;
    END WHILE;
    SET pNumDiscounts = (SELECT COUNT(IdDiscount) FROM tmp_discounts);
    SET pIndex = 0;
    /*
		Loop through the tmp_discounts table and call every stored procedure stored in Function column in the Discounts Table.
    */
    WHILE pIndex < pNumDiscounts DO
		SET pIdDiscount = (SELECT IdDiscount FROM tmp_discounts ORDER BY 1 asc LIMIT 1);
		SET pFunction = (SELECT Function FROM tmp_discounts WHERE IdDiscount = pIdDiscount);
		CALL ssp_eval(CONCAT('CALL ', pFunction, '(', pIdDiscount,');'));        
        DELETE FROM tmp_discounts WHERE IdDiscount = pIdDiscount;
		SET pIndex = pIndex + 1;
    END WHILE;
    SET pFinalPrice = (SELECT SUM((NumberItems - WithPromotion) * Price + WithPromotion * PriceDiscount) as PrecioFinal FROM tmp_products);
    SET pDiscountPrice = ((SELECT SUM(NumberItems * Price) FROM tmp_products) - pFinalPrice);
    SET pProducts = JSON_SET(pProducts, 
							'$.FinalPrice', pFinalPrice,
                            '$.Discounts', pDiscountPrice);
    SELECT pProducts Sale;
	DROP TEMPORARY TABLE IF EXISTS tmp_products;
	DROP TEMPORARY TABLE IF EXISTS tmp_discounts;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `nsp_disable_product` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `nsp_disable_product`(pIdProduct int)
EXT:BEGIN
/*
	Update the state of the product to D (Disabled).
*/
	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		SELECT 0 Id, 'Error in the transaction. Contact the administrator.' Message;
        ROLLBACK;
	END;
    IF pIdProduct = '' OR pIdProduct IS NULL THEN
		SELECT 0 Id, 'Error in the transaction. Contact the administrator' Message;
        LEAVE EXT;
    END IF;
	START TRANSACTION;
		UPDATE Products SET State = 'D' WHERE IdProduct = pIdProduct;
        SELECT pIdProduct Id, 'Product deleted succesfully' Message;
    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `nsp_dis_2_for_1` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `nsp_dis_2_for_1`(pIdDiscount int)
BEGIN
/*
	Calculates the discount of 2 items for 1.
*/
	DECLARE pNumProd, pIndex, pIdProduct int;
    SET pIndex = 0;
    SET pNumProd = (SELECT COUNT(IdProduct) FROM tmp_products);
    
	WHILE pIndex < pNumProd DO
		SET pIdProduct = (SELECT IdProduct FROM tmp_products LIMIT pIndex, 1);
		IF EXISTS (SELECT IdProduct FROM ProductsDiscount WHERE IdProduct = pIdProduct AND IdDiscount = pIdDiscount) THEN
			UPDATE tmp_products 
			SET WithPromotion = (SELECT FLOOR((NumberItems - WithPromotion)/2) * 2), PriceDiscount = (SELECT Price/2) 
			WHERE IdProduct = pIdProduct;
		END IF;
		SET pIndex = pIndex + 1; 
	END WHILE;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `nsp_dis_3_same_prod` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `nsp_dis_3_same_prod`(pIdDiscount int)
BEGIN
/*
	Calculates the discount of 3 items or more of the same product.
*/
	DECLARE pNumProd, pIndex, pIdProduct, pNumber int;
    SET pIndex = 0;
    SET pNumProd = (SELECT COUNT(IdProduct) FROM tmp_products);
    
	WHILE pIndex < pNumProd DO
		SET pIdProduct = (SELECT IdProduct FROM tmp_products LIMIT pIndex, 1);
        SET pNumber = (SELECT NumberItems - WithPromotion FROM tmp_products WHERE IdProduct = pIdProduct);
		IF EXISTS (SELECT IdProduct FROM ProductsDiscount WHERE IdProduct = pIdProduct AND IdDiscount = pIdDiscount AND pNumber >= 3) THEN
			UPDATE tmp_products 
			SET WithPromotion = pNumber, PriceDiscount = Price * 0.95
			WHERE IdProduct = pIdProduct;
		END IF;
		SET pIndex = pIndex + 1; 
	END WHILE;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `nsp_get_product` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `nsp_get_product`(pIdProduct int)
BEGIN
/*
	Returns a product with the currently price
*/
	SELECT pro.*, pri.Price
    FROM Products pro
    INNER JOIN Prices pri USING(IdProduct)
    WHERE pro.IdProduct = pIdProduct AND pri.State = 'A';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `nsp_list_products` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `nsp_list_products`()
BEGIN
/*
	List every product with state = 'A' (Active) and their currently prices.
*/
	SELECT pro.*,  pri.Price
    FROM Products pro
    INNER JOIN Prices pri USING(IdProduct)
    WHERE pro.State = 'A' AND pri.State = 'A';
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `nsp_new_product` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `nsp_new_product`(pProduct varchar(45), pDescription varchar(200), pPrice decimal(10,2))
EXT:BEGIN
	/*
		Add a new product. pProduct, pDescription, pPrice can not be null. 
        Controls that not exists another product with the same name.
    */
	DECLARE pIdProduct int;

	DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
		SELECT 0 Id, 'Error in the transaction. Contact the administrator.' Message;
        ROLLBACK;
	END;
    IF pProduct = '' OR pProduct IS NULL THEN
		SELECT 0 Id, 'Product can not be null' Message;
        LEAVE EXT;
    END IF;
    
    IF pDescription = '' OR pDescription IS NULL THEN
		SELECT 0 Id, 'Description can not be null' Message;
        LEAVE EXT;
    END IF;
    
    IF pPrice = '' OR pPrice IS NULL THEN
		SELECT 0 Id, 'Price can not be null' Message;
        LEAVE EXT;
    END IF;
    
    IF EXISTS (SELECT IdProduct FROM Products WHERE Product = pProduct) THEN
		SELECT 0 Id, 'Other product has the same name.' Message;
        LEAVE EXT;
    END IF;
    START TRANSACTION;
		SET pIdProduct = 1 +(SELECT COALESCE(MAX(IdProduct),0) FROM Products);
		INSERT INTO Products VALUES(pIdProduct, pProduct, pDescription, 'A');
        INSERT INTO Prices VALUES(0,pIdProduct, pPrice, NOW(),'A');
        SELECT  pIdProduct Id, 'Product created succesfully.' Message;
    COMMIT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ssp_eval` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ssp_eval`(pCadena mediumtext)
BEGIN
	
	SET @Cadena = pCadena;
    PREPARE STMT FROM @Cadena;
    EXECUTE STMT;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-07-07 12:55:20
