<?php

namespace Sunnysideup\CMSNiceties\Extensions;

use SilverStripe\Admin\LeftAndMain;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use SilverStripe\ORM\DB;
use SilverStripe\ORM\Queries\SQLSelect;
use SilverStripe\Security\Group;
use SilverStripe\Security\Permission;

/**
 * Adds two sortable summary fields to Group:
 * - PrivilegeCount: the number of different permission codes the group has,
 *   either granted directly or through one of its roles (a code that is in both counts once).
 *   Groups with the ADMIN permission (administrators) show "ALL" and sort as the highest number.
 *   Zero is shown as "-" (it still sorts as 0).
 * - RoleCount: the number of roles linked to the group (roles that have been deleted are not counted).
 *
 * In the CMS, administrator groups are listed first, as long as no other sort has been chosen.
 *
 * Both are calculated in the database (as sub-selects that are added to the group queries in the CMS)
 * so that the columns can be sorted in the GridField.
 *
 * @property Group|CMSNicetiesGroupExtension $owner
 * @property int|string $PrivilegeCount
 * @property int|string $RoleCount
 */
class CMSNicetiesGroupExtension extends Extension
{
    /**
     * The value that administrators get for sorting (higher than any real number of privileges).
     */
    private const ADMINISTRATOR_SORT_VALUE = 1000000;

    /**
     * Group queries only get the extra columns in the CMS (e.g. Security admin),
     * so the (small) sub-selects are not run for all other group queries.
     * Set to false to add them to all group queries.
     */
    private static bool $only_add_columns_in_cms = true;

    /**
     * Adds the two columns to the summary fields, right after the Members column
     * (or at the end if there is no Members column).
     */
    public function updateSummaryFields(&$fields)
    {
        $new = [
            'PrivilegeCount' => 'Privileges',
            'RoleCount' => 'Roles',
        ];
        $fields = array_diff_key($fields, $new);
        $result = [];
        $added = false;
        foreach ($fields as $name => $label) {
            $result[$name] = $label;
            if ('Members.Count' === $name) {
                $result += $new;
                $added = true;
            }
        }
        if (! $added) {
            $result += $new;
        }
        $fields = $result;
    }

    /**
     * When the query is created with its default sort, list administrators first.
     * Choosing another sort (e.g. clicking a column title) replaces the sort, including this part.
     */
    public function augmentDataQueryCreation(SQLSelect $query, $dataQuery = null)
    {
        if (! $this->addColumns()) {
            return;
        }
        $query->setOrderBy(['"IsAdministrator"' => 'DESC'] + $query->getOrderBy());
    }

    /**
     * Adds the count columns to the group query so that they can be displayed and sorted.
     */
    public function augmentSQL(SQLSelect $query, $dataQuery = null)
    {
        if (! $this->addColumns()) {
            return;
        }
        $query->selectField(self::administratorSql(), 'IsAdministrator');
        $query->selectField(self::privilegeCountSql(), 'PrivilegeCount');
        $query->selectField(self::roleCountSql(), 'RoleCount');
    }

    /**
     * @return int|string the number of privileges, "ALL" for administrators, or "-" for none
     */
    public function getPrivilegeCount(): int|string
    {
        $count = $this->getCount('PrivilegeCount', self::privilegeCountSql());
        if ($count >= self::ADMINISTRATOR_SORT_VALUE) {
            return 'ALL';
        }

        return $count > 0 ? $count : '-';
    }

    /**
     * @return int|string the number of roles, or "-" for none
     */
    public function getRoleCount(): int|string
    {
        $count = $this->getCount('RoleCount', self::roleCountSql());

        return $count > 0 ? $count : '-';
    }

