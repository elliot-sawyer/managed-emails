<?php
namespace ElliotSawyer\EmailManagement;
use SilverStripe\Admin\ModelAdmin;

class EmailManagementAdmin extends ModelAdmin
{
    private static $menu_title = 'Email management';
    private static $url_segment = 'emails';
    private static $menu_icon_class = 'font-icon-p-mail';
    private static $managed_models = [
        ManagedEmail::class
    ];

}
