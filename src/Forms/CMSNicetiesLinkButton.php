<?php

namespace Sunnysideup\CMSNiceties\Forms;

use Override;
use SilverStripe\Model\ModelData;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\ORM\FieldType\DBField;

// use SilverStripe\Forms\GridField\GridFieldArchiveAction;

class CMSNicetiesLinkButton extends ReadonlyField
{
    protected $targetBlank = false;

    /**
     * Creates a new field.
     *
     * @param string                   $name  the internal field name, passed to forms
     * @param null|string|ModelData $label the human-readable field label
     * @param mixed                    $link  the value of the field
     */
    public function __construct($name, protected $label = null, protected $link = null, ?bool $targetBlank = false)
    {
        $title = '🚀';
        $this->targetBlank = $targetBlank;

        parent::__construct($name, $title, $this->link);
    }

    #[Override]
    public function getValue()
    {
        $target = '';
        if ($this->targetBlank) {
            $target = ' target="_blank" rel="noreferrer noopener"';
        }

        return DBField::create_field(
            'HTMLText',
            '<a href="' . $this->link . '" class="btn action btn-outline-primary" ' . $target . '>
                <span class="btn__title">' . $this->label . '</span>
            </a>'
        );
    }
}
