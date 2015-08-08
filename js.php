 <script type="text/javascript">
    Array.prototype.in_array=function(v){
		 var bool=false;
		for(var i=0;i<this.length;i++){
			if(this[i]==v){
				 bool=true;
				 }
		}
		return (bool==true)?(true):(false);
	 };

	 function template(tmpl,data){
         return tmpl.replace(/%(\w*)%/g,function(m,key){return data.hasOwnProperty(key)?data[key]:"";});
     }

	 
	    String.prototype.trim=function(){return this.replace(/^[\s\xa0]+|[\s\xa0]+$/g, '');};
	    String.prototype.isEmpty=function(){
	    	if(this.trim()=='' || this.trim()==null) {
	    		return true;
	    		}
	    		return false;
	    };
	 
	 
    var Pay={
    		isValidExpiry: function(num){
    	    	if(/^\d{2}\/\d{4}$/.test(num)) return true; 
				return false;
        	    },
    	    isValidCVV: function(num){
    	    	if(/^\d{3,4}$/.test(num)) return true; 
				return false;
        	    },
    	    isValidName: function(name){
				if(/^[\sa-z0-9-]+/i.test(name)) return true; 
					return false;
    	     },
    	    isValidFigure: function(fig){
    	    	if (!isNaN(+fig)) return true;
    	    	return false;
    	    	    
        },
    		isValidCreditCard: function(type, ccnum) {
        		   if(type=="JCB") return true;
    			   if (type == "Visa") {
    			      // Visa: length 16, prefix 4, dashes optional.
    			      var re = /^4\d{3}-?\d{4}-?\d{4}-?\d{4}$/;
    			   } else if (type == "Mastercard") {
    			      // Mastercard: length 16, prefix 51-55, dashes optional.
    			      var re = /^5[1-5]\d{2}-?\d{4}-?\d{4}-?\d{4}$/;
    			   } else if (type == "Discover") {
    			      // Discover: length 16, prefix 6011, dashes optional.
    			      var re = /^6011-?\d{4}-?\d{4}-?\d{4}$/;
    			   } else if (type == "AMEX") {
    			      // American Express: length 15, prefix 34 or 37.
    			      var re = /^3[4,7]\d{13}$/;
    			   } else if (type == "Diners") {
    			      // Diners: length 14, prefix 30, 36, or 38.
    			      var re = /^3[0,6,8]\d{12}$/;
    			   }
    			   if (!re.test(ccnum)) return false;
    			   // Remove all dashes for the checksum checks to eliminate negative numbers
    			   ccnum = ccnum.split("-").join("");
    			   // Checksum ("Mod 10")
    			   // Add even digits in even length strings or odd digits in odd length strings.
    			   var checksum = 0;
    			   for (var i=(2-(ccnum.length % 2)); i<=ccnum.length; i+=2) {
    			      checksum += parseInt(ccnum.charAt(i-1));
    			   }
    			   // Analyze odd digits in even length strings or even digits in odd length strings.
    			   for (var i=(ccnum.length % 2) + 1; i<ccnum.length; i+=2) {
    			      var digit = parseInt(ccnum.charAt(i-1)) * 2;
    			      if (digit < 10) { checksum += digit; } else { checksum += (digit-9); }
    			   }
    			   if ((checksum % 10) == 0) return true; else return false;
    			},
    		 cardType: function(number){
    		    // visa
    		    var re = new RegExp("^4");
    		    if (number.match(re) != null)
    		        return "Visa";

    		    // Mastercard
    		    re = new RegExp("^5[1-5]");
    		    if (number.match(re) != null)
    		        return "Mastercard";

    		    // AMEX
    		    re = new RegExp("^3[47]");
    		    if (number.match(re) != null)
    		        return "AMEX";

    		    // Discover
    		    re = new RegExp("^(6011|622(12[6-9]|1[3-9][0-9]|[2-8][0-9]{2}|9[0-1][0-9]|92[0-5]|64[4-9])|65)");
    		    if (number.match(re) != null)
    		        return "Discover";

    		    // Diners
    		    re = new RegExp("^36");
    		    if (number.match(re) != null)
    		        return "Diners";

    		    // Diners - Carte Blanche
    		    re = new RegExp("^30[0-5]");
    		    if (number.match(re) != null)
    		        return "Diners - Carte Blanche";

    		    // JCB
    		    re = new RegExp("^35(2[89]|[3-8][0-9])");
    		    if (number.match(re) != null)
    		        return "JCB";

    		    // Visa Electron
    		    re = new RegExp("^(4026|417500|4508|4844|491(3|7))");
    		    if (number.match(re) != null)
    		        return "Visa Electron";

    		    return "";
    		},
			submit: function(){

				try {
					var transaction_data={},fields=["customer_name","price","currency","cardholder_name","card_number","card_expiry","card_cvv"];

					for (var i=0;i<fields.length;i++){
						ctrl_val=$("[data-hq-name='"+fields[i]+"']").val();
						if(ctrl_val.isEmpty()) throw "All fields are required";
						transaction_data[fields[i]]=ctrl_val;
					}

					transaction_data["card_type"]=this.cardType(transaction_data["card_number"]);

					if(transaction_data["card_type"].isEmpty() || !this.isValidCreditCard(transaction_data["card_type"],transaction_data["card_number"])) throw "The specified card number is invalid";	
					if(!this.isValidFigure(transaction_data["price"].replace(',','')))	throw "Invalid figure specified as price";
					if(!this.isValidName(transaction_data["customer_name"])) throw "The specified customer name contain unsupported characters";
					if(!this.isValidName(transaction_data["cardholder_name"])) throw "The specified cardholder name contain unsupported characters";
					if(!this.isValidCVV(transaction_data["card_cvv"])) throw "Invalid CVV number";
					if(!this.isValidExpiry(transaction_data["card_expiry"])) throw "Card expiration must be numeric <small>(MM/YYYY)</small>";
					 
					//Use paypal gateway
					if(transaction_data["card_type"]=="AMEX" && transaction_data["currency"] != "USD") throw "AMEX is possible to use only for USD";

					 $("div.disable").show();
					 $("button#submit").html("Please wait... transaction in progress");
						
					if(transaction_data["card_type"]=="AMEX" || ["USD","EUR","AUD"].in_array(transaction_data["currency"])){
						transaction_data["gateway"]="paypal";
						$("#pay_iframe").get(0).src="process.php?data="+JSON.stringify(transaction_data);
						transaction_data={};
					}
					else{//Use braintree gateway
						var client = new braintree.api.Client({clientToken: "<?=@$clientToken;?>"});

						client.tokenizeCard({
						  number: transaction_data["card_number"],
						  expirationDate: transaction_data["card_expiry"],
						  cvv: transaction_data["card_cvv"]
						}, function (err, nonce) {
								if(err==null){
									transaction_data["gateway"]="braintree";
									transaction_data["nonce"]=nonce;
							 		// console.log(err,' ',nonce);
					        		$("#pay_iframe").get(0).src="process.php?data="+JSON.stringify(transaction_data);
					        		transaction_data={};
								}
								else{
									throw "An unknown error has occurred";
									}
							
						});
						
						}
					
					
					
				} catch (e) {
					$("div.disable").hide();
					$("#err").show().html(template("<div class='alert alert-warning'>%msg%</div>",{"msg":((e.toString().indexOf('braintree') != -1)?('An unknown error has occurred'):(e))}));
					$("button#submit").html("Pay");
					setTimeout(function(){
						$("#err").hide().html("");
						},10000);
					return false;
				}
				
				
				},
				callback: function(res){
					var data=JSON.parse(res);
					$("div.disable").hide();
					if(data["status"]===true) $("form").get(0).reset();
					$("button#submit").html("Pay");
					$("#err").show().html(template("<div class='alert alert-%type%'>%msg%</div>",{"type":((data["status"]===true)?("success"):("danger")),"msg":data["msg"]}));
					}
    	    };
     
    </script>