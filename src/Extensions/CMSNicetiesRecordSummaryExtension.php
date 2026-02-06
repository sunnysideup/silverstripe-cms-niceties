<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\FieldType\DBField;

/**
 * Class \Sunnysideup\CMSNiceties\Extensions\CMSNicetiesRecordSummaryExtension
 *
 * @property CMSNicetiesRecordSummaryExtension $owner
 */
class CMSNicetiesRecordSummaryExtension extends Extension
{
    private static $casting = [
        'RecordSummary' => 'HTMLFragment',
    ];

    public function updateSummaryFields(&$array)
    {
        $array = ['RecordSummary' => 'ID'] + $array;
    }

    public function getRecordSummary()
    {
        /** @var DBField $owner */
        $owner = $this->getOwner();
        $html = '<div class="record-summary">';
        $html .= '<div class="record-summary-id">' . $owner->ID . '</div>';
        $html .= '<div class="record-summary-dropdown">';
        //created
        $created = $owner->obj('Created');
        $html .= '<div class="record-summary-more">Created: ' . $created->Ago() . '</div>';
        //last edited
        $lastEdited = $owner->obj('LastEdited');
        $html .= '<div class="record-summary-more">Last Edited: ' . $lastEdited->Ago() . '</div>';
        // close
        $html .= '</div>';
        $html .= '</div>';
        // prepare for output
        $html = DBField::create_field('HTMLText', $html);
        return $html;
    }
}
