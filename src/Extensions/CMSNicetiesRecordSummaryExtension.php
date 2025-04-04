<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBField;


class CMSNicetiesRecordSummaryExtension extends Extension
{

    private static $casting = [
        'RecordSummary' => 'HTMLFragment',
    ];

    public function updateSummaryFields(&$array)
    {
        array_unshift($array, ['RecordSummary' => 'ID']);
    }

    protected function getRecordSummary()
    {
        /** @var DBField $owner */
        $owner = $this->getOwner();
        $html = '<div class="record-summary">';
        $html .= '<span class="record-summary-id">' . $owner->ID . '</span>';
        //created
        $created = $owner->obj('Created');
        $html .= '<span class="record-summary-more">Created: ' . $created->Nice() . '(' . $created->Ago() . ')</span>';
        //last edited
        $lastEdited = $owner->obj('LastEdited');
        $html .= '<span class="record-summary-more">Last Edited: ' . $lastEdited->Nice() . '(' . $lastEdited->Ago() . ')</span>';
        // close
        $html .= '</div>';
        // prepare for output
        $html = DBField::create_field('HTMLText', $html);
        return $html;
    }
}
