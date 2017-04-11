<?php
namespace Dachi\Permissions\Authentication;

use Dachi\Core\Model;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity(repositoryClass="RepositoryUser")
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="type", type="string")
 * @Table(name="authentication_users", indexes={
 *   @Index(name="idx_name_search", columns={"first_name","last_name"})
 * })
 */
class ModelUser extends Model {
  /**
   * @Id @Column(type="integer") @GeneratedValue
   */
  protected $id;

  /**
   * @Column(type="string")
   */
  protected $first_name;

  /**
   * @Column(type="string")
   */
  protected $last_name;

  /**
   * @Column(type="string", nullable=true)
   */
  protected $username;

  /**
   * @Column(type="string")
   */
  protected $password;

  /**
   * @Column(type="string", nullable=true)
   */
  protected $email;

  /**
   * @ManyToOne(targetEntity="ModelRole", fetch="EAGER")
   */
  private $role;

  /**
   * @Column(type="datetime")
   */
  protected $created;

  /**
   * @Column(type="datetime")
   */
  protected $last_login;

  /**
   * @Column(type="string", length=64, nullable=true, options={"fixed"=true})
   */
  protected $reset_key;

  public function getId() {
    return $this->id;
  }

  public function getFirstName() {
    return $this->first_name;
  }

  public function setFirstName($first_name) {
    $this->first_name = $first_name;
  }

  public function getLastName() {
    return $this->last_name;
  }

  public function setLastName($last_name) {
    $this->last_name = $last_name;
  }

  public function getUsername() {
    return $this->username;
  }

  public function setUsername($username) {
    $this->username = $username;
  }

  public function getPassword() {
    return $this->password;
  }

  public function setPassword($password) {
    $this->password = $password;
  }

  public function getEmail() {
    return $this->email;
  }

  public function setEmail($email) {
    $this->email = $email;
  }

  public function getRole() {
    return $this->role;
  }

  public function setRole($role) {
    $this->role = $role;
  }

  public function getCreated() {
    return $this->created;
  }

  public function setCreated($datetime) {
    $this->created = $datetime;
  }

  public function getLastLogin() {
    return $this->last_login;
  }

  public function setLastLogin($datetime) {
    $this->last_login = $datetime;
  }

  public function setResetKey($key) {
    $this->reset_key = $key;
  }

  public function getResetKey() {
    return $this->reset_key;
  }

}
