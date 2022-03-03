<?php

namespace Doggo\Model;

use JsonSerializable;
use SilverStripe\ORM\DataObject;
use Silverstripe\Assets\Image;

class Park extends DataObject implements JsonSerializable
{
    private static $table_name = 'Park';

    private static $db = [
        'Title' => 'Varchar',
        'Latitude' => 'Decimal(9,6)',
        'Longitude' => 'Decimal(9,6)',
        'Notes' => 'Text',
        'Provider' => "Enum(array('Wellington City Council', 'Palmerston North City Council'))",
        'ProviderCode' => 'Varchar(100)',
        'GeoJson' => 'Text',
        'FeatureOnOffLeash' => "Enum(array('On-leash', 'Off-leash'), 'On-leash')",
        'IsToPurge' => 'Boolean',
    ];

    private static $has_one = [
        'Photo' => Image::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
    ];

    private static $indexes = [
        'Provider' => [
            'columns' => ['Provider'],
        ],
        'ProviderCode' => [
            'columns' => ['Provider', 'ProviderCode'],
        ],
    ];

    private static $default_sort = "Title";

    public function validate()
    {
        $validate = parent::validate();

        if (empty($this->Title)) {
            $validate->addFieldError('Title', 'Title is required');
        }

        return $validate;
    }

    public function thumbnail()
    {
        //echo '<pre>';
        //print_r($this->Photo);
        //exit();
        //return '<img src="' . $this->Photo->FitMax(400,400)  . '" alt="'. $this->Photo->Title .'" />';
        return $this->Photo->FitMax(400,400) . '';
    }

    public function jsonSerialize()
    {
        return [
            'ID' => $this->ID,
            'Title' => $this->Title,
            'Latitude' => $this->Latitude,
            'Longitude' => $this->Longitude,
            'Notes' => $this->Notes,
            'Provider' => $this->Provider,
            'ProviderCode' => $this->ProviderCode,
            'GeoJson' => $this->GeoJson,
            'FeatureOnOffLeash' => $this->FeatureOnOffLeash,
            'Photo' => $this->thumbnail(),
            'LastEdited' => $this->LastEdited,
            'Created' => $this->Created,
        ];
    }
}
