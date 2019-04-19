<?php
namespace ElliotSawyer\EmailManagement;

use SilverStripe\Control\Email\Email;
use SilverStripe\ORM\DataObject;

class ManagedEmailAddress extends DataObject {
    private static $db = [
        'Address' => 'Varchar(255)',
        'Name' => 'Varchar(255)',
        'TypeField' => "Enum('To,CC,BCC','BCC')"
    ];

    private static $has_one = [
        'ManagedEmail' => ManagedEmail::class,
    ];

    private static $summary_fields = ['Name', 'Address', 'TypeField'];

    public function validate()
    {
        $valid = parent::validate();

        if ($this->Address) {
            if (!Email::is_valid_address($this->Address)) {
                $valid->addError("Email address is not valid: ".$this->Address);
            }

        }
        return $valid;
    }

    public function getFullEmailAddress()
    {
        if ($this->Name) {
            return sprintf("%s<%s>", $this->Name, $this->Address);
        }

        return $this->Address;
    }
}
