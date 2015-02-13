<?php
/**
 * Created by PhpStorm.
 * User: j-b
 * Date: 13/02/2015
 * Time: 19:33
 */

class Devices_Model extends Model {

    public function register($registerid) {
        $id = $this->createQuery()
            ->set(array('registerid'=>$registerid))->insert();
        return $id;
    }

    public function listRegisredDevices() {
        $devices = DB::select('SELECT d.registerid FROM devices d');
        return $devices;
    }

} 