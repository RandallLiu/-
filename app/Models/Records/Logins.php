<?php namespace App\Models\Records;

use CodeIgniter\Model;

class Logins extends \App\Models\BaseModel {
    protected $table = 'admin_login_logs';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'login_time', 'source', 'phone_guid', 'unionid', 'openid', 'uid_code', 'ip_addr', 'ret_code', 'ret_msg', 'ua', 'mac_addr', 'channel', 'platform', 'device', 'device_name', 'version', 'os_version', 'net_type', 'resolution', 'crack_type', 'system', 'sim', 'token', 'verify_code', 'invitation_code'];
}