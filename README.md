# Scheduler-InvoiceNinja
A module for InvoiceNinja for scheduling events via FullCalendar


Scheduler Module for InvoiceNinja
----------------------------------

Installation or Update:
Initial Installation or update from Scheduler v2.0.0 and later:

1.) Extract zip file directly into InvoiceNinja web directory.
    This will create a Modules/Scheduler directory and contents.

2.) Open a terminal in the InvoiceNinja web directory.

3.) Type "php artisan module:migrate Scheduler" and press "Enter".
    Answer "Yes" if prompted to continue.

4.) Type "php artisan module:publish scheduler" and press "Enter".
    Check that all permissions for Modules directory and contents are correct.

5.) Scheduler menu - Utilities - Settings
       Set options to your preference.
       
       If running the Workorders Addon for InvoiceNinja:
       Set Enable "Create Workorder" functionality to "Yes"

NOTE: Occasionally there may be a conflict with old views in the Laravel cache.

      If you receive an odd error on page load after install try and clear the cache by:
      (in browser address bar:)
            http://<YourFusionInvoice>/scheduler/viewclear

That should do it !
Description of functions is available in Scheduler -> Utilities -> About
