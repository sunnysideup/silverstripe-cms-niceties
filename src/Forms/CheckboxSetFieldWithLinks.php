<?php

namespace Sunnysideup\CMSNiceties\Forms;

use SilverStripe\Forms\CheckboxSetField;

use SilverStripe\ORM\ArrayList;

use SilverStripe\ORM\FieldType\DBField;

class CheckboxSetFieldWithLinks extends CheckboxSetField
{

    public function getTemplate()
    {
        return self::class;
    }

    protected $classNameForLinks = '';

    public function setClassNameForLinks(string $s) : self
    {
        $this->classNameForLinks = $s;

        return $this;
    }

    public function getClassNameForLinks() : string
    {
        return $this->classNameForLinks;
    }

    protected $isReact = '';

    public function setIsReact(bool $b) : self
    {
        $this->isReact = $b;

        return $this;
    }

    public function getIsReact() : bool
    {
        return $this->isReact;
    }

    protected $linksPerOption = [];

    /**
     * Gets the list of options to render in this formfield
     *
     * @return ArrayList
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        $className = $this->getClassNameForLinks();
        if ($className) {
            foreach($options as $key => $option)  {
                $obj = $className::get()->byID($option->Value);
                if($obj && $obj->hasMethod('CMSEditLink')) {
                    $link = $obj->CMSEditLink();
                    if($option->isChecked) {
                        $this->linksPerOption[] = '<a href="'.$link.'">'.$option->Title.'</a>';
                    }
                    $option->setField('Link', $link);
                    $option->setField(
                        'Title',
                        DBField::create_field('HTMLText', '<a href="'.$link.'">'.$option->Title.'</a>')
                    );
                }
            }
        }
        return $options;
    }

    public function getLinksPerOption() : array
    {
        return $this->linksPerOption;
    }

    public function getLinksPerOptionAsString() : string
    {
        return implode(', ', $this->linksPerOption);
    }

    public function Type()
    {
        return 'optionset checkboxset checkboxsetwithlinks';
    }

    public function getDescription()
    {
        $this->getOptions();
        if($this->isReact) {
            $this->description .= 'Quick Edit: <br />'.$this->getLinksPerOptionAsString();
        }

        return $this->description;
    }

}
