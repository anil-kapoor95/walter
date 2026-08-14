<?php
/**
 * Classes for the various AuthorizeNet data types.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */


/**
 * A class that contains all fields for a CIM Customer Profile.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */
class AuthorizeNetCustomer
{
    public $merchantCustomerId;
    public $description;
    public $email;
    public $paymentProfiles = array();
    public $shipToList = array();
    public $customerProfileId;
    
}
 
/**
 * A class that contains all fields for a CIM Address.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */
class AuthorizeNetAddress
{
    public $firstName;
    public $lastName;
	public $address;
	public $zip;
    public $city;
    public $state;
    public $country;
	public $company;
    public $phoneNumber;
    public $faxNumber;
    public $customerAddressId;
}

/**
 * A class that contains all fields for a CIM Payment Profile.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */
class AuthorizeNetPaymentProfile
{
    
    public $customerType;
    public $billTo;
    public $payment;
    public $customerPaymentProfileId;
    
    public function __construct()
    {
        $this->billTo = new AuthorizeNetAddress;
        $this->payment = new AuthorizeNetPayment;
    }

}

/**
 * A class that contains all fields for a CIM Payment Type.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */
class AuthorizeNetPayment
{
    public $creditCard;
    public $bankAccount;
    
    public function __construct()
    {
        $this->creditCard = new AuthorizeNetCreditCard;
        $this->bankAccount = new AuthorizeNetBankAccount;
    }
}

/**
 * A class that contains all fields for a CIM Transaction.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */
class AuthorizeNetTransaction
{
    public $amount;
    public $tax;
    public $shipping;
    public $duty;
    public $lineItems = array();
    public $customerProfileId;
    public $customerPaymentProfileId;
    public $customerShippingAddressId;
    public $creditCardNumberMasked;
    public $bankRoutingNumberMasked;
    public $bankAccountNumberMasked;
    public $order;
    public $taxExempt;
    public $recurringBilling;
    public $cardCode;
    public $splitTenderId;
    public $approvalCode;
    public $transId;
    
    public function __construct()
    {
        $this->tax = (object)array();
        $this->tax->amount = "";
        $this->tax->name = "";
        $this->tax->description = "";
        
        $this->shipping = (object)array();
        $this->shipping->amount = "";
        $this->shipping->name = "";
        $this->shipping->description = "";
        
        $this->duty = (object)array();
        $this->duty->amount = "";
        $this->duty->name = "";
        $this->duty->description = "";
        
        // line items
        
        $this->order = (object)array();
        $this->order->invoiceNumber = "";
        $this->order->description = "";
        $this->order->purchaseOrderNumber = "";
    }
    
}

/**
 * A class that contains all fields for a CIM Transaction Line Item.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */
class AuthorizeNetLineItem
{
    public $itemId;
    public $name;
    public $description;
    public $quantity;
    public $unitPrice;
    public $taxable;

}

/**
 * A class that contains all fields for a CIM Credit Card.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */
class AuthorizeNetCreditCard
{
    public $cardNumber;
    public $expirationDate;
    public $cardCode;
}

/**
 * A class that contains all fields for a CIM Bank Account.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetCIM
 */
class AuthorizeNetBankAccount
{
    public $accountType;
    public $routingNumber;
    public $accountNumber;
    public $nameOnAccount;
    public $echeckType;
    public $bankName;
}

/**
 * A class that contains all fields for an AuthorizeNet ARB Subscription.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetARB
 */
class AuthorizeNet_Subscription
{

    public $name;
    public $intervalLength;
    public $intervalUnit;
    public $startDate;
    public $totalOccurrences;
    public $trialOccurrences;
    public $amount;
    public $trialAmount;
    public $creditCardCardNumber;
    public $creditCardExpirationDate;
    public $creditCardCardCode;
    public $bankAccountAccountType;
    public $bankAccountRoutingNumber;
    public $bankAccountAccountNumber;
    public $bankAccountNameOnAccount;
    public $bankAccountEcheckType;
    public $bankAccountBankName;
    public $orderInvoiceNumber;
    public $orderDescription;
    public $customerId;
    public $customerEmail;
    public $customerPhoneNumber;
    public $customerFaxNumber;
    public $billToFirstName;
    public $billToLastName;
    public $billToAddress;
	public $billToZip;
    public $billToCity;
	public $billToState;
    public $billToCountry;
	public $billToCompany;
    public $shipToFirstName;
    public $shipToLastName;
    public $shipToAddress;
	public $shipToZip;
    public $shipToCity;
    public $shipToState;
    public $shipToCountry;
	public $shipToCompany;
    
