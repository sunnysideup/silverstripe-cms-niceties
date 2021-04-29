/**
 * Side menu labels were dissappearing
 * when editing pages in the CMS. This bug was due to the 3rd party library
 * thinking the side menu was collapsed then failing to properly collapse the sidemenu
 * The bug is present in the vendor folder so this script sets specific cookies to trick the 3rd
 * party code into thinking the side menu is always pulled out
 *
 * @author Tristan Mastrodicasa
 */

jQuery.cookie("cms-menu-sticky", "true", {
  path: "/",
  expires: 31,
});

jQuery.cookie("cms-panel-collapsed-cms-menu", "false", {
  path: "/",
  expires: 31,
});

jQuery.cookie("cms-panel-collapsed-cms-content-tools-CMSMain", "false", {
  path: "/",
  expires: 31,
});