    /**
     * SQL for 1 if the group has the ADMIN permission (directly or through a role), otherwise 0.
     * Refers to the "Group" table of the query it is used in.
     */
    protected static function administratorSql(): string
    {
        $grant = (int) Permission::GRANT_PERMISSION;

        return '(CASE WHEN'
            . ' EXISTS (SELECT 1 FROM "Permission"'
            . ' WHERE "Permission"."GroupID" = "Group"."ID"'
            . ' AND "Permission"."Type" = ' . $grant
            . ' AND "Permission"."Code" = \'ADMIN\')'
            . ' OR EXISTS (SELECT 1 FROM "PermissionRoleCode"'
            . ' INNER JOIN "Group_Roles" ON "Group_Roles"."PermissionRoleID" = "PermissionRoleCode"."RoleID"'
            . ' INNER JOIN "PermissionRole" ON "PermissionRole"."ID" = "Group_Roles"."PermissionRoleID"'
            . ' WHERE "Group_Roles"."GroupID" = "Group"."ID"'
            . ' AND "PermissionRoleCode"."Code" = \'ADMIN\')'
            . ' THEN 1 ELSE 0 END)';
    }

    /**
     * SQL for the number of different permission codes of a group (direct and from roles).
     * Administrators get a very high number so that they sort last (ascending) or first (descending).
     * Refers to the "Group" table of the query it is used in.
     */
    protected static function privilegeCountSql(): string
    {
        $grant = (int) Permission::GRANT_PERMISSION;

        return '(CASE WHEN ' . self::administratorSql() . ' = 1 THEN ' . self::ADMINISTRATOR_SORT_VALUE . ' ELSE ('
            . '(SELECT COUNT(DISTINCT "Permission"."Code") FROM "Permission"'
            . ' WHERE "Permission"."GroupID" = "Group"."ID"'
            . ' AND "Permission"."Type" = ' . $grant
            . ' AND "Permission"."Code" IS NOT NULL)'
            . ' + '
            . '(SELECT COUNT(DISTINCT "PermissionRoleCode"."Code") FROM "PermissionRoleCode"'
            . ' INNER JOIN "Group_Roles" ON "Group_Roles"."PermissionRoleID" = "PermissionRoleCode"."RoleID"'
            . ' INNER JOIN "PermissionRole" ON "PermissionRole"."ID" = "Group_Roles"."PermissionRoleID"'
            . ' WHERE "Group_Roles"."GroupID" = "Group"."ID"'
            . ' AND "PermissionRoleCode"."Code" IS NOT NULL'
            . ' AND "PermissionRoleCode"."Code" NOT IN ('
            . 'SELECT "DirectPermission"."Code" FROM "Permission" AS "DirectPermission"'
            . ' WHERE "DirectPermission"."GroupID" = "Group"."ID"'
            . ' AND "DirectPermission"."Type" = ' . $grant
            . ' AND "DirectPermission"."Code" IS NOT NULL))'
            . ') END)';
    }

    /**
     * SQL for the number of roles of a group.
     * Refers to the "Group" table of the query it is used in.
     */
    protected static function roleCountSql(): string
    {
        // only count roles that still exist: the link table can contain rows of roles that have been deleted.
        return '(SELECT COUNT(DISTINCT "Group_Roles"."PermissionRoleID") FROM "Group_Roles"'
            . ' INNER JOIN "PermissionRole" ON "PermissionRole"."ID" = "Group_Roles"."PermissionRoleID"'
            . ' WHERE "Group_Roles"."GroupID" = "Group"."ID")';
    }

    protected function addColumns(): bool
    {
        if (! Config::inst()->get(self::class, 'only_add_columns_in_cms')) {
            return true;
        }

        return Controller::has_curr() && Controller::curr() instanceof LeftAndMain;
    }

    /**
     * Uses the value from the query if it is there, otherwise asks the database
     * (e.g. when the group was loaded outside the CMS).
     */
    protected function getCount(string $field, string $sql): int
    {
        $owner = $this->getOwner();
        $value = $owner->getField($field);
        if (null !== $value) {
            return (int) $value;
        }
        if (! $owner->exists()) {
            return 0;
        }

        return (int) DB::prepared_query(
            'SELECT ' . $sql . ' FROM "Group" WHERE "Group"."ID" = ?',
            [$owner->ID]
        )->value();
    }
}
