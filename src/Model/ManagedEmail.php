<?php
namespace ElliotSawyer\EmailManagement;
use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Control\Email\Email;
use SilverStripe\View\SSViewer;
use SilverStripe\View\ArrayData;

class ManagedEmail extends DataObject
{
    private static $default_from_address = 'do-not-reply@example.com';
    private static $minimum_body_length = 7;

    private static $db = [
        'FromAddress' => 'Varchar(255)',
        'Subject' => 'Varchar(255)',
        'Body' => 'HTMLText'
    ];
    private static $has_many = [
        'OtherAddresses' => ManagedEmailAddress::class,
    ];

    private static $summary_fields = ['Subject'];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $addressConfig = GridFieldConfig::create()
            ->addComponent(new GridFieldButtonRow('before'))
            ->addComponent(new GridFieldToolbarHeader())
            ->addComponent(new GridFieldTitleHeader())
            ->addComponent(new GridFieldEditableColumns())
            ->addComponent(new GridFieldDeleteAction())
            ->addComponent(new GridFieldAddNewInlineButton());

        $messageField = LiteralField::create(
            'RecipientsMessage',
            sprintf(
                "<div class=\"alert alert-info\">%s</div>",
                'These recipients will receive email in addition to any user-supplied ones.'
            )
        );
        $othersField = GridField::create('OtherAddresses','Other addresses',$this->OtherAddresses(),$addressConfig);

        $fields->dataFieldByName('FromAddress')
            ->setDescription('If left blank, this will default to ' . $this->config()->default_from_address);

        $fields->dataFieldByName('Body')
            ->setDescription('Your emails can have data passed into them as an'
            . ' array or a DataObject. You can utilise these variables in your '
            .' templates like this: '
            .' {$Member.FirstName} sent ${$AmountNZD} to {$BankAccount}.');

        $fields->addFieldsToTab('Root.OtherAddresses', [
            $messageField, $othersField
        ]);

        return $fields;
    }

    public function validate()
    {
        $valid = parent::validate();

        if (!$this->Subject) {
            $valid->addError('\'Subject\' is a required field');
        }

        if( !$this->Body || strlen($this->Body) < $this->config()->minimum_body_length) {
            $valid->addError('\'Body\' is missing or too short to be valid HTML');
        }

        if ($this->FromAddress && !Email::is_valid_address($this->FromAddress)) {
            $valid->addError('\'FromAddress\' is not a valid email address');
        }

        return $valid;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->FromAddress) {
            $this->FromAddress = $this->config()->default_from_address;
        }
    }

    public function send($toAddress, $data = [])
    {
        $email = Email::create();
        $email->addTo($toAddress);
        $email->addFrom($this->FromAddress);
        $email->setSender($this->FromAddress);
        $email->setReturnPath($this->FromAddress);
        $email->Subject = $this->Subject;
        $email->Body = SSViewer::execute_string($this->Body, ArrayData::create($data));

        foreach($this->OtherAddresses() as $otherEmailAddress) {
            if ($otherEmailAddress->TypeField == 'To') {
                $email->addTo($otherEmailAddress->Address, $otherEmailAddress->Name);
            }

            if ($otherEmailAddress->TypeField == 'CC') {
                $email->addCC($otherEmailAddress->Address, $otherEmailAddress->Name);
            }

            if ($otherEmailAddress->TypeField == 'BCC') {
                $email->addBCC($otherEmailAddress->Address, $otherEmailAddress->Name);
            }

        }

        $email->send();

    }
}
