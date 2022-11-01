<?php

namespace Sunnysideup\CMSNiceties\Forms;

use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\ViewableData;

// use SilverStripe\Forms\GridField\GridFieldArchiveAction;

class CMSNicetiesLinkButton extends ReadonlyField
{
    protected $link = '';

    protected $label = '';

    protected $targetBlank = false;

    /**
     * Creates a new field.
     *
     * @param string                   $name  the internal field name, passed to forms
     * @param null|string|ViewableData $label the human-readable field label
     * @param mixed                    $link  the value of the field
     */
    public function __construct($name, $label = null, $link = null, ?bool $targetBlank = false)
    {
        $title = '🚀';
        $this->link = $link;
        $this->label = $label;
        $this->targetBlank = $targetBlank;

        parent::__construct($name, $title, $link);
    }

    public function Value()
    {
        $target = '';
        if($this->targetBlank) {
            $target = ' target="_blank" rel="noreferrer noopener"';
        }
        return DBField::create_field(
            'HTMLText',
            '<a href="' . $this->link . '" class="btn action btn-outline-primary" '.$target.'>
                <span class="btn__title">' . $this->label . '</span>
            </a>'
        );
    }
}
