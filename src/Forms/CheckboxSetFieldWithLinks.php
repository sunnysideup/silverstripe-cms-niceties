<?php

namespace Sunnysideup\CMSNiceties\Forms;

use Override;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\ORM\FieldType\DBField;

class CheckboxSetFieldWithLinks extends CheckboxSetField
{
    protected $classNameForLinks = '';

    protected $isReact = '';

    protected $linksPerOption = [];

    #[Override]
    public function getTemplate()
    {
        return self::class;
    }

    public function setClassNameForLinks(string $s): self
    {
        $this->classNameForLinks = $s;

        return $this;
    }

    public function getClassNameForLinks(): string
    {
        return $this->classNameForLinks;
    }

    public function setIsReact(bool $b): self
    {
        $this->isReact = $b;

        return $this;
    }

    public function getIsReact(): bool
    {
        return $this->isReact;
    }

    /**
     * Gets the list of options to render in this formfield.
     *
     * @return ArrayList
     */
    #[Override]
    public function getOptions()
    {
        $options = parent::getOptions();
        $className = $this->getClassNameForLinks();
        if ($className !== '' && $className !== '0') {
            foreach ($options as $option) {
                $obj = $className::get_by_id($option->Value);
                if ($obj && $obj->hasMethod('CMSEditLink')) {
                    $link = $obj->CMSEditLink();
                    if ($option->isChecked) {
                        $this->linksPerOption[] = '<a href="' . $link . '">' . $option->Title . '</a>';
                    }

                    $option->setField('Link', $link);
                    $option->setField(
                        'Title',
                        DBField::create_field('HTMLText', '<a href="' . $link . '">' . $option->Title . '</a>')
                    );
                }
            }
        }

        return $options;
    }

    public function getLinksPerOption(): array
    {
        return $this->linksPerOption;
    }

    public function getLinksPerOptionAsString(): string
    {
        return implode(', ', $this->linksPerOption);
    }

    #[Override]
    public function Type()
    {
        return 'optionset checkboxset checkboxsetwithlinks';
    }

    #[Override]
    public function getDescription()
    {
        $this->getOptions();
        if ($this->isReact) {
            $this->description .= 'Quick Edit: <br />' . $this->getLinksPerOptionAsString();
        }

        return $this->description;
    }
}
