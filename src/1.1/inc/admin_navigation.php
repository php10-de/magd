<?php
 /*?>echo '<ul>';
echo '<li><a href="admin_user_s.php" '.(($menu_item=="user")?"class='active'":"").' >User Administration</a></li>';
echo '<li><a href="changelog.php" '.(($menu_item=="changelog")?"class='active'":"").' >Changelog / 2DO </a></li>';
echo '<li><a href="changepw.php" '.(($menu_item=="password")?"class='active'":"").' >Password</a></li>';
echo '<li><a href="superadmin_general.php" '.(($menu_item=="general")?"class='active'":"").' >General</a></li>';
echo '</ul>';<?php */?>
<?php
echo '<ul>';
echo '<li><a href="admin_user_s.php" '.(($menu_item=="user")?"class='active'":"").' >User Administration</a></li>';
echo '<li><a href="userrights.php" '.(($menu_item=="userrights")?"class='active'":"").' >User Rights</a></li>';
echo '<li><a href="changelog.php" '.(($menu_item=="changelog")?"class='active'":"").' >Changelog / 2DO </a></li>';
echo '<li><a href="changepw.php" '.(($menu_item=="password")?"class='active'":"").' >Password</a></li>';
echo '<li><a href="superadmin_general.php" '.(($menu_item=="general")?"class='active'":"").' >General</a></li>';
echo '</ul>';
?>