<?php
/**
* @version 			SEBLOD Importer 1.x
* @package			SEBLOD Importer Add-on for SEBLOD 3.x
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>

CHANGELOG:

Legend:

* -> Security Fix
# -> Bug Fix
$ -> Language fix or change
+ -> Addition
^ -> Change
- -> Removed
! -> Note

@ID is the ID on SEBLOD Tracker.

-------------------- 1.7.0 Upgrade Release [25-May-2016] -----------------

+ Additional info/details for Columns/Fields implemented.
  >> "data_type" to force column creation to be INT(11), VARCHAR(255) or else
  >> "label", "type", "options" or other attributes can be overriden for field creation.

^ Default separator is now ";"

# Conditional States fixed.
# User Import issue fixed.

-------------------- 1.6.0 Upgrade Release [25-Apr-2016] -----------------

+ "onCckPreBeforeImport", "onCckPostBeforeImport" events added.
+ "onCckPreAfterImport", "onCckPostAfterImport" events added.

^ CHARSET updated to "utf8mb4" and DEFAULT COLLATE to "utf8mb4_unicode_ci".
^ "onCCK_Storage_LocationAfterImport" replaced by "onCCK_Storage_LocationAfterImports".

# Data Consistency issues fixed.

-------------------- 1.5.2 Upgrade Release [19-Dec-2015] -----------------

! Copyright Updated.
! Language constant updated for Updater Add-on.

-------------------- 1.5.1 Upgrade Release [7-Sep-2015] ------------------

! Language constant updated for Updater Add-on.

-------------------- 1.5.0 Upgrade Release [17-Jul-2015] -----------------

! Ability to manage Sessions >> SEBLOD 3.7.0 required. (!)

+ Session Manager button added in the toolbar.
+ Session Dropdown Menu updated.

# Javascript issues fixed.

-------------------- 1.4.0 Upgrade Release [7-May-2015] ------------------

! Joomla! 3.4 ready.

+ Update by Custom Key (custom field) added.

^ Implement JCckImporterVersion class.
^ SQL table storage engine switched from MyISAM to InnoDB in install.sql

# Force fieldnames to lowercase.

-------------------- 1.3.3 Upgrade Release [1-Dec-2014] ------------------

+ "CSV Length" added to SEBLOD Importer Options.

# CSV Length issue fixed.

-------------------- 1.3.2 Upgrade Release [29-Apr-2014] ------------------

! Download URL updated for SEBLOD 3.3.4

-------------------- 1.3.1 Upgrade Release [15-Apr-2014] ------------------

# cck.dev-3.2.0.min.js updated to cck.dev-3.3.0.min.js

-------------------- 1.3.0 Upgrade Release [24-Dec-2013] ------------------

! Joomla! 3.2 ready.
! Performance improvements and optimizations.

+ AJAX mode added.
+ "Force Password" parameter added. (Joomla! User)
+ "Reordering" parameter added. (Joomla! Article)

# Queries overloading issue fixed. (Joomla! Article)

-------------------- 1.2.0 Upgrade Release [12-Apr-2013] -------------

! Joomla! 3 ready.
+ Log added.
+ Sessions added.

+ Dynamic options (per Content Object) added.
+ Output options added on SEBLOD Importer configuration.

# Storages (#__cck_store_item..) fixed.
# Various improvements or issues fixed.

-------------------- 1.0.0 Initial Release [18-Jan-2012] ------------------

+ Initial Release