    public function getXml()
    {
        $xml = "<subscription>
    <name>{$this->name}</name>
    <paymentSchedule>
        <interval>
            <length>{$this->intervalLength}</length>
            <unit>{$this->intervalUnit}</unit>
        </interval>
        <startDate>{$this->startDate}</startDate>
        <totalOccurrences>{$this->totalOccurrences}</totalOccurrences>
        <trialOccurrences>{$this->trialOccurrences}</trialOccurrences>
    </paymentSchedule>
    <amount>{$this->amount}</amount>
    <trialAmount>{$this->trialAmount}</trialAmount>
    <payment>
        <creditCard>
            <cardNumber>{$this->creditCardCardNumber}</cardNumber>
            <expirationDate>{$this->creditCardExpirationDate}</expirationDate>
            <cardCode>{$this->creditCardCardCode}</cardCode>
        </creditCard>
        <bankAccount>
            <accountType>{$this->bankAccountAccountType}</accountType>
            <routingNumber>{$this->bankAccountRoutingNumber}</routingNumber>
            <accountNumber>{$this->bankAccountAccountNumber}</accountNumber>
            <nameOnAccount>{$this->bankAccountNameOnAccount}</nameOnAccount>
            <echeckType>{$this->bankAccountEcheckType}</echeckType>
            <bankName>{$this->bankAccountBankName}</bankName>
        </bankAccount>
    </payment>
    <order>
        <invoiceNumber>{$this->orderInvoiceNumber}</invoiceNumber>
        <description>{$this->orderDescription}</description>
    </order>
    <customer>
        <id>{$this->customerId}</id>
        <email>{$this->customerEmail}</email>
        <phoneNumber>{$this->customerPhoneNumber}</phoneNumber>
        <faxNumber>{$this->customerFaxNumber}</faxNumber>
    </customer>
    <billTo>
        <firstName>{$this->billToFirstName}</firstName>
        <lastName>{$this->billToLastName}</lastName>
        <address>{$this->billToAddress}</address>
        <zip>{$this->billToZip}</zip>
		<city>{$this->billToCity}</city>
        <state>{$this->billToState}</state>
        <country>{$this->billToCountry}</country>
		<company>{$this->billToCompany}</company>
    </billTo>
    <shipTo>
        <firstName>{$this->shipToFirstName}</firstName>
        <lastName>{$this->shipToLastName}</lastName>
        <address>{$this->shipToAddress}</address>
        <zip>{$this->shipToZip}</zip>
		<city>{$this->shipToCity}</city>
        <state>{$this->shipToState}</state>
        <country>{$this->shipToCountry}</country>
		<company>{$this->shipToCompany}</company>
    </shipTo>
</subscription>";
        
        $xml_clean = "";
        // Remove any blank child elements
        foreach (preg_split("/(\r?\n)/", $xml) as $key => $line) {
            if (!preg_match('/><\//', $line)) {
                $xml_clean .= $line . "\n";
            }
        }
        
        // Remove any blank parent elements
        $element_removed = 1;
        // Recursively repeat if a change is made
        while ($element_removed) {
            $element_removed = 0;
            if (preg_match('/<[a-z]+>[\r?\n]+\s*<\/[a-z]+>/i', $xml_clean)) {
                $xml_clean = preg_replace('/<[a-z]+>[\r?\n]+\s*<\/[a-z]+>/i', '', $xml_clean);
                $element_removed = 1;
            }
        }
        
        // Remove any blank lines
        // $xml_clean = preg_replace('/\r\n[\s]+\r\n/','',$xml_clean);
        return $xml_clean;
    }
}

/**
 * A class that contains all fields for an AuthorizeNet ARB SubscriptionList.
 *
 * @package    AuthorizeNet
 * @subpackage AuthorizeNetARB
 */
class AuthorizeNetGetSubscriptionList
{
    public $searchType;
    public $sorting;
    public $paging;

    public function getXml()
    {
        $emptyString = "";
        $sortingXml = (is_null($this->sorting)) ? $emptyString : $this->sorting->getXml();
        $pagingXml  = (is_null($this->paging))  ? $emptyString : $this->paging->getXml();

        $xml = "
        <searchType>{$this->searchType}</searchType>"
        .$sortingXml
        .$pagingXml
        ;

        $xml_clean = "";
        // Remove any blank child elements
        foreach (preg_split("/(\r?\n)/", $xml) as $key => $line) {
            if (!preg_match('/><\//', $line)) {
                $xml_clean .= $line . "\n";
            }
        }

        // Remove any blank parent elements
        $element_removed = 1;
        // Recursively repeat if a change is made
        while ($element_removed) {
            $element_removed = 0;
            if (preg_match('/<[a-z]+>[\r?\n]+\s*<\/[a-z]+>/i', $xml_clean)) {
                $xml_clean = preg_replace('/<[a-z]+>[\r?\n]+\s*<\/[a-z]+>/i', '', $xml_clean);
                $element_removed = 1;
            }
        }

        // Remove any blank lines
        // $xml_clean = preg_replace('/\r\n[\s]+\r\n/','',$xml_clean);
        return $xml_clean;
    }
}

class AuthorizeNetSubscriptionListPaging
{
    public $limit;
    public $offset;

    public function getXml()
    {
        $xml = "<paging>
            <limit>{$this->limit}</limit>
            <offset>{$this->offset}</offset>
        </paging>";

        return $xml;
    }
}

class AuthorizeNetSubscriptionListSorting
{
    public $orderBy;
    public $orderDescending;

    public function getXml()
    {
        $xml = "
        <sorting>
            <orderBy>{$this->orderBy}</orderBy>
            <orderDescending>{$this->orderDescending}</orderDescending>
        </sorting>";

        return $xml;
    }
}
