<?php

namespace Doggo\Admin;

use Doggo\Model\Park;
use SilverStripe\Admin\ModelAdmin;

class PhotoModerationAdmin extends ModelAdmin
{
    private static $managed_models = [
        Park::class,
    ];

    private static $menu_title = 'Photo Moderation';

    private static $url_segment = 'photoModeration';

    var $showImportForm = false;
    var $showSearchForm = false;

    public function getList()
    {
        $list = parent::getList();
        $list = $list->filter('UnderModeration', '1');
        return $list;
    }
}
