<?php
// Quick script to refresh session permissions for admin user
// Run this from the ci4 directory: php ../refresh_permissions.php

// This is a workaround - the proper solution is to log out and log back in

echo "This script would refresh permissions, but the recommended solution is:\n\n";
echo "🔄 PLEASE LOG OUT AND LOG BACK IN to refresh your permissions.\n\n";
echo "Why? Your session has cached the old permissions. Logging out and back in will reload them.\n\n";
echo "Alternative: Clear your browser cookies/session for this site.\n";
