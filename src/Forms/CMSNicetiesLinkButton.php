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

    /**
     * Creates a new field.
     *
     * @param string $name The internal field name, passed to forms.
     * @param string|ViewableData|null $label The human-readable field label.
     * @param mixed $link The value of the field.
     */
    public function __construct($name, $label = null, $link = null)
    {
        $title = '🚀';
        $this->link = $link;
        $this->label = $label;

        parent::__construct($name, $title, $link);
    }

    public function Value()
    {
        return DBField::create_field(
            'HTMLText',
            '<a href="' . $this->link . '" class="btn action btn-outline-primary" target="_blank">
                <span class="btn__title">' . $this->label . '</span>
            </a>'
        );
    }
}
