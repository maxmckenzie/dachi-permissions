<?php
namespace Dachi\Permissions;

use Dachi\Core\Database;
use Dachi\Core\Request;
use Dachi\Core\Template;
use Dachi\Core\Configuration;

/**
 * The Permissions class is responsable for providing an interface to user permissions
 *
 * @version   2.0.0
 * @since     2.0.0
 * @license   LICENCE.md
 * @author    LemonDigits.com <devteam@lemondigits.com>
 */
class Permissions {
  protected static $active_user_permissions = null;

  public static function load() {
    if(is_array(self::$active_user_permissions))
      return false;

    self::$active_user_permissions = array();

    if(Request::getSession("dachi_authenticated", false) == false)
      return;

    $active_user = Database::getRepository('Authentication:ModelUser')->findOneBy(array(
      "id" => Request::getSession("dachi_authenticated", false)
    ));
    if(!$active_user) return false;

    $role = $active_user->getRole();
    if(!$role) return false;

    foreach($role->getPermissions() as $perm)
      self::$active_user_permissions[$perm->getBit()] = true;

    $staff = Database::getRepository("Staff:ModelStaffMember")->getStaffFromUser($active_user);

    $output_user = array(
      "id"         => $active_user->getId(),
      "first_name" => $active_user->getFirstName(),
      "last_name"  => $active_user->getLastName(),
      "email"      => $active_user->getEmail(),
      "image"      => $staff->getImage(),
      "role"       => array(
        "id"   => $role->getId(),
        "name" => $role->getName()
      )
    );

    if(Configuration::get("authentication.identifier", "email") == "username")
      $output_user["username"] = $active_user->getUsername();

    Request::setData("active_user", $output_user);
    Request::setData("active_user_id", $active_user->getId());
    Request::setData("dachi_permissions", self::$active_user_permissions);

    return true;
  }

  public static function getActiveUser() {
    if(Request::getSession("dachi_authenticated", false) == false)
      return false;

    return Database::getRepository('Authentication:ModelUser')->findOneBy(array(
      "id" => Request::getSession("dachi_authenticated", false)
    ));
  }

  public static function has($bit) {
    if(!is_array(self::$active_user_permissions))
      self::load();

    if(isset(self::$active_user_permissions[$bit]) && self::$active_user_permissions[$bit] === true)
      return true;

    return false;
  }

  public static function hasUser($bit, $user) {
    $permissions = array();
    foreach($user->getRole()->getPermissions() as $perm)
      $permissions[$perm->getBit()] = true;

    return isset($permissions[$bit]) && $permissions[$bit] == true;
  }

  public static function enforce($bit) {
    if(!is_array(self::$active_user_permissions))
      self::load();

    if(isset(self::$active_user_permissions[$bit]) && self::$active_user_permissions[$bit] === true)
      return true;

    return self::fail();
  }

  public static function enforceUser($bit, $user) {
    $permissions = array();
    foreach($user->getRole()->getPermissions() as $perm)
      $permissions[$perm->getBit()] = true;

    if(isset($permissions[$bit]) && $permissions[$bit] == true)
      return true;

    return self::fail();
  }

  public static function fail() {
    Request::setResponseCode("error", "Insufficent permission");
    Template::redirect("/auth");
    return false;
  }

  public static function register($bit, $name = null, $description = null) {
    $permission = Database::getRepository('Authentication:ModelPermission')->findOneBy(array(
      "bit" => $bit
    ));

    $newPermission = false;
    if(!$permission) {
      $newPermission = true;
      $permission = new Authentication\ModelPermission();
    }

    $permission->setBit($bit);
    $permission->setName($name == null ? $bit : $name);
    $permission->setDescription($description == null ? $bit : $description);

    if($newPermission == true)
      Database::persist($permission);

    Database::flush();
  }
}
