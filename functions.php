<?php
    define("BASEURL","BASEURLOFYOURACCOUNT"); //e.g. https://ws-eu1.brightpearl.com/
	define("ACC", "ENTERYOURACCOUNTNAME");
	define("APPID","ENTERYOURAPPID");
	define("TOKEN","ENTERYOURTOKEN");
	define("USEREMAIL","ENTERYOUREMAIL");
	define("PASS","ENTERYOURPASS");
	
	function authenticateBP()
    {
		$authoriseUrl = BASEURL.ACC."/authorise/";
		$appid = APPID;
		$creds = array('emailAddress'=>USEREMAIL, 'password'=>PASS);
		$data = array( 'brightpearl-app-ref'=>$appid, 'apiAccountCredentials'=>$creds);
		
		$data_string = json_encode($data, TRUE);
		$headers = array('Content-Type'=>'application/json','Content-Length:'=>strlen($data_string));
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $authoriseUrl);
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT ,3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',                                                                                
			'Content-Length: ' . strlen($data_string))
		);
		$response = curl_exec($ch);
		
		if(curl_errno($ch)){
			echo 'Curl error: ' . curl_error($ch);
		}
		curl_close ($ch);
		
		unset($data);
		unset($headers);
		
		$authToken = json_decode($response, TRUE);
		$authTokenValue = $authToken['response'];
		
		$_SESSION['auth'] = $authTokenValue;
	}    
    
    //CREATE CUSTOMER
	
	function addCustomerBP($post)
	{
		authenticateBP();
		
		$addressId = addAddressBP($post);
		
		foreach($post as &$value)
		{
			$value = stripslashes($value);
		}
		extract($post);
		unset($value);
		
		$postAddressIds = array( "DEF"=>$addressId, "BIL"=>$addressId, "DEL"=>$addressId);
		$pri_email = array( "email"=>$email );
		$sec_email = array( "email"=>$contact_email );
		$emails = array( "PRI"=>$pri_email, "SEC"=>$sec_email );
		$telephones = array( "PRI"=>$tel, "SEC"=>$contact_tel);
		$communication = array( "emails" => $emails, "telephones"=>$telephones);
		$organisation = array( "name"=>$company);
		$contactRow = array( "firstName"=>$contact_fname, "lastName"=>$contact_sname, "postAddressIds"=>$postAddressIds, "communication"=>$communication, "organisation"=>$organisation);
		
		$url = BASEURL."/public-api/".ACC;
        $get = "/contact-service/contact/";
		
        $data_string = json_encode($contactRow, JSON_FORCE_OBJECT);
        
        $headers = array('Content-Type'=>'application/json','Content-Length:'=>strlen($data_string));
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url.$get);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'brightpearl-auth:  '.$_SESSION['auth'])
        );
        
        $whatthecurlsay = curl_exec($ch);
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close ($ch);
        
        $bporeference = json_decode($whatthecurlsay, TRUE);
        $bporef = $bporeference['response'];
		
		foreach($post as &$value)
		{
			$value = addslashes($value);
		}
		extract($post);
		unset($value);
		
		if (isset($bporef) && isset($bporeference['response']))
		{
			#do some db stuff - i return $result as the insert id
			#add the customer
			return $result;
		}
		else
		{
			echo "AN ERROR HAS OCCURED $whatthecurlsay";
			return 0;
		}
	}
	
	function addManufacturerBP($post)
	{
		authenticateBP();
		
		$addressId = addAddressBP($post);
		
		foreach($post as &$value)
		{
			$value = stripslashes($value);
		}
		extract($post);
		unset($value);
		
		$postAddressIds = array( "DEF"=>$addressId, "BIL"=>$addressId, "DEL"=>$addressId);
		$organisation = array( "name"=>$company);
		$relationshipToAccount = array( "isSupplier"=>true);
		$contactRow = array( "firstName"=>'', "lastName"=>'', "postAddressIds"=>$postAddressIds, "organisation"=>$organisation, "relationshipToAccount"=>$relationshipToAccount);
		
		$url = BASEURL."/public-api/".ACC;
        $get = "/contact-service/contact/";
		
        $data_string = json_encode($contactRow, JSON_FORCE_OBJECT);
        
        $headers = array('Content-Type'=>'application/json','Content-Length:'=>strlen($data_string));
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url.$get);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'brightpearl-auth:  '.$_SESSION['auth'])
        );
        
        $whatthecurlsay = curl_exec($ch);
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close ($ch);
        
        $bporeference = json_decode($whatthecurlsay, TRUE);
		if (isset($bporeference['response']))
		{
			$bporef = $bporeference['response'];
		}
		
		foreach($post as &$value)
		{
			$value = addslashes($value);
		}
		extract($post);
		unset($value);
		
		if (isset($bporef) && isset($bporeference['response']))
		{
			#do some db stuff - i return $result as the insert id7
			#add manuf
			return $result;
		}
		else
		{
			echo "AN ERROR HAS OCCURED $whatthecurlsay";
			exit;
		}
	}
	
	function addProductBP($post)
	{
		authenticateBP();
		
		foreach($post as &$value)
		{
			$value = stripslashes($value);
		}
		extract($post);
		unset($value);		
		
		$taxCode = array("id"=>1, "code"=>"T20");
		$financialDetails = array("taxable"=>true, "taxCode"=>$taxCode);
		$identity = array("sku"=>$modelno);
		
		$categories = array( array( "categoryCode"=>$cat_id) );
		$description = array( "languageCode"=>"en", "text"=>$longdescription, "format"=>"HTML_FRAGMENT");
		$shortDescription = array( "languageCode"=>"en", "text"=>$shortdescription, "format"=>"HTML_FRAGMENT");
		$salesChannels = array( array( "salesChannelName"=>"Brightpearl", "productName"=>"$modelno", "productCondition"=>"new", "categories"=>$categories, "description"=>$description, "shortDescription"=>$shortDescription) );
		$productRow = array( "brandId"=>$manu_id, "productTypeId"=>$type_id, "identity"=>$identity, "financialDetails"=>$financialDetails, "salesChannels"=>$salesChannels);		
		
		$url = BASEURL."/public-api/".ACC;
        $get = "/product-service/product/";
		
        $data_string = json_encode($productRow);
        
        $headers = array('Content-Type'=>'application/json','Content-Length:'=>strlen($data_string));
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url.$get);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'brightpearl-auth:  '.$_SESSION['auth'])
        );
        
        $whatthecurlsay = curl_exec($ch);
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close ($ch);
        
        $bporeference = json_decode($whatthecurlsay, TRUE);
		if (isset($bporeference['response']))
		{
			$bporef = $bporeference['response'];
		}
		
		foreach($post as &$value)
		{
			$value = addslashes($value);
		}
		extract($post);
		unset($value);
		
		if (isset($bporef) && isset($bporeference['response']))
		{
			#do some db stuff - i return $result as the insert id
			#insert the product
			$this_id = $result;
		
			$quantityPrice = array( "1"=>$price );
			$priceList = array( array( "priceListId"=>1, "quantityPrice"=>$quantityPrice ) );
			$priceRow = array( "priceLists"=>$priceList );
			
			$url = BASEURL."/public-api/".ACC;
			$get = "/product-service/product-price/$bporef/price-list";
			
			$data_string = json_encode($priceRow);
			
			$headers = array('Content-Type'=>'application/json','Content-Length:'=>strlen($data_string));
			
			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_URL, $url.$get);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
			curl_setopt($ch, CURLOPT_TIMEOUT, 60);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'Content-Length: ' . strlen($data_string),
				'brightpearl-auth:  '.$_SESSION['auth'])
			);
			
			$whatthecurlsay = curl_exec($ch);
			
			if(curl_errno($ch)){
				echo 'Curl error: ' . curl_error($ch);
			}
			curl_close ($ch);
			
			$bporeference = json_decode($whatthecurlsay, TRUE);
			if (isset($bporeference['response']))
			{
				$bporef = $bporeference['response'];
			}
		
			if ($whatthecurlsay == "{}")
			{
				#do some db stuff - i return $result as the insert id
				#update your price
				return $this_id;
			}
			else
			{
				echo " PRICE IMPORT| AN ERROR HAS OCCURED $whatthecurlsay";
				exit;
			}
			
		}
		else
		{
			echo " PRODUCT IMPORT| AN ERROR HAS OCCURED $whatthecurlsay";
			exit;
		}
	}
	
	//CREATE CUSTOMER ADDRESS IN BP
	
	function addAddressBP($post)
    {		
		foreach($post as &$value)
		{
			$value = stripslashes($value);
		}
		extract($post);
		unset($value);
		
		$addressRow = array( "addressLine1"=>$add1, "addressLine2"=>$add2, "addressLine3"=>$city, "addressLine4"=>$county, "postalCode"=>$pc, "countryIsoCode"=>'GBR');
		
		$url = BASEURL."/public-api/".ACC;
        $get = "/contact-service/postal-address/";		
		
        $data_string = json_encode($addressRow, JSON_FORCE_OBJECT);
        
        $headers = array('Content-Type'=>'application/json','Content-Length:'=>strlen($data_string));
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url.$get);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'brightpearl-auth:  '.$_SESSION['auth'])
        );
        
        $whatthecurlsay = curl_exec($ch);
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close ($ch);
        
        $bporeference = json_decode($whatthecurlsay, TRUE);
        $bporef = $bporeference['response'];
		return $bporef;
	}  
	
	function postOrderBP($orderid)
	{
		authenticateBP();
		
		$url = BASEURL."/public-api/".ACC;
		$get = "/order-service/order/";
		
		#do some db stuff - get your order deets
		$row = mysql_fetch_array($result);
		extract($row);
		
		$orderRef = "DB0".$orderid;
		
		$customer = array( 'contactId'=>$customer_id );
		$parties = array( 'customer'=>$customer );
		
		$current = array( 'channelId' => 27);
		$assignment = array( 'current'=>$current );
		
		$order = array( 'orderTypeCode'=>'SO', 'reference'=> $orderRef, 'parties'=>$parties, 'assignment'=>$assignment);    
		
		$data_string = json_encode($order, JSON_FORCE_OBJECT);
		
		$headers = array('Content-Type'=>'application/json','Content-Length:'=>strlen($data_string));
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, $url.$get);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string),
			'brightpearl-auth:  '.$_SESSION['auth'])
		);
		
		$whatthecurlsay = curl_exec($ch);
		
		if(curl_errno($ch)){
			echo 'Curl error: ' . curl_error($ch);
		}
		curl_close ($ch);
		
		$i = 0;
		
		$bpreference = json_decode($whatthecurlsay, TRUE);
		$bpref = $bpreference['response'];
		#do some db stuff - update the order with brightpear reference
		
		#do some db stuff - get the order items
		while ($row = mysql_fetch_array($result))
		{
			foreach($row as &$value)
			{
				$value = stripslashes($value);
			}
			extract($row);
			unset($value);
			
			if ($product_id != 0)
			{
				#where you have a product in mind
				$quantity = array( 'magnitude'=>$qty );
				$rowNet = array( 'value'=>$linetotal );
				$rowTax = array( 'value'=>($linetotal*0.2) );
				$rowValue = array( 'taxCode'=>'T20', 'rowNet'=>$rowNet, 'rowTax'=>$rowTax );
				
				$orderRow = array( 'productId'=>$product_id, 'quantity'=> $quantity, 'rowValue'=>$rowValue, 'nominalCode'=>'4000');
				
				postOrderRowBP($orderid, $id, $bpref, $orderRow);
			}
			else
			{
				#or just want to put in a description
				$quantity = array( 'magnitude'=>$qty );
				$rowNet = array( 'value'=>$linetotal );
				$rowTax = array( 'value'=>($linetotal*0.2) );
				$rowValue = array( 'taxCode'=>'T20', 'rowNet'=>$rowNet, 'rowTax'=>$rowTax );
				
				$orderRow = array( 'productName'=>'Engineer Labour', 'quantity'=> $quantity, 'rowValue'=>$rowValue, 'nominalCode'=>'4000');
				
				postOrderRowBP($orderid, $id, $bpref, $orderRow);
			}
		}
	}
    
    
    
    //ORDER ROWS
    
    function postOrderRowBP($orderid, $rowid, $bpref, $orderRow)
    {
		authenticateBP();
		
        $url = BASEURL."/public-api/".ACC;
        $get = "/order-service/order/$bpref/row";
        
        $data_string = json_encode($orderRow, JSON_FORCE_OBJECT);
        
        $headers = array('Content-Type'=>'application/json','Content-Length:'=>strlen($data_string));
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url.$get);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
            'brightpearl-auth:  '.$_SESSION['auth'])
        );
        
        $whatthecurlsay = curl_exec($ch);
        
        if(curl_errno($ch)){
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close ($ch);
        
        $bporeference = json_decode($whatthecurlsay, TRUE);
        $bporef = $bporeference['response'];
        #do some db stuff - update the order items with brightpear reference
    }    
?>